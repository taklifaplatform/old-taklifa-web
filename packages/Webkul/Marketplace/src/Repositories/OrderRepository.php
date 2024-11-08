<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Eloquent\Repository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Marketplace\Contracts\Order as OrderContract;
use Webkul\Marketplace\Enum\Order;
use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;
use Webkul\Sales\Repositories\OrderItemRepository as BaseOrderItemRepository;
use Webkul\Sales\Repositories\OrderRepository as BaseOrderRepository;

class OrderRepository extends Repository
{
    public function __construct(
        App $app,
        protected BaseOrderItemRepository $baseOrderItemRepository,
        protected BaseOrderRepository $baseOrderRepository,
        protected BaseProductRepository $baseProductRepository,
        protected CustomerRepository $customerRepository,
        protected OrderItemRepository $orderItemRepository,
        protected ProductRepository $productRepository,
        protected SellerRepository $sellerRepository
    ) {
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return OrderContract::class;
    }

    /**
     * @return mixed
     */
    public function create(array $data)
    {
        $order = $data['order'];

        Event::dispatch('marketplace.sales.order.save.before', $data);

        $sellerOrders = [];

        $commissionPercentage = core()->getConfigData('marketplace.settings.general.commission_per_unit');

        foreach ($order->items()->get() as $item) {
            $itemAdditional = $item->additional;

            if (isset($itemAdditional['seller_info'])
                && ! $itemAdditional['seller_info']['is_owner']
            ) {
                $seller = $this->sellerRepository->find($item->additional['seller_info']['seller_id']);
            } else {
                $seller = $this->productRepository->getSellerByProductId($item->product_id);
            }

            if (! isset($seller)
                || ! $seller->is_approved
            ) {
                continue;
            }

            if ($item->product->type == 'configurable') {
                $sellerProduct = $this->productRepository->findOneWhere([
                    'product_id'            => $item->additional['selected_configurable_option'],
                    'marketplace_seller_id' => $seller->seller_id,
                ]);
            } else {
                $sellerProduct = $this->productRepository->findOneWhere([
                    'product_id'            => $item->product->id,
                    'marketplace_seller_id' => $seller->seller_id,
                ]);
            }

            if (! $sellerProduct->is_approved) {
                continue;
            }

            if (! $sellerProduct->haveSufficientQuantity($item->additional['quantity'])
                && ! in_array($sellerProduct->product->type, ['downloadable', 'booking', 'bundle'])
            ) {
                continue;
            }

            if ($seller->commission_enable) {
                $commissionPercentage = $seller->commission_percentage;
            }

            $sellerOrder = $this->findOneWhere([
                'order_id'              => $order->id,
                'marketplace_seller_id' => $seller->seller_id,
            ]);

            if (! $sellerOrder) {
                $sellerOrders[] = $sellerOrder = parent::create([
                    'status'                  => Order::STATUS_PENDING->value,
                    'seller_payout_status'    => Order::STATUS_PENDING->value,
                    'order_id'                => $order->id,
                    'marketplace_seller_id'   => $seller->seller_id,
                    'commission_percentage'   => $commissionPercentage,
                    'is_withdrawal_requested' => 0,
                    'shipping_amount'         => $order->shipping_amount,
                    'base_shipping_amount'    => $order->base_shipping_amount,
                ]);
            }

            $commission = $baseCommission = 0;
            $sellerTotal = $baseSellerTotal = 0;

            if (isset($commissionPercentage)) {
                $commission = ($item->total * $commissionPercentage) / 100;
                $baseCommission = ($item->base_total * $commissionPercentage) / 100;

                $sellerTotal = $item->total - $commission;
                $baseSellerTotal = $item->base_total - $baseCommission;
            }

            $sellerOrderItem = $this->orderItemRepository->create([
                'marketplace_product_id' => $sellerProduct->id,
                'marketplace_order_id'   => $sellerOrder->id,
                'order_item_id'          => $item->id,
                'commission'             => $commission,
                'base_commission'        => $baseCommission,
                'seller_total'           => $sellerTotal + $item->tax_amount - $item->discount_amount,
                'base_seller_total'      => $baseSellerTotal + $item->base_tax_amount - $item->base_discount_amount,
            ]);

            if ($childItem = $item->child) {
                $childSellerProduct = $this->productRepository->findOneWhere([
                    'product_id'            => $childItem->product->id,
                    'marketplace_seller_id' => $seller->seller_id,
                ]);

                $this->orderItemRepository->create([
                    'marketplace_product_id' => $childSellerProduct->id,
                    'marketplace_order_id'   => $sellerOrder->id,
                    'order_item_id'          => $childItem->id,
                    'parent_id'              => $sellerOrderItem->id,
                ]);
            }
        }

        foreach ($sellerOrders as $order) {
            $this->collectTotals($order);

            Event::dispatch('marketplace.sales.order.save.after', $order);
        }

        session()->forget('marketplace_shipping_rates');
    }

