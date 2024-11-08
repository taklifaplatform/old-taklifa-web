<?php

namespace Webkul\Marketplace\DataGrids\Shop;

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
            ->select(
                'marketplace_seller_reviews.id',
                'rating',
                'title',
                'comment',
                'marketplace_seller_reviews.status',
                'marketplace_seller_reviews.created_at'
            )
            ->addSelect(
                DB::raw('CONCAT('.$table_prefix.'customers.first_name, " ", '.$table_prefix.'customers.last_name) as customer_name'), 'customers.email'
            )
            ->where('marketplace_seller_reviews.marketplace_seller_id', auth()->guard('seller')->user()->seller_id);

        $this->addFilter('customer_name', DB::raw('CONCAT('.$table_prefix.'customers.first_name, " ", '.$table_prefix.'customers.last_name)'));
        $this->addFilter('id', 'marketplace_seller_reviews.id');
        $this->addFilter('status', 'marketplace_seller_reviews.status');
        $this->addFilter('created_at', 'marketplace_seller_reviews.created_at');

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
            'label'      => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.customer'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'email',
            'label'      => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.email'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'rating',
            'label'      => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.rating'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => false,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.title'),
            'type'       => 'string',
            'filterable' => false,
            'sortable'   => false,
            'searchable' => false,
        ]);

        $this->addColumn([
            'index'      => 'comment',
            'label'      => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.description'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => false,
            'searchable' => false,
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.status'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.approved'),
                    'value' => Review::STATUS_APPROVED->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.pending'),
                    'value' => Review::STATUS_PENDING->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.disapproved'),
                    'value' => Review::STATUS_DISAPPROVED->value,
                ],
            ],
            'sortable'   => true,
            'searchable' => true,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case Review::STATUS_PENDING->value:
                        return '<p class="label-pending">'.trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.pending').'</p>';

                    case Review::STATUS_APPROVED->value:
                        return '<p class="label-active">'.trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.approved').'</p>';

                    case Review::STATUS_DISAPPROVED->value:
                        return '<p class="label-cancelled">'.trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.disapproved').'</p>';
                }
            },
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('marketplace::app.shop.sellers.account.seller-reviews.index.datagrid.date'),
            'type'            => 'date',
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'sortable'        => true,
            'searchable'      => true,
        ]);
    }
}
