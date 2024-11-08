<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Marketplace\Enum\Order;
use Webkul\Marketplace\Enum\Payout;
use Webkul\Sales\Models\OrderAddress;

class OrderDataGrid extends DataGrid
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
        $table_prefix = DB::getTablePrefix();

        $queryBuilder = DB::table('marketplace_orders')
            ->leftJoin('orders', 'marketplace_orders.order_id', '=', 'orders.id')
            ->leftJoin('marketplace_transactions', 'marketplace_orders.id', '=', 'marketplace_transactions.marketplace_order_id')
            ->leftJoin('addresses as order_address_shipping', function ($leftJoin) {
                $leftJoin->on('order_address_shipping.order_id', '=', 'orders.id')
                    ->where('order_address_shipping.address_type', OrderAddress::ADDRESS_TYPE_SHIPPING);
            })
            ->leftJoin('addresses as order_address_billing', function ($leftJoin) {
                $leftJoin->on('order_address_billing.order_id', '=', 'orders.id')
                    ->where('order_address_billing.address_type', OrderAddress::ADDRESS_TYPE_BILLING);
            })
            ->leftJoin('order_payment', 'orders.id', '=', 'order_payment.order_id')
            ->select(
                'marketplace_orders.*',
                'marketplace_orders.id as marketplace_order_id',
                'order_payment.method as payment_method',
                'orders.increment_id',
                'orders.customer_email',
                'orders.shipping_method',
                'orders.order_currency_code',
                'orders.status as order_status',
                'marketplace_transactions.base_total',
                'marketplace_transactions.id as marketplace_transaction_id',
            )
            ->addSelect(
                DB::raw('CONCAT('.$table_prefix.'orders.customer_first_name, " ",'.$table_prefix.'orders.customer_last_name) as customer_name'),
                DB::raw('CONCAT('.$table_prefix.'order_address_billing.city, ", ", '.$table_prefix.'order_address_billing.state,", ", '.$table_prefix.'order_address_billing.country) as location')
            )
            ->where('marketplace_orders.marketplace_seller_id', auth()->guard('seller')->user()->seller_id);

        $this->addFilter('created_at', 'marketplace_orders.created_at');
        $this->addFilter('order_status', 'marketplace_orders.status');
        $this->addFilter('customer_name', DB::raw('CONCAT('.$table_prefix.'orders.customer_first_name, " ", '.$table_prefix.'orders.customer_last_name)'));
        $this->addFilter('location', DB::raw('CONCAT('.$table_prefix.'order_address_billing.city, ", ", '.$table_prefix.'order_address_billing.state,", ", '.$table_prefix.'order_address_billing.country)'));
        $this->addFilter('payment_method', 'order_payment.method');
        $this->addFilter('base_sub_total', 'marketplace_orders.base_sub_total');
        $this->addFilter('discount_amount', 'orders.discount_amount');

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
            'label'           => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.date'),
            'type'            => 'date',
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'              => 'order_status',
            'label'              => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.status'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.processing'),
                    'value' => Order::STATUS_PROCESSING->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.completed'),
                    'value' => Order::STATUS_COMPLETED->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.canceled'),
                    'value' => Order::STATUS_CANCELED->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.closed'),
                    'value' => Order::STATUS_CLOSED->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.pending'),
                    'value' => Order::STATUS_PENDING->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.pending-payment'),
                    'value' => Order::STATUS_PENDING_PAYMENT->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.fraud'),
                    'value' => Order::STATUS_FRAUD->value,
                ],
            ],
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case Order::STATUS_PROCESSING->value:
                        return '<p class="label-processing">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.processing').'</p>';

                    case Order::STATUS_COMPLETED->value:
                        return '<p class="label-active">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.completed').'</p>';

                    case Order::STATUS_CANCELED->value:
                        return '<p class="label-cancelled">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.canceled').'</p>';

                    case Order::STATUS_CLOSED->value:
                        return '<p class="label-closed">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.closed').'</p>';

                    case Order::STATUS_PENDING->value:
                        return '<p class="label-pending">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.pending').'</p>';

                    case Order::STATUS_PENDING_PAYMENT->value:
                        return '<p class="label-pending">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.pending-payment').'</p>';

                    case Order::STATUS_FRAUD->value:
                        return '<p class="label-cancelled">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.fraud').'</p>';
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.customer'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'customer_email',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.email'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'      => 'location',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.location'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'           => 'total_item_count',
            'label'           => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.items'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'payment_method',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.payment'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => false,
            'closure'    => function ($row) {
                return core()->getConfigData('sales.payment_methods.'.$row->payment_method.'.title');
            },
        ]);

        $this->addColumn([
            'index'      => 'shipping_method',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.shipment'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => false,
            'closure'    => function ($row) {
                $method = explode('_', $row->shipping_method);

                return core()->getConfigData('sales.carriers.'.current($method).'.title');
            },
        ]);

        $this->addColumn([
            'index'      => 'base_sub_total',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.gross-amt'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatPrice($row->base_sub_total, $row->order_currency_code);
            },
        ]);

        $this->addColumn([
            'index'      => 'discount_amount',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.discount'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatPrice($row->discount_amount, $row->order_currency_code);
            },
        ]);

        $this->addColumn([
            'index'      => 'commission',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.commission'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatPrice($row->commission, $row->order_currency_code);
            },
        ]);

        $this->addColumn([
            'index'      => 'seller_total',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.seller-earn'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatPrice($row->seller_total, $row->order_currency_code);
            },
        ]);

        $this->addColumn([
            'index'      => 'seller_payout_status',
            'label'      => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.status'),
            'type'       => 'string',
            'filterable' => false,
            'searchable' => true,
            'sortable'   => false,
            'closure'    => function ($row) {
                if ($row->seller_payout_status == Payout::STATUS_PAID->value) {
                    return '<div class="grid gap-y-1.5"><span class="label-active">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.paid').'</span><a href='.route('shop.marketplace.seller.account.transaction.view', $row->marketplace_transaction_id).' class="text-[#0A49A7]">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.transaction').'</a></div>';
                } elseif ($row->seller_payout_status == Payout::STATUS_REFUNDED->value) {
                    return '<span class="label-closed">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.refunded').'</span>';
                } elseif ($row->seller_payout_status == Payout::STATUS_REQUESTED->value) {
                    return '<span class="label-pending">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.requested').'</span>';
                } else {
                    $remaining = ! is_null($row->base_total) ? $row->base_seller_total_invoiced - $row->base_total : $row->base_seller_total_invoiced;

                    if (
                        (float) $remaining
                        && $row->status == 'completed'
                    ) {
                        return '<a href='.route('shop.marketplace.seller.account.payment.request', $row->marketplace_order_id).' class="text-[#0A49A7]">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.payment-request').'</a> ';
                    } elseif (
                        (float) $remaining
                        && $row->status == 'processing'
                    ) {
                        return '<span class="label-active">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.processing').'</span>';
                    } elseif ($row->status == 'canceled') {
                        return '<span class="label-cancelled">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.canceled').'</span>';
                    }

                    return '<span class="label-pending">'.trans('marketplace::app.shop.sellers.account.orders.index.datagrid.invoice-pending').'</span>';
                }
            },
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (seller()->hasPermission('orders.view')) {
            $this->addAction([
                'type'   => 'View',
                'icon'   => 'icon-arrow-right',
                'method' => 'GET',
                'title'  => trans('marketplace::app.shop.sellers.account.orders.index.datagrid.view'),
                'url'    => function ($row) {
                    return route('shop.marketplace.seller.account.orders.view', $row->order_id);
                },
            ]);
        }
    }
}
