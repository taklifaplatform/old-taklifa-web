<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Marketplace\Enum\Review;

class SellerReviewDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $table_prefix = DB::getTablePrefix();

        $queryBuilder = DB::table('marketplace_seller_reviews')
            ->leftJoin('customers', 'marketplace_seller_reviews.customer_id', '=', 'customers.id')
            ->leftJoin('marketplace_sellers', 'marketplace_seller_reviews.marketplace_seller_id', '=', 'marketplace_sellers.id')
            ->select(
                'marketplace_seller_reviews.id',
                'rating',
                'marketplace_seller_reviews.status',
                'comment',
                'marketplace_sellers.shop_title',
                'marketplace_sellers.name as seller_name',
                'title',
                'marketplace_seller_reviews.created_at'
            )
            ->addSelect(
                DB::raw('CONCAT('.$table_prefix.'customers.first_name, " ", '.$table_prefix.'customers.last_name) as customer_name')
            );

        $this->addFilter('customer_name', DB::raw('CONCAT('.$table_prefix.'customers.first_name, " ", '.$table_prefix.'customers.last_name)'));
        $this->addFilter('seller_name', 'marketplace_sellers.name');
        $this->addFilter('created_at', 'marketplace_seller_reviews.created_at');
        $this->addFilter('status', 'marketplace_seller_reviews.status');
        $this->addFilter('id', 'marketplace_seller_reviews.id');

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
            'index'      => 'customer_name',
            'label'      => trans('marketplace::app.admin.seller-reviews.index.datagrid.customer-name'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('marketplace::app.admin.seller-reviews.index.datagrid.status'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label'  => trans('marketplace::app.admin.seller-reviews.index.datagrid.approved'),
                    'value'  => Review::STATUS_APPROVED->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.seller-reviews.index.datagrid.pending'),
                    'value'  => Review::STATUS_PENDING->value,
                ],
                [
                    'label'  => trans('marketplace::app.admin.seller-reviews.index.datagrid.disapproved'),
                    'value'  => Review::STATUS_DISAPPROVED->value,
                ],
            ],
            'sortable'   => true,
            'searchable' => true,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case Review::STATUS_PENDING->value:
                        return '<p class="label-pending">'.trans('marketplace::app.admin.seller-reviews.index.datagrid.pending').'</p>';

                    case Review::STATUS_APPROVED->value:
                        return '<p class="label-active">'.trans('marketplace::app.admin.seller-reviews.index.datagrid.approved').'</p>';

                    case Review::STATUS_DISAPPROVED->value:
                        return '<p class="label-canceled">'.trans('marketplace::app.admin.seller-reviews.index.datagrid.disapproved').'</p>';
                }
            },
        ]);

        $this->addColumn([
            'index'           => 'id',
            'label'           => trans('marketplace::app.admin.seller-reviews.index.datagrid.id'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => false,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'seller_name',
            'label'      => trans('marketplace::app.admin.seller-reviews.index.datagrid.seller-name'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'rating',
            'label'      => trans('marketplace::app.admin.seller-reviews.index.datagrid.rating'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('marketplace::app.admin.seller-reviews.index.datagrid.date'),
            'type'            => 'date',
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'shop_title',
            'label'      => trans('marketplace::app.admin.seller-reviews.index.datagrid.shop-title'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
            'searchable' => false,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('marketplace::app.admin.seller-reviews.index.datagrid.review-title'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
            'searchable' => false,
        ]);

        $this->addColumn([
            'index'      => 'comment',
            'label'      => trans('marketplace::app.admin.seller-reviews.index.datagrid.comment'),
            'type'       => 'string',
            'filterable' => false,
            'sortable'   => false,
            'searchable' => true,
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (bouncer()->hasPermission('marketplace.seller-reviews.mass-update')) {
            $this->addMassAction([
                'title'   => trans('marketplace::app.admin.seller-reviews.index.datagrid.update-status'),
                'url'     => route('admin.marketplace.seller_reviews.mass_update'),
                'method'  => 'POST',
                'options' => [
                    [
                        'label' => trans('marketplace::app.admin.seller-reviews.index.datagrid.pending'),
                        'value' => Review::STATUS_PENDING->value,
                    ],
                    [
                        'label' => trans('marketplace::app.admin.seller-reviews.index.datagrid.approved'),
                        'value' => Review::STATUS_APPROVED->value,
                    ],
                    [
                        'label' => trans('marketplace::app.admin.seller-reviews.index.datagrid.disapproved'),
                        'value' => Review::STATUS_DISAPPROVED->value,
                    ],
                ],
            ]);
        }
    }
}
