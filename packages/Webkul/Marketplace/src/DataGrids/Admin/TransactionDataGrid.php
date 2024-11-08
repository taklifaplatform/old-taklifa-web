<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class TransactionDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketplace_transactions')
            ->leftJoin('marketplace_sellers', 'marketplace_transactions.marketplace_seller_id', '=', 'marketplace_sellers.id')
            ->select(
                'marketplace_transactions.id',
                'transaction_id',
                'comment',
                'base_total',
                'marketplace_seller_id',
                'marketplace_order_id',
                'marketplace_sellers.name as seller_name'
            );

        $this->addFilter('seller_name', 'marketplace_sellers.name');
        $this->addFilter('id', 'marketplace_transactions.id');

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
            'index'           => 'id',
            'label'           => trans('marketplace::app.admin.transactions.index.datagrid.id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'seller_name',
            'label'      => trans('marketplace::app.admin.transactions.index.datagrid.seller-name'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'           => 'marketplace_seller_id',
            'label'           => trans('marketplace::app.admin.transactions.index.datagrid.seller-id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'sortable'        => true,
            'searchable'      => true,
        ]);

        $this->addColumn([
            'index'      => 'transaction_id',
            'label'      => trans('marketplace::app.admin.transactions.index.datagrid.transaction-id'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => false,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'comment',
            'label'      => trans('marketplace::app.admin.transactions.index.datagrid.comment'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => false,
            'searchable' => false,
        ]);

        $this->addColumn([
            'index'           => 'base_total',
            'label'           => trans('marketplace::app.admin.transactions.index.datagrid.base-total'),
            'type'            => 'string',
            'filterable'      => true,
            'filterable_type' => 'price',
            'searchable'      => false,
            'sortable'        => true,
            'closure'         => function ($row) {
                return core()->formatPrice($row->base_total);
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
        if (bouncer()->hasPermission('marketplace.transactions.view')) {
            $this->addAction([
                'icon'   => 'icon-view',
                'title'  => trans('marketplace::app.admin.transactions.index.datagrid.view'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.marketplace.transactions.view', $row->id);
                },
            ]);
        }
    }
}
