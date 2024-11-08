<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Marketplace\Enum\Seller;

class SellerFlagReasonDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketplace_seller_flag_reasons')
            ->select(
                'marketplace_seller_flag_reasons.id',
                'marketplace_seller_flag_reasons.reason',
                'marketplace_seller_flag_reasons.status'
            );

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
            'label'           => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'reason',
            'label'      => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.reason'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.status'),
            'type'               => 'integer',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label'  => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.active'),
                    'value'  => Seller::FLAG_REASON_ACTIVE->value(),
                ],
                [
                    'label'  => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.disable'),
                    'value'  => Seller::FLAG_REASON_INACTIVE->value(),
                ],
            ],
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                if ($row->status) {
                    return '<span class="label-active">'.trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.active').'</span>';
                }

                return '<span class="label-info">'.trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.disable').'</span>';
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
        if (bouncer()->hasPermission('marketplace.seller-flag-reasons.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.marketplace.seller_flag_reasons.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('marketplace.seller-flag-reasons.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.marketplace.seller_flag_reasons.delete', $row->id);
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
        if (bouncer()->hasPermission('marketplace.seller-flag-reasons.mass-update')) {
            $this->addMassAction([
                'title'   => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.update-status'),
                'url'     => route('admin.marketplace.seller_flag_reasons.mass_update'),
                'method'  => 'POST',
                'options' => [
                    [
                        'label' => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.active'),
                        'value' => Seller::FLAG_REASON_ACTIVE->value(),
                    ],
                    [
                        'label' => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.inactive'),
                        'value' => Seller::FLAG_REASON_INACTIVE->value(),
                    ],
                ],
            ]);
        }

        if (bouncer()->hasPermission('marketplace.seller-flag-reasons.mass-delete')) {
            $this->addMassAction([
                'title'  => trans('marketplace::app.admin.seller-flag-reasons.index.datagrid.delete'),
                'url'    => route('admin.marketplace.seller_flag_reasons.mass_delete'),
                'method' => 'POST',
            ]);
        }
    }
}
