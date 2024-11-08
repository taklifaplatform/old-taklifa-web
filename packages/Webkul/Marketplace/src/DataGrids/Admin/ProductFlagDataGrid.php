<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\ProductRepository;

class ProductFlagDataGrid extends DataGrid
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductRepository $productRepository) {}

    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketplace_product_flags')
            ->select(
                'marketplace_product_flags.id',
                'marketplace_product_flags.reason',
                'marketplace_product_flags.name',
                'marketplace_product_flags.email',
                'marketplace_sellers.name as seller_name',
            )
            ->leftJoin('marketplace_sellers', 'marketplace_sellers.id', '=', 'marketplace_product_flags.seller_id')
            ->where('marketplace_product_flags.product_id', request('product_id'));

        $this->addFilter('seller_name', 'marketplace_sellers.name');
        $this->addFilter('name', 'marketplace_product_flags.name');
        $this->addFilter('id', 'marketplace_product_flags.id');

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
            'label'           => trans('marketplace::app.admin.products.edit.datagrid.id'),
            'type'            => 'integer',
            'searchable'      => false,
            'sortable'        => true,
            'filterable'      => true,
            'filterable_type' => 'number',
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('marketplace::app.admin.products.edit.datagrid.customer'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return '<div class="flex flex-col gap-1.5">
                    <p class="text-base dark:text-white">'.ucwords($row->name).'</p>
                </div>';
            },
        ]);

        $this->addColumn([
            'index'      => 'seller_name',
            'label'      => trans('marketplace::app.admin.products.edit.datagrid.seller'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return ucwords($row?->seller_name) ?? '-';
            },
        ]);

        $this->addColumn([
            'index'      => 'reason',
            'label'      => trans('marketplace::app.admin.sellers.edit.datagrid.reason'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }
}
