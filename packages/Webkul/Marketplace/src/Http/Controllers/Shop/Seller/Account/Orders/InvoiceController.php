<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Orders;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Response;
use Webkul\Sales\Models\Order;
use Webkul\Core\Traits\PDFHandler;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Models\Seller;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\InvoiceRepository;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
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
    public function print(int $id, Order $order)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        $seller = Seller::first();

        $html = view('marketplace::shop.sellers.account.orders.invoices.pdf', [
            'invoice' => $invoice,
            'seller' => $seller,
            'order'  => $order,

        ])->toArabicHTML();


        $pdf = app(PDF::class)
            ->loadHTML($html)
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }
}
