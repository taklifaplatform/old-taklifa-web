<?php

namespace Webkul\Marketplace\DataGrids\Shop;

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
            ->select('marketplace_transactions.*')
            ->where('marketplace_seller_id', auth()->guard('seller')->user()->seller_id);

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
            'label'           => trans('marketplace::app.shop.sellers.account.transactions.index.datagrid.id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'transaction_id',
            'label'      => trans('marketplace::app.shop.sellers.account.transactions.index.datagrid.transaction-id'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => false,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'comment',
            'label'      => trans('marketplace::app.shop.sellers.account.transactions.index.datagrid.comment'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => false,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'           => 'base_total',
            'label'           => trans('marketplace::app.shop.sellers.account.transactions.index.datagrid.total'),
            'type'            => 'string',
            'filterable'      => true,
            'filterable_type' => 'price',
            'searchable'      => true,
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
        if (seller()->hasPermission('transactions.view')) {
            $this->addAction([
                'type'   => 'View',
                'icon'   => 'mp-eye-icon',
                'method' => 'GET',
                'title'  => 'View',
                'url'    => function ($row) {
                    return route('shop.marketplace.seller.account.transaction.view', $row->id);
                },
            ]);
        }
    }
}
