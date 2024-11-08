<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class SellerCategoryDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('seller_categories')
            ->leftJoin('marketplace_sellers', 'seller_categories.seller_id', 'marketplace_sellers.id')
            ->select(
                'marketplace_sellers.name',
                'seller_categories.categories',
                'seller_categories.id'
            );

        $this->addFilter('id', 'seller_categories.id');
        $this->addFilter('name', 'marketplace_sellers.name');

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
            'label'           => trans('marketplace::app.admin.seller-categories.index.datagrid.id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('marketplace::app.admin.seller-categories.index.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (bouncer()->hasPermission('marketplace.seller-categories.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('marketplace::app.admin.seller-categories.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.marketplace.seller_categories.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('marketplace.seller-categories.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('marketplace::app.admin.seller-categories.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.marketplace.seller_categories.delete', $row->id);
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (bouncer()->hasPermission('marketplace.seller-categories.mass-delete')) {
            $this->addMassAction([
                'title'  => trans('marketplace::app.admin.seller-categories.index.datagrid.delete'),
                'url'    => route('admin.marketplace.seller_categories.mass_delete'),
                'method' => 'POST',
            ]);
        }
    }
}
