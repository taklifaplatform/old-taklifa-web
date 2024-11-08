<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\Invoice;

class InvoiceRepository extends Repository
{
    public function __construct(
        App $app,
        protected InvoiceItemRepository $invoiceItemRepository,
        protected OrderItemRepository $orderItemRepository,
        protected OrderRepository $orderRepository,
        protected ProductRepository $productRepository,
        protected SellerRepository $sellerRepository,
    ) {
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Invoice::class;
    }

    /**
     * Create invoice.
     */
    public function create(array $data)
    {
        $invoice = $data['invoice'];

        Event::dispatch('marketplace.sales.invoice.save.before', $data);

        $sellerInvoices = [];

        foreach ($invoice->items()->get() as $item) {
            if (isset($item->additional['seller_info'])
                && ! $item->additional['seller_info']['is_owner']
            ) {
                $seller = $this->sellerRepository->find($item->additional['seller_info']['seller_id']);

                $sellers[] = $this->sellerRepository->find($item->additional['seller_info']['seller_id']);
            } else {
                $seller = $this->productRepository->getSellerByProductId($item->product_id);

                $sellers[] = $this->productRepository->getSellerByProductId($item->product_id);
            }

            if (! $seller) {
                continue;
            }

            $sellerOrder = $this->orderRepository->findOneWhere([
                'order_id'              => $invoice->order->id,
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

            $sellerInvoice = $this->findOneWhere([
                'invoice_id'            => $invoice->id,
                'marketplace_order_id'  => $sellerOrder->id,
            ]);

            if (! $sellerInvoice) {
                $sellerInvoices[] = $sellerInvoice = parent::create([
                    'total_qty'            => $item->qty,
                    'state'                => 'paid',
                    'invoice_id'           => $invoice->id,
                    'marketplace_order_id' => $sellerOrder->id,
                ]);
            } else {
                $sellerInvoice->total_qty += $item->qty;

                $sellerInvoice->save();
            }

            $this->invoiceItemRepository->create([
                'marketplace_invoice_id' => $sellerInvoice->id,
                'invoice_item_id'        => $item->id,
            ]);

            $this->orderItemRepository->collectTotals($sellerOrderItem);
        }

        foreach ($sellerInvoices as $sellerInvoice) {
            $this->collectTotals($sellerInvoice);
        }

        foreach ($sellers as $seller) {
            if ($seller) {
                foreach ($this->orderRepository->findWhere(['order_id' => $invoice->order->id, 'marketplace_seller_id' => $seller->seller_id]) as $order) {
                    $this->orderRepository->collectTotals($order);
                    $this->orderRepository->updateOrderStatus($order);
                }
            }
        }

        foreach ($sellerInvoices as $sellerInvoice) {
            Event::dispatch('marketplace.sales.invoice.save.after', $sellerInvoice);
        }
    }

    /**
     * Collect totals.
     *
     * @param  mixed  $sellerInvoice
     */
    public function collectTotals($sellerInvoice)
    {
        $sellerInvoice->sub_total = $sellerInvoice->base_sub_total = 0;
        $sellerInvoice->tax_amount = $sellerInvoice->base_tax_amount = 0;
        $sellerInvoice->sub_total_incl_tax = $sellerInvoice->base_sub_total_incl_tax = 0;
        $sellerInvoice->shipping_amount = $sellerInvoice->base_shipping_amount = 0;
        $sellerInvoice->grand_total = $sellerInvoice->base_grand_total = 0;
        $sellerInvoice->discount_amount = $sellerInvoice->base_discount_amount = 0;

        foreach ($sellerInvoice->items as $sellerInvoiceItem) {
            $sellerInvoice->sub_total += $sellerInvoiceItem->item->total;
            $sellerInvoice->base_sub_total += $sellerInvoiceItem->item->base_total;

            $sellerInvoice->tax_amount += $sellerInvoiceItem->item->tax_amount;
            $sellerInvoice->base_tax_amount += $sellerInvoiceItem->item->base_tax_amount;

            $sellerInvoice->sub_total_incl_tax += $sellerInvoiceItem->item->tax_amount + $sellerInvoiceItem->item->total;
            $sellerInvoice->base_sub_total_incl_tax += $sellerInvoiceItem->item->base_tax_amount + $sellerInvoiceItem->item->base_total;

            $sellerInvoice->discount_amount += $sellerInvoiceItem->item->discount_amount;
            $sellerInvoice->base_discount_amount += $sellerInvoiceItem->item->base_discount_amount;
        }

        $sellerInvoice->shipping_amount = $sellerInvoice->order->shipping_amount;
        $sellerInvoice->base_shipping_amount = $sellerInvoice->order->base_shipping_amount;

        if ($sellerInvoice->order->shipping_amount) {
            foreach ($sellerInvoice->order->invoices as $prevInvoice) {
                if ((float) $prevInvoice->shipping_amount) {
                    $sellerInvoice->base_shipping_amount = $sellerInvoice->shipping_amount = 0;
                }
            }
        }

        $sellerInvoice->grand_total = $sellerInvoice->sub_total + $sellerInvoice->tax_amount + $sellerInvoice->shipping_amount - $sellerInvoice->discount_amount;
        $sellerInvoice->base_grand_total = $sellerInvoice->base_sub_total + $sellerInvoice->base_tax_amount + $sellerInvoice->base_shipping_amount - $sellerInvoice->base_discount_amount;

        $sellerInvoice->save();

        return $sellerInvoice;
    }
}
