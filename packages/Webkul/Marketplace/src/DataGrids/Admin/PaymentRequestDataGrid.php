<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Marketplace\Enum\Order;
use Webkul\Marketplace\Enum\Payout;

class PaymentRequestDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'order_id';

    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketplace_orders')
            ->leftJoin('orders', 'marketplace_orders.order_id', '=', 'orders.id')
            ->leftJoin('marketplace_transactions', 'marketplace_orders.id', '=', 'marketplace_transactions.marketplace_order_id')
            ->select(
                'orders.id',
                'orders.customer_email',
                'marketplace_orders.order_id',
                'marketplace_orders.base_sub_total',
                'marketplace_orders.base_grand_total',
                'marketplace_orders.base_commission',
                'marketplace_orders.base_seller_total',
                'marketplace_orders.base_seller_total_invoiced',
                'marketplace_orders.created_at',
                'marketplace_orders.status',
                'is_withdrawal_requested',
                'seller_payout_status',
                'marketplace_orders.marketplace_seller_id',
                'marketplace_orders.base_discount_amount'
            )
            ->addSelect(
                DB::raw('CONCAT('.DB::getTablePrefix().'orders.customer_first_name, " ", '.DB::getTablePrefix().'orders.customer_last_name) as customer_name'), 'orders.increment_id'
            )
            ->addSelect(
                DB::raw('SUM('.DB::getTablePrefix().'marketplace_transactions.base_total) as total_paid')
            )
            ->groupBy('marketplace_orders.id')->where('marketplace_orders.seller_payout_status', 'requested');

        $queryBuilder->leftJoin('marketplace_sellers', 'marketplace_orders.marketplace_seller_id', '=', 'marketplace_sellers.id')
            ->addSelect(
                'marketplace_sellers.shop_title',
                'marketplace_sellers.name as seller_name'
            );

        $this->addFilter('seller_name', 'marketplace_sellers.name');
        $this->addFilter('customer_name', DB::raw('CONCAT('.DB::getTablePrefix().'orders.customer_first_name, " ", '.DB::getTablePrefix().'orders.customer_last_name)'));
        $this->addFilter('base_grand_total', 'marketplace_orders.base_grand_total');
        $this->addFilter('status', 'marketplace_orders.status');
        $this->addFilter('created_at', 'marketplace_orders.created_at');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'           => 'increment_id',
            'label'           => trans('marketplace::app.admin.payment-request.index.datagrid.id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('marketplace::app.admin.payment-request.index.datagrid.date'),
            'type'            => 'date',
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'sortable'        => true,
            'searchable'      => false,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('marketplace::app.admin.payment-request.index.datagrid.status'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label'  => trans('marketplace::app.admin.payment-request.index.datagrid.processing'),
                    'value'  => Order::STATUS_PROCESSING->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.payment-request.index.datagrid.completed'),
                    'value'  => Order::STATUS_COMPLETED->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.payment-request.index.datagrid.canceled'),
                    'value'  => Order::STATUS_CANCELED->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.payment-request.index.datagrid.closed'),
                    'value'  => Order::STATUS_CLOSED->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.payment-request.index.datagrid.pending'),
                    'value'  => Order::STATUS_PENDING->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.payment-request.index.datagrid.pending-payment'),
                    'value'  => Order::STATUS_PENDING_PAYMENT->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.payment-request.index.datagrid.fraud'),
                    'value'  => Order::STATUS_FRAUD->value,
                ],
            ],
            'sortable'   => true,
            'searchable' => false,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case Order::STATUS_PROCESSING->value:
                        return '<p class="label-processing">'.trans('marketplace::app.admin.payment-request.index.datagrid.processing').'</p>';

                    case Order::STATUS_COMPLETED->value:
                        return '<p class="label-active">'.trans('marketplace::app.admin.payment-request.index.datagrid.completed').'</p>';

                    case Order::STATUS_CANCELED->value:
                        return '<p class="label-cancelled">'.trans('marketplace::app.admin.payment-request.index.datagrid.canceled').'</p>';

                    case Order::STATUS_CLOSED->value:
                        return '<p class="label-closed">'.trans('marketplace::app.admin.payment-request.index.datagrid.closed').'</p>';

                    case Order::STATUS_PENDING->value:
                        return '<p class="label-pending">'.trans('marketplace::app.admin.payment-request.index.datagrid.pending').'</p>';

                    case Order::STATUS_PENDING_PAYMENT->value:
                        return '<p class="label-pending">'.trans('marketplace::app.admin.payment-request.index.datagrid.pending-payment').'</p>';

                    case Order::STATUS_FRAUD->value:
                        return '<p class="label-cancelled">'.trans('marketplace::app.admin.payment-request.index.datagrid.fraud').'</p>';
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => trans('marketplace::app.admin.payment-request.index.datagrid.customer-name'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'customer_email',
            'label'      => trans('marketplace::app.admin.payment-request.index.datagrid.email'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('marketplace::app.admin.payment-request.index.datagrid.gross-amt'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'base_remaining_total',
            'label'      => trans('marketplace::app.admin.payment-request.index.datagrid.remaining-total'),
            'type'       => 'string',
            'filterable' => false,
            'searchable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                if (! is_null($row->total_paid)) {
                    return core()->formatBasePrice($row->base_seller_total_invoiced - $row->total_paid);
                }

                return core()->formatBasePrice($row->base_seller_total_invoiced);
            },
        ]);

        if (bouncer()->hasPermission('marketplace.payment-request.pay')) {
            $this->addColumn([
                'index'      => 'pay',
                'label'      => trans('marketplace::app.admin.payment-request.index.datagrid.pay'),
                'type'       => 'string',
                'filterable' => false,
                'searchable' => false,
                'sortable'   => false,
                'closure'    => function ($row) {
                    if ($row->seller_payout_status == Payout::STATUS_PAID->value) {
                        return '<p class="label-active">'.trans('marketplace::app.admin.payment-request.index.datagrid.already-paid').'</p>';
                    } elseif ($row->seller_payout_status == Payout::STATUS_REFUNDED->value) {
                        return '<p class="label-cancelled">'.trans('marketplace::app.admin.payment-request.index.datagrid.refunded').'</p>';
                    } elseif ($row->seller_payout_status == Payout::STATUS_REQUESTED) {
                        $remaining = ! is_null($row->total_paid) ? $row->base_seller_total_invoiced - $row->total_paid : $row->base_seller_total_invoiced;

                        if ((float) $remaining) {
                            return '<button class="secondary-button" data-id="'.$row->id.'" seller-id="'.$row->marketplace_seller_id.'">'.trans('marketplace::app.admin.payment-request.index.datagrid.pay').'</button>';
                        }

                        return '<p class="label-pending">'.trans('marketplace::app.admin.payment-request.index.datagrid.invoice-pending').'</p>';
                    } else {
                        return '<p class="label-pending">'.trans('marketplace::app.admin.payment-request.index.datagrid.invoice-pending').'</p>';
                    }
                },
            ]);
        }

        $this->addColumn([
            'index'           => 'base_seller_total_invoiced',
            'label'           => trans('marketplace::app.admin.payment-request.index.datagrid.seller-total-invoiced'),
            'type'            => 'string',
            'filterable'      => true,
            'filterable_type' => 'price',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'seller_name',
            'label'      => trans('marketplace::app.admin.payment-request.index.datagrid.seller'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'shop_title',
            'label'      => trans('marketplace::app.admin.payment-request.index.datagrid.shop'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'           => 'total_paid',
            'label'           => trans('marketplace::app.admin.payment-request.index.datagrid.total-paid'),
            'type'            => 'string',
            'filterable'      => false,
            'filterable_type' => 'price',
            'searchable'      => false,
            'sortable'        => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon-view',
            'title'  => trans('marketplace::app.admin.payment-request.index.datagrid.view'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.sales.orders.view', $row->id);
            },
        ]);
    }
}
