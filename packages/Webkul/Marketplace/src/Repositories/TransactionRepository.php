<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\Transaction;
use Webkul\Marketplace\Enum\Payout;

class TransactionRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Transaction::class;
    }

    /**
     * Pay seller
     *
     * @return bool
     */
    public function paySeller(array $data)
    {
        $sellerOrder = app(OrderRepository::class)->findOneWhere([
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

        $transaction = $this->create([
            'type'                  => isset($data['type']) ? $data['type'] : 'Manual',
            'method'                => isset($data['method']) ? $data['method'] : 'Manual',
            'transaction_id'        => $data['order_id'].'-'.Str::random(10),
            'comment'               => $data['comment'],
            'base_total'            => $amount,
            'marketplace_order_id'  => $sellerOrder->id,
            'marketplace_seller_id' => $sellerOrder->marketplace_seller_id,
        ]);

        if (($amount + $totalPaid) == $sellerOrder->base_seller_total) {
            app(OrderRepository::class)->update([
                'seller_payout_status' => Payout::STATUS_PAID->value,
            ], $sellerOrder->id);
        }

        return $transaction;
    }

    /**
     * Fet Transaction statistics
     */
    public function statistics(object $seller): array
    {
        return [
            'total_sale' => app(OrderRepository::class)->scopeQuery(function ($query) use ($seller) {
                return $query->where('marketplace_orders.marketplace_seller_id', $seller->seller_id);
            })->sum(DB::raw('base_grand_total_invoiced - base_shipping_invoiced - base_grand_total_refunded + base_shipping_refunded')),

            'total_payout' => $totalPayout = $this->scopeQuery(function ($query) use ($seller) {
                return $query->where('marketplace_transactions.marketplace_seller_id', $seller->seller_id);
            })->sum('base_total'),

            'remaining_payout' => app(OrderRepository::class)->scopeQuery(function ($query) use ($seller) {
                return $query->where('marketplace_orders.marketplace_seller_id', $seller->seller_id)
                    ->where('status', 'completed')
                    ->whereIn('seller_payout_status', ['pending', 'requested']);
            })->sum(DB::raw('base_sub_total_invoiced - base_sub_total_refunded + base_tax_amount_invoiced - base_tax_amount_refunded')),

            'total_seller_sale' => $totalSale = app(OrderRepository::class)->scopeQuery(function ($query) use ($seller) {
                return $query->where('marketplace_orders.marketplace_seller_id', $seller->seller_id);
            })->sum(DB::raw('base_grand_total - base_shipping_amount - base_commission')),

            'total_commission' => $totalCommission = app(OrderRepository::class)->scopeQuery(function ($query) use ($seller) {
                return $query->where('marketplace_orders.marketplace_seller_id', $seller->seller_id);
            })->sum(DB::raw('base_commission')),
        ];
    }
}
