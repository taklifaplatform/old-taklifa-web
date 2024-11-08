<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\RoleRepository;

class UserDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'seller_id';

    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketplace_sellers')
            ->leftJoin('marketplace_seller_flags', 'marketplace_sellers.id', '=', 'marketplace_seller_flags.seller_id')
            ->leftJoin('marketplace_roles', 'marketplace_sellers.marketplace_role_id', '=', 'marketplace_roles.id')
            ->select(
                'marketplace_sellers.id as seller_id',
                'marketplace_sellers.name as seller_name',
                'marketplace_sellers.email as seller_email',
                'marketplace_sellers.is_suspended',
                'marketplace_roles.name as role_name'
            )
            ->addSelect(DB::raw('COUNT(DISTINCT '.DB::getTablePrefix().'marketplace_seller_flags.id) as flag_count'))
            ->where('marketplace_sellers.parent_id', auth()->guard('seller')->user()->seller_id)
            ->groupBy('marketplace_sellers.id');

        $this->addFilter('seller_id', 'marketplace_sellers.id');
        $this->addFilter('seller_name', 'marketplace_sellers.name');
        $this->addFilter('seller_email', 'marketplace_sellers.email');
        $this->addFilter('role_name', 'marketplace_roles.name');

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
            'index'      => 'seller_id',
            'label'      => trans('marketplace::app.shop.sellers.account.users.index.datagrid.id'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'seller_name',
            'label'      => trans('marketplace::app.shop.sellers.account.users.index.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'seller_email',
            'label'      => trans('marketplace::app.shop.sellers.account.users.index.datagrid.email'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'is_suspended',
            'label'              => trans('marketplace::app.shop.sellers.account.users.index.datagrid.status.title'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('marketplace::app.shop.sellers.account.users.index.datagrid.status.options.active'),
                    'value' => 0,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.users.index.datagrid.status.options.suspended'),
                    'value' => 1,
                ],
            ],
            'sortable'           => true,
            'searchable'         => false,
        ]);

        $this->addColumn([
            'index'      => 'flag_count',
            'label'      => trans('marketplace::app.shop.sellers.account.users.index.datagrid.flags'),
            'type'       => 'integer',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'              => 'role_name',
            'label'              => trans('marketplace::app.shop.sellers.account.users.index.datagrid.permission'),
            'type'               => 'string',
            'searchable'         => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => app(RoleRepository::class)->all([
                'name as label',
                'name as value',
            ])->toArray(),
            'sortable'           => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (seller()->hasPermission('sellers.edit')) {
            $this->addAction([
                'icon'   => 'mp-pen-icon',
                'title'  => trans('marketplace::app.shop.sellers.account.users.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('shop.marketplace.seller.account.users.edit', $row->seller_id);
                },
            ]);
        }

        if (seller()->hasPermission('sellers.delete')) {
            $this->addAction([
                'icon'   => 'mp-delete-icon',
                'title'  => trans('marketplace::app.shop.sellers.account.users.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('shop.marketplace.seller.account.users.delete', $row->seller_id);
                },
            ]);
        }
    }
}
