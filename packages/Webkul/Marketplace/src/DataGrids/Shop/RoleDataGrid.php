<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class RoleDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        return DB::table('marketplace_roles')
            ->select('id', 'name', 'permission_type')
            ->where('marketplace_seller_id', auth()->guard('seller')->user()->seller_id);
    }

    /**
     * Add Columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('marketplace::app.shop.sellers.account.roles.index.datagrid.id'),
            'type'       => 'integer',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('marketplace::app.shop.sellers.account.roles.index.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'permission_type',
            'label'              => trans('marketplace::app.shop.sellers.account.roles.index.datagrid.permission-type'),
            'type'               => 'string',
            'searchable'         => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('marketplace::app.shop.sellers.account.roles.index.datagrid.custom'),
                    'value' => 'custom',
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.roles.index.datagrid.all'),
                    'value' => 'all',
                ],
            ],
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
        if (seller()->hasPermission('roles.edit')) {
            $this->addAction([
                'icon'   => 'icon-arrow-right',
                'title'  => trans('marketplace::app.shop.sellers.account.roles.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('shop.marketplace.seller.account.roles.edit', $row->id);
                },
            ]);
        }

        if (seller()->hasPermission('roles.delete')) {
            $this->addAction([
                'icon'   => 'mp-delete-icon',
                'title'  => trans('marketplace::app.shop.sellers.account.roles.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('shop.marketplace.seller.account.roles.delete', $row->id);
                },
            ]);
        }
    }
}
