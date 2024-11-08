<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Marketplace\Enum\Order;
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
            ->leftJoin('marketplace_sellers', 'marketplace_orders.marketplace_seller_id', '=', 'marketplace_sellers.id')
            ->select(
                'marketplace_orders.*',
                'order_payment.method as payment_method',
                'orders.increment_id',
                'orders.customer_email',
                'orders.shipping_method',
                'orders.status as order_status',
                'marketplace_transactions.base_total',
                'marketplace_transactions.marketplace_order_id',
                'marketplace_sellers.shop_title',
                'marketplace_sellers.name as seller_name',
            )
            ->addSelect(
                DB::raw('CONCAT('.$table_prefix.'orders.customer_first_name, " ", '.$table_prefix.'orders.customer_last_name) as customer_name'), 'orders.increment_id',
                DB::raw('CONCAT('.$table_prefix.'order_address_billing.city, ", ", '.$table_prefix.'order_address_billing.state,", ", '.$table_prefix.'order_address_billing.country) as location'),
            );

        $this->addFilter('status', 'marketplace_orders.status');
        $this->addFilter('created_at', 'marketplace_orders.created_at');
        $this->addFilter('customer_name', DB::raw('CONCAT('.$table_prefix.'orders.customer_first_name, " ", '.$table_prefix.'orders.customer_last_name)'));
        $this->addFilter('seller_name', 'marketplace_sellers.name');
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
            'label'           => trans('marketplace::app.admin.orders.index.datagrid.id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('marketplace::app.admin.orders.index.datagrid.date'),
            'type'            => 'date',
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'sortable'        => true,
            'searchable'      => false,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('marketplace::app.admin.orders.index.datagrid.status'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label'  => trans('marketplace::app.admin.orders.index.datagrid.processing'),
                    'value'  => Order::STATUS_PROCESSING->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.orders.index.datagrid.completed'),
                    'value'  => Order::STATUS_COMPLETED->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.orders.index.datagrid.canceled'),
                    'value'  => Order::STATUS_CANCELED->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.orders.index.datagrid.closed'),
                    'value'  => Order::STATUS_CLOSED->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.orders.index.datagrid.pending'),
                    'value'  => Order::STATUS_PENDING->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.orders.index.datagrid.pending-payment'),
                    'value'  => Order::STATUS_PENDING_PAYMENT->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.orders.index.datagrid.fraud'),
                    'value'  => Order::STATUS_FRAUD->value,
                ],
            ],
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case Order::STATUS_PROCESSING->value:
                        return '<p class="label-processing">'.trans('marketplace::app.admin.orders.index.datagrid.processing').'</p>';

                    case Order::STATUS_COMPLETED->value:
                        return '<p class="label-active">'.trans('marketplace::app.admin.orders.index.datagrid.completed').'</p>';

                    case Order::STATUS_CANCELED->value:
                        return '<p class="label-canceled">'.trans('marketplace::app.admin.orders.index.datagrid.canceled').'</p>';

                    case Order::STATUS_CLOSED->value:
                        return '<p class="label-closed">'.trans('marketplace::app.admin.orders.index.datagrid.closed').'</p>';

                    case Order::STATUS_PENDING->value:
                        return '<p class="label-pending">'.trans('marketplace::app.admin.orders.index.datagrid.pending').'</p>';

                    case Order::STATUS_PENDING_PAYMENT->value:
                        return '<p class="label-pending">'.trans('marketplace::app.admin.orders.index.datagrid.pending-payment').'</p>';

                    case Order::STATUS_FRAUD->value:
                        return '<p class="label-cancelled">'.trans('marketplace::app.admin.orders.index.datagrid.fraud').'</p>';
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.customer'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'customer_email',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.email'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'location',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.location'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'           => 'total_item_count',
            'label'           => trans('marketplace::app.admin.orders.index.datagrid.items'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'payment_method',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.payment'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->getConfigData('sales.payment_methods.'.$row->payment_method.'.title');
            },
        ]);

        $this->addColumn([
            'index'      => 'shipping_method',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.shipment'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                $method = explode('_', $row->shipping_method);

                return core()->getConfigData('sales.carriers.'.current($method).'.title');
            },
        ]);

        $this->addColumn([
            'index'      => 'base_sub_total',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.gross-amt'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'discount_amount',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.discount'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'commission',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.commission'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'seller_name',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.seller'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'shop_title',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.shop'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'seller_total',
            'label'      => trans('marketplace::app.admin.orders.index.datagrid.seller-earn'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (bouncer()->hasPermission('marketplace.orders.view')) {
            $this->addAction([
                'icon'   => 'icon-view',
                'title'  => trans('marketplace::app.admin.orders.index.datagrid.view'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.sales.orders.view', $row->order_id);
                },
            ]);
        }
    }
}