    /**
     * @return mixed
     */
    public function cancel(array $data)
    {
        $order = $data['order'];

        $sellerOrders = $this->findWhere(['order_id' => $order->id]);

        foreach ($sellerOrders as $sellerOrder) {
            Event::dispatch('marketplace.sales.order.cancel.before', $sellerOrder);

            $this->updateOrderStatus($sellerOrder);

            Event::dispatch('marketplace.sales.order.cancel.after', $sellerOrder);
        }
    }

    /**
     * @param  int  $orderId
     * @param  object  $seller
     * @return mixed
     */
    public function sellerCancelOrder($orderId, $seller)
    {
        $sellerOrders = $this->findWhere([
            'order_id'              => $orderId,
            'marketplace_seller_id' => $seller->seller_id,
        ]);

        foreach ($sellerOrders as $sellerOrder) {
            if (! $sellerOrder->canCancel()) {
                return false;
            }

            Event::dispatch('marketplace.sales.order.cancel.before', $sellerOrder);

            foreach ($sellerOrder->items as $item) {
                if ($item->item->qty_to_cancel) {
                    if ($item->product) {
                        $this->baseOrderItemRepository->returnQtyToProductInventory($item->item);
                    }

                    $item->item->qty_canceled += $item->item->qty_to_cancel;

                    $item->item->save();
                }
            }

            $this->updateOrderStatus($sellerOrder);

            if ($this->baseOrderRepository->isInCanceledState($sellerOrder->order)) {
                $sellerOrder->order->update(['status' => 'canceled']);
            }

            Event::dispatch('marketplace.sales.order.cancel.after', $sellerOrder);

            return true;
        }
    }

