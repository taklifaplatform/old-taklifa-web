<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\Refund;

class RefundRepository extends Repository
{
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected ProductRepository $productRepository,
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
        protected InvoiceItemRepository $invoiceItemRepository,
        protected RefundItemRepository $refundItemRepository,
        App $app
    ) {
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Refund::class;
    }

    /**
     * @param  object  $refund
     * @return void
     */
    public function create($refund)
    {
        Event::dispatch('marketplace.sales.refund.save.before', $refund);

        $sellerRefunds = [];

        foreach ($refund->items()->get() as $item) {
            if (isset($item->additional['seller_info'])
                && ! $item->additional['seller_info']['is_owner']
            ) {
                $seller = $this->sellerRepository->find($item->additional['seller_info']['seller_id']);
            } else {
                $seller = $this->productRepository->getSellerByProductId($item->product_id);
            }

            if (! $seller) {
                continue;
            }

            // Ignore duplicate sellers in array
            if (! in_array($seller->seller_id, data_get($sellers ?? [], '*.id'))) {
                $sellers[] = $seller;
            }

            $sellerOrder = $this->orderRepository->findOneWhere([
                'order_id'              => $refund->order->id,
                'marketplace_seller_id' => $seller->seller_id,
            ]);

            if (! $sellerOrder) {
                continue;
            }

            $sellerOrderItem = $this->orderItemRepository->findOneWhere([
                'marketplace_order_id' => $sellerOrder->id,
                'order_item_id'        => $item->order_item->id,
            ]);

            if (! $sellerOrderItem) {
                continue;
            }

            $sellerRefund = $this->findOneWhere([
                'refund_id'            => $refund->id,
                'marketplace_order_id' => $sellerOrder->id,
            ]);

            if (! $sellerRefund) {
                $sellerRefunds[] = $sellerRefund = parent::create([
                    'total_qty'              => $item->qty,
                    'state'                  => 'refunded',
                    'refund_id'              => $refund->id,
                    'marketplace_order_id'   => $sellerOrder->id,
                    'adjustment_refund'      => core()->convertPrice($refund['adjustment_refund'], $sellerOrder->order_currency_code),
                    'base_adjustment_refund' => $refund['adjustment_refund'],
                    'adjustment_fee'         => core()->convertPrice($refund['adjustment_fee'], $sellerOrder->order_currency_code),
                    'base_adjustment_fee'    => $refund['adjustment_fee'],
                    'shipping_amount'        => core()->convertPrice($refund['shipping_amount'], $sellerOrder->order_currency_code),
                    'base_shipping_amount'   => $refund['shipping_amount'],
                ]);
            } else {
                $sellerRefund->total_qty += $item->qty;

                $sellerRefund->save();
            }

            $this->refundItemRepository->create([
                'marketplace_refund_id' => $sellerRefund->id,
                'refund_item_id'        => $item->id,
            ]);

            $this->orderItemRepository->collectTotals($sellerOrderItem);
        }

        foreach ($sellerRefunds as $sellerRefund) {
            $this->collectTotals($sellerRefund);
        }

        foreach ($sellers ?? [] as $seller) {
            $orders = $this->orderRepository->findWhere([
                'order_id'              => $refund->order->id,
                'marketplace_seller_id' => $seller->seller_id,
            ]);

            foreach ($orders as $order) {
                $this->orderRepository->collectTotals($order);

                $this->orderRepository->updateOrderStatus($order);

                $this->orderRepository->update(['seller_payout_status' => 'refunded'], $order->id);
            }
        }

        foreach ($sellerRefunds as $sellerRefund) {
            Event::dispatch('marketplace.sales.refund.save.after', $sellerRefund);
        }
    }

    /**
     * @param  mixed  $sellerRefund
     * @return mixed
     */
    public function collectTotals($sellerRefund)
    {
        $sellerRefund->sub_total = $sellerRefund->base_sub_total = 0;
        $sellerRefund->tax_amount = $sellerRefund->base_tax_amount = 0;
        $sellerRefund->sub_total_incl_tax = $sellerRefund->base_sub_total_incl_tax = 0;
        $sellerRefund->grand_total = $sellerRefund->base_grand_total = 0;
        $sellerRefund->discount_amount = $sellerRefund->base_discount_amount = 0;

        foreach ($sellerRefund->items as $sellerRefundItem) {
            $sellerRefund->sub_total += $sellerRefundItem->item->total;
            $sellerRefund->base_sub_total += $sellerRefundItem->item->base_total;

            $sellerRefund->tax_amount += $sellerRefundItem->item->tax_amount;
            $sellerRefund->base_tax_amount += $sellerRefundItem->item->base_tax_amount;

            $sellerRefund->sub_total_incl_tax += $sellerRefundItem->item->tax_amount + $sellerRefundItem->item->total;
            $sellerRefund->base_sub_total_incl_tax += $sellerRefundItem->item->base_tax_amount + $sellerRefundItem->item->base_total;

            $sellerRefund->discount_amount += $sellerRefundItem->item->discount_amount;
            $sellerRefund->base_discount_amount += $sellerRefundItem->item->base_discount_amount;
        }

        $sellerRefund->grand_total = $sellerRefund->sub_total + $sellerRefund->tax_amount + $sellerRefund->shipping_amount + $sellerRefund->adjustment_refund - $sellerRefund->adjustment_fee - $sellerRefund->discount_amount;

        $sellerRefund->base_grand_total = $sellerRefund->base_sub_total + $sellerRefund->base_tax_amount + $sellerRefund->base_shipping_amount + $sellerRefund->base_adjustment_refund - $sellerRefund->base_adjustment_fee - $sellerRefund->base_discount_amount;

        $sellerRefund->save();

        return $sellerRefund;
    }
}
