<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Orders;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Traits\PDFHandler;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\InvoiceRepository;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository as BaseInvoiceRepository;

class InvoiceController extends Controller
{
    use PDFHandler;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected BaseInvoiceRepository $baseInvoiceRepository,
        protected InvoiceRepository $invoiceRepository,
        protected OrderRepository $orderRepository
    ) {}

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(int $orderId)
    {
        $sellerOrder = $this->orderRepository->findOneWhere([
            'order_id'              => $orderId,
            'marketplace_seller_id' => auth()->guard('seller')->user()->seller_id,
        ]);

        if (! $sellerOrder->canInvoice()) {
            session()->flash('error', trans('marketplace::app.shop.sellers.account.orders.invoices.permission-error'));

            return Back();
        }

        $this->validate(request(), [
            'invoice.items.*' => 'required|numeric|min:0',
        ]);

        $data = request()->input();

        $haveProductToInvoice = false;

        foreach ($data['invoice']['items'] as $qty) {
            if ($qty) {
                $haveProductToInvoice = true;

                break;
            }
        }

        if (! $haveProductToInvoice) {
            session()->flash('error', trans('marketplace::app.shop.sellers.account.orders.invoices.qty-error'));

            return Back();
        }

        Event::dispatch('marketplace.seller.account.orders.invoice.create.before', $orderId);

        $invoice = $this->baseInvoiceRepository->create(array_merge($data, ['order_id' => $orderId]));

        Event::dispatch('marketplace.seller.account.orders.invoice.create.after', $invoice);

        session()->flash('success', trans('marketplace::app.shop.sellers.account.orders.invoices.invoice-success'));

        return Back();
    }

    /**
     * Print and download the for the specified resource.
     *
     * @return Response
     */
    public function print(int $id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        return $this->downloadPDF(
            view('marketplace::shop.sellers.account.orders.invoices.pdf', compact('invoice'))->render(),
            'invoice-'.$invoice->created_at->format('d-m-Y').'.pdf'
        );
    }
}