    /**
     * @param  mixed  $order
     * @return void
     */
    public function isInCompletedState($order)
    {
        $totalQtyOrdered = $totalQtyInvoiced = $totalQtyShipped = $totalQtyRefunded = $totalQtyCanceled = 0;

        foreach ($order->items as $sellerOrderItem) {
            $totalQtyOrdered += $sellerOrderItem->item->qty_ordered;
            $totalQtyInvoiced += $sellerOrderItem->item->qty_invoiced;

            if (! $sellerOrderItem->item->isStockable()) {
                $totalQtyShipped += $sellerOrderItem->item->qty_invoiced;
            } else {
                $totalQtyShipped += $sellerOrderItem->item->qty_shipped;
            }

            $totalQtyRefunded += $sellerOrderItem->item->qty_refunded;
            $totalQtyCanceled += $sellerOrderItem->item->qty_canceled;
        }

        if ($totalQtyOrdered != ($totalQtyRefunded + $totalQtyCanceled)
            && $totalQtyOrdered == $totalQtyInvoiced + $totalQtyRefunded + $totalQtyCanceled
            && $totalQtyOrdered == $totalQtyShipped + $totalQtyRefunded + $totalQtyCanceled
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param  mixed  $order
     * @return void
     */
    public function isInCanceledState($order)
    {
        $totalQtyOrdered = $totalQtyCanceled = 0;

        foreach ($order->items as $sellerOrderItem) {
            $totalQtyOrdered += $sellerOrderItem->item->qty_ordered;
            $totalQtyCanceled += $sellerOrderItem->item->qty_canceled;
        }

        if ($totalQtyOrdered == $totalQtyCanceled) {
            return true;
        }

        return false;
    }

    /**
     * @param  mixed  $order
     * @return void
     */
    public function isInClosedState($order)
    {
        $totalQtyOrdered = $totalQtyRefunded = $totalQtyCanceled = 0;

        foreach ($order->items as $sellerOrderItem) {
            $totalQtyOrdered += $sellerOrderItem->item->qty_ordered;
            $totalQtyRefunded += $sellerOrderItem->item->qty_refunded;
            $totalQtyCanceled += $sellerOrderItem->item->qty_canceled;
        }

        if ($totalQtyOrdered == $totalQtyRefunded + $totalQtyCanceled) {
            return true;
        }

        return false;
    }

    /**
     * @param  mixed  $order
     * @return void
     */
    public function updateOrderStatus($order)
    {
        $status = Order::STATUS_PROCESSING->value;

        if ($this->isInCompletedState($order)) {
            $status = Order::STATUS_COMPLETED->value;
        }

        if ($this->isInCanceledState($order)) {
            $status = Order::STATUS_CANCELED->value;
        } elseif ($this->isInClosedState($order)) {
            $status = Order::STATUS_CLOSED->value;
        }

        $order->status = $status;

        $order->save();
    }

    /**
     * Updates marketplace order totals
     *
     * @param  Order  $order
     * @return void
     */
    public function collectTotals($order)
    {
        $order->grand_total = $order->base_grand_total = 0;
        $order->sub_total = $order->base_sub_total = 0;
        $order->tax_amount = $order->base_tax_amount = 0;
        $order->sub_total_incl_tax = $order->base_sub_total_incl_tax = 0;
        $order->discount_amount_invoiced = $order->base_discount_amount_invoiced = 0;
        $order->commission = $order->base_commission = 0;
        $order->seller_total = $order->base_seller_total = 0;
        $order->total_item_count = $order->total_qty_ordered = 0;
        $order->discount_amount = $order->base_discount_amount = 0;

        $shippingCodes = explode('_', $order->order->shipping_method);
        $carrier = current($shippingCodes);
        $shippingMethod = end($shippingCodes);

        $marketplaceShippingRates = session()->get('marketplace_shipping_rates');

        if (isset($marketplaceShippingRates[$carrier])
            && isset($marketplaceShippingRates[$carrier][$shippingMethod])
            && isset($marketplaceShippingRates[$carrier][$shippingMethod][$order->marketplace_seller_id])
        ) {
            $sellerShippingRate = $marketplaceShippingRates[$carrier][$shippingMethod][$order->marketplace_seller_id];

            $order->shipping_amount = $sellerShippingRate['amount'];
            $order->base_shipping_amount = $sellerShippingRate['base_amount'];
        }

        foreach ($order->items()->get() as $sellerOrderItem) {
            $item = $sellerOrderItem->item;
            $order->discount_amount += $item->discount_amount;
            $order->base_discount_amount += $item->base_discount_amount;
            $order->grand_total += $item->total + $item->tax_amount - $item->discount_amount;

            $order->base_grand_total += $item->base_total + $item->base_tax_amount - $item->base_discount_amount;

            $order->sub_total += $item->total;
            $order->base_sub_total += $item->base_total;

            $order->tax_amount += $item->tax_amount;
            $order->base_tax_amount += $item->base_tax_amount;

            $order->sub_total_incl_tax += $item->tax_amount + $item->total;
            $order->base_sub_total_incl_tax += $item->base_tax_amount + $item->base_total;

            $order->commission += $sellerOrderItem->commission;
            $order->base_commission += $sellerOrderItem->base_commission;

            $order->seller_total += $sellerOrderItem->seller_total;
            $order->base_seller_total += $sellerOrderItem->base_seller_total;

            $order->total_qty_ordered += $item->qty_ordered;

            $order->total_item_count += 1;
        }

        if ($order->shipping_amount > 0) {
            $order->grand_total += $order->shipping_amount;
            $order->base_grand_total += $order->base_shipping_amount;
        }

        $order->sub_total_invoiced = $order->base_sub_total_invoiced = 0;
        $order->shipping_invoiced = $order->base_shipping_invoiced = 0;
        $order->commission_invoiced = $order->base_commission_invoiced = 0;
        $order->seller_total_invoiced = $order->base_seller_total_invoiced = 0;
        $order->base_grand_total_invoiced = $order->grand_total_invoiced = 0;
        $order->base_tax_amount_invoiced = $order->tax_amount_invoiced = 0;

        foreach ($order->invoices as $invoice) {
            $order->sub_total_invoiced += $invoice->sub_total;
            $order->base_sub_total_invoiced += $invoice->base_sub_total;

            $order->shipping_invoiced += $invoice->shipping_amount;
            $order->base_shipping_invoiced += $invoice->base_shipping_amount;

            $order->tax_amount_invoiced += $invoice->tax_amount;
            $order->base_tax_amount_invoiced += $invoice->base_tax_amount;

            $order->discount_amount_invoiced += $invoice->discount_amount;
            $order->base_discount_amount_invoiced += $invoice->base_discount_amount;

            $order->commission_invoiced += $commissionInvoiced = ($invoice->sub_total * $order->commission_percentage) / 100;
            $order->base_commission_invoiced += $baseCommissionInvoiced = ($invoice->base_sub_total * $order->commission_percentage) / 100;

            $order->seller_total_invoiced += $invoice->sub_total - $commissionInvoiced - $invoice->discount_amount + $invoice->tax_amount;
            $order->base_seller_total_invoiced += $invoice->base_sub_total - $baseCommissionInvoiced - $invoice->base_discount_amount + $invoice->base_tax_amount;
        }

        $order->grand_total_invoiced = $order->sub_total_invoiced + $order->shipping_invoiced + $order->tax_amount_invoiced - $order->discount_amount_invoiced;
        $order->base_grand_total_invoiced = $order->base_sub_total_invoiced + $order->base_shipping_invoiced + $order->base_tax_amount_invoiced - $order->base_discount_amount_invoiced;

        $order->sub_total_refunded = $order->base_sub_total_refunded = 0;
        $order->shipping_refunded = $order->base_shipping_refunded = 0;
        $order->tax_amount_refunded = $order->base_tax_amount_refunded = 0;
        $order->discount_refunded = $order->base_discount_refunded = 0;
        $order->grand_total_refunded = $order->base_grand_total_refunded = 0;

        foreach ($order->refunds as $refund) {
            $order->sub_total_refunded += $refund->sub_total;
            $order->base_sub_total_refunded += $refund->base_sub_total;

            $order->shipping_refunded += $refund->shipping_amount;
            $order->base_shipping_refunded += $refund->base_shipping_amount;

            $order->tax_amount_refunded += $refund->tax_amount;
            $order->base_tax_amount_refunded += $refund->base_tax_amount;

            $order->discount_refunded += $refund->discount_amount;
            $order->base_discount_refunded += $refund->base_discount_amount;

            $order->grand_total_refunded += $refund->adjustment_refund - $refund->adjustment_fee;
            $order->base_grand_total_refunded += $refund->base_adjustment_refund - $refund->base_adjustment_fee;
        }

        $order->grand_total_refunded += $order->sub_total_refunded + $order->shipping_refunded + $order->tax_amount_refunded - $order->discount_refunded;

        $order->base_grand_total_refunded += $order->base_sub_total_refunded + $order->base_shipping_refunded + $order->base_tax_amount_refunded - $order->base_discount_refunded;

        $order->save();

        return $order;
    }

    /**
     * Search Orders.
     *
     * @param  object  $seller
     * @return object
     */
    public function searchOrders($seller)
    {
        return $this->scopeQuery(function ($query) {
            return $query
                ->leftJoin('orders', 'orders.id', 'marketplace_orders.order_id')
                ->where(function ($query) {
                    $query->where('customer_email', 'like', '%'.urldecode(request()->input('query')).'%')
                        ->orWhere('orders.status', 'like', '%'.urldecode(request()->input('query')).'%')
                        ->orWhere(DB::raw('CONCAT('.DB::getTablePrefix().'customer_first_name, " ", '.DB::getTablePrefix().'customer_last_name)'), 'like', '%'.urldecode(request()->input('query')).'%')
                        ->orWhere('increment_id', request()->input('query'));
                })
                ->orderBy('orders.created_at', 'desc');
        })
            ->where('marketplace_seller_id', $seller->seller_id)
            ->paginate(10);
    }

    /**
     * Search Customers.
     *
     * @param  object  $seller
     * @return object
     */
    public function searchCustomers($seller)
    {
        return $this->scopeQuery(function ($query) {
            return $query->leftJoin('orders', 'orders.id', '=', 'marketplace_orders.order_id')
                ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
                ->groupBy('customers.id')
                ->orderBy('customers.created_at', 'desc')
                ->select('customers.email')
                ->addSelect(DB::raw('CONCAT('.DB::getTablePrefix().'first_name, " ", '.DB::getTablePrefix().'last_name) as full_name'))
                ->where(function ($query) {
                    $query->where('customers.email', 'like', '%'.urldecode(request()->input('query')).'%')
                        ->orWhere(DB::raw('CONCAT('.DB::getTablePrefix().'customers.first_name, " ", '.DB::getTablePrefix().'customers.last_name)'), 'like', '%'.urldecode(request()->input('query')).'%');
                });
        })
            ->where('marketplace_seller_id', $seller->seller_id)
            ->paginate(10);
    }
}
