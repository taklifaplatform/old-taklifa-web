<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\DataGrids\Admin\PaymentRequestDataGrid;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Mail\PaymentRequestCompleteNotification;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\TransactionRepository;

class PaymentRequestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected OrderRepository $orderRepository,
        protected TransactionRepository $transactionRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(PaymentRequestDataGrid::class)->process();
        }

        return view('marketplace::admin.payment-request.index');
    }

    /**
     * Update the order for payment and sends mails to admin.
     */
    public function pay(): JsonResponse
    {
        $this->validate(request(), [
            'order_id'  => 'required',
            'seller_id' => 'required',
        ]);

        $seller = $this->sellerRepository->find(request()->input('seller_id'));

        $order = $this->orderRepository->findOneWhere([
            'order_id'              => request()->input('order_id'),
            'marketplace_seller_id' => request()->input('seller_id'),
        ]);

        if (! $order) {
            return new JsonResponse([
                'message' => trans('marketplace::app.admin.payment-request.index.order-not-found'),
            ]);
        }

        Event::dispatch('marketplace.sales.transaction.create.before', $data = request()->input());

        $transaction = $this->transactionRepository->paySeller(request()->input());

        Event::dispatch('marketplace.sales.transaction.create.after', $transaction);

        try {
            Mail::queue(new PaymentRequestCompleteNotification($order, $seller));
        } catch (\Exception $e) {
            report($e);
        }

        return new JsonResponse([
            'message' => trans('marketplace::app.admin.payment-request.index.payment-success'),
        ]);
    }
}
