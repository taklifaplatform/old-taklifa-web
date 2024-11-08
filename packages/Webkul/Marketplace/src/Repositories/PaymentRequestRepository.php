<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\PaymentRequest;
use Webkul\Marketplace\Enum\Payout;

class PaymentRequestRepository extends Repository
{
    /**
     * Create a new repository instance.
     */
    public function __construct(
        App $app,
        protected OrderRepository $orderRepository
    ) {
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return PaymentRequest::class;
    }

    /**
     * Pay seller
     *
     * @return bool
     */
    public function paySeller(array $data)
    {
        $sellerOrder = $this->orderRepository->findOneWhere([
            'order_id'              => $data['order_id'],
            'marketplace_seller_id' => $data['seller_id'],
        ]);

        if (! $sellerOrder) {
            session()->flash('error', trans('marketplace::app.admin.orders.order-not-exist'));

            return;
        }

        $totalPaid = $this->scopeQuery(function ($query) use ($sellerOrder) {
            return $query->where('marketplace_transactions.marketplace_seller_id', $sellerOrder->marketplace_seller_id)
                ->where('marketplace_transactions.marketplace_order_id', $sellerOrder->id);
        })->sum('base_total');

        $amount = $sellerOrder->base_seller_total_invoiced - $totalPaid;

        if (! $amount) {
            session()->flash('error', trans('marketplace::app.admin.orders.no-amount-to-paid'));

            return;
        }

        Event::dispatch('marketplace.sales.transaction.create.before', $data);

        $transaction = $this->create([
            'type'                  => isset($data['type']) ? $data['type'] : 'manual',
            'method'                => isset($data['method']) ? $data['method'] : 'manual',
            'transaction_id'        => $data['order_id'].'-'.Str::random(10),
            'comment'               => $data['comment'],
            'base_total'            => $amount,
            'marketplace_order_id'  => $sellerOrder->id,
            'marketplace_seller_id' => $sellerOrder->marketplace_seller_id,
        ]);

        if (($amount + $totalPaid) == $sellerOrder->base_seller_total) {
            $this->orderRepository->update([
                'seller_payout_status' => Payout::STATUS_PAID->value,
            ], $sellerOrder->id);
        }

        Event::dispatch('marketplace.sales.transaction.create.after', $transaction);

        return $transaction;
    }
}
