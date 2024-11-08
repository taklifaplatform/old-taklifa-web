<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Marketplace\Enum\Review;

class ProductReviewsDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'product_review_id';

    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('product_reviews as pr')
            ->join('marketplace_product_reviews as mpr', 'mpr.product_review_id', 'pr.id')
            ->leftJoin('product_flat as pf', 'pr.product_id', '=', 'pf.product_id')
            ->leftJoin('customers as c', 'pr.customer_id', '=', 'c.id')
            ->select(
                'pr.id as product_review_id',
                'pr.title',
                'pr.comment',
                'pr.name as customer_full_name',
                'c.email',
                'pf.name as product_name',
                'pf.visible_individually',
                'pf.url_key',
                'pr.status as product_review_status',
                'pr.rating',
                'pr.created_at'
            )
            ->where('mpr.marketplace_seller_id', auth()->guard('seller')->user()->seller_id)
            ->where('channel', core()->getCurrentChannelCode())
            ->where('locale', app()->getLocale());

        $this->addFilter('product_review_id', 'pr.id');
        $this->addFilter('customer_full_name', 'pr.name');
        $this->addFilter('product_review_status', 'pr.status');
        $this->addFilter('product_name', 'pf.name');
        $this->addFilter('created_at', 'pr.created_at');

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
            'index'      => 'customer_full_name',
            'label'      => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.customer'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'email',
            'label'      => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.email'),
            'type'       => 'string',
            'filterable' => false,
            'searchable' => false,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'      => 'product_name',
            'label'      => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.product'),
            'type'       => 'string',
            'filterable' => false,
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                if (
                    ! empty($row->visible_individually)
                    && ! empty($row->url_key)
                ) {
                    return "<a href='".route('shop.product_or_category.index', $row->url_key)."' target='_blank'>".$row->product_name.'</a>';
                }

                return $row->product_name;
            },
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.date'),
            'type'            => 'date',
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'sortable'        => true,
            'searchable'      => false,
        ]);

        $this->addColumn([
            'index'              => 'product_review_status',
            'label'              => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.status'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.pending'),
                    'value' => Review::STATUS_PENDING->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.approved'),
                    'value' => Review::STATUS_APPROVED->value,
                ],
                [
                    'label' => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.disapproved'),
                    'value' => Review::STATUS_DISAPPROVED->value,
                ],
            ],
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                switch ($row->product_review_status) {
                    case Review::STATUS_PENDING->value:
                        return '<p class="label-pending">'.trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.pending').'</p>';

                    case Review::STATUS_APPROVED->value:
                        return '<p class="label-active">'.trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.approved').'</p>';

                    case Review::STATUS_DISAPPROVED->value:
                        return '<p class="label-closed">'.trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.disapproved').'</p>';
                }
            },
        ]);

        $this->addColumn([
            'index'           => 'rating',
            'label'           => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.rating'),
            'type'            => 'integer',
            'filterable'      => true,
            'filterable_type' => 'number',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.title'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'comment',
            'label'      => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.description'),
            'type'       => 'string',
            'filterable' => true,
            'searchable' => true,
            'sortable'   => true,
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (seller()->hasPermission('product_reviews.edit')) {
            $this->addMassAction([
                'title'   => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.update-status'),
                'url'     => route('shop.marketplace.seller.account.products.review.mass_update'),
                'method'  => 'POST',
                'options' => [
                    [
                        'label' => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.pending'),
                        'value' => Review::STATUS_PENDING->value,
                    ],
                    [
                        'label' => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.approved'),
                        'value' => Review::STATUS_APPROVED->value,
                    ],
                    [
                        'label' => trans('marketplace::app.shop.sellers.account.product-reviews.index.datagrid.disapproved'),
                        'value' => Review::STATUS_DISAPPROVED->value,
                    ],
                ],
            ]);
        }
    }
}
