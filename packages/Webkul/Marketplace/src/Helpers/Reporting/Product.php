<?php

namespace Webkul\Marketplace\Helpers\Reporting;

use Illuminate\Support\Facades\DB;
use Webkul\Admin\Helpers\Reporting\AbstractReporting;
use Webkul\Marketplace\Repositories\OrderItemRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;

class Product extends AbstractReporting
{
    /**
     * Create a helper instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductInventoryRepository $productInventoryRepository,
        protected OrderItemRepository $orderItemRepository,
    ) {
        parent::__construct();
    }

    /**
     * Gets stock threshold.
     *
     * @param  \Webkul\Marketplace\Contracts\Seller  $seller
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStockThresholdProducts($seller, $limit = 5)
    {
        return $this->productInventoryRepository->getModel()
            ->with([
                'product' => [
                    'images',
                ],
            ])
            ->leftJoin('products', 'product_inventories.product_id', 'products.id')
            ->leftJoin('marketplace_products', 'products.id', 'marketplace_products.product_id')
            ->select('marketplace_products.*')
            ->addSelect(DB::raw('SUM(qty) as total_qty'))
            ->where('products.type', '!=', 'configurable')
            ->where('marketplace_products.marketplace_seller_id', $seller->seller_id)
            ->where('product_inventories.vendor_id', $seller->seller_id)
            ->groupBy('marketplace_products.product_id')
            ->orderBy('total_qty', 'ASC')
            ->limit($limit)
            ->get();
    }

    /**
     * Gets top-selling products by revenue.
     *
     * @param  \Webkul\Marketplace\Contracts\Seller  $seller
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopSellingProductsByRevenue($seller, $limit = 5)
    {
        return $this->orderItemRepository->getModel()
            ->with([
                'item' => [
                    'product' => [
                        'images',
                    ],
                ],
            ])
            ->join('order_items', 'order_items.id', 'marketplace_order_items.order_item_id')
            ->join('products', 'products.id', 'order_items.product_id')
            ->leftJoin('marketplace_orders', 'marketplace_order_items.marketplace_order_id', 'marketplace_orders.id')
            ->addSelect(
                '*',
                DB::raw('SUM(base_total_invoiced - order_items.base_discount_refunded) as revenue'),
                DB::raw('SUM(base_total_invoiced - order_items.base_discount_refunded) / SUM(order_items.qty_invoiced - order_items.qty_refunded) as per_unit')
            )
            ->whereNull('order_items.parent_id')
            ->where('marketplace_orders.marketplace_seller_id', $seller->seller_id)
            ->whereBetween('order_items.created_at', [$this->startDate, $this->endDate])
            ->groupBy('order_items.product_id')
            ->orderBy('revenue', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top-selling categories by revenue.
     *
     * @param  \Webkul\Marketplace\Contracts\Seller  $seller
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopSellingCategoriesByRevenue($seller, $limit = 5)
    {
        $locale = core()->getCurrentLocale();

        $channel = core()->getRequestedChannel();

        return $this->orderItemRepository->getModel()
            ->join('order_items', 'order_items.id', '=', 'marketplace_order_items.order_item_id')
            ->leftJoin('marketplace_orders', 'marketplace_order_items.marketplace_order_id', '=', 'marketplace_orders.id')
            ->leftJoin('product_categories', 'product_categories.product_id', '=', 'order_items.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'product_categories.category_id')
            ->leftJoin('category_translations', function ($join) use ($locale) {
                $join->on('category_translations.category_id', '=', 'product_categories.category_id')
                    ->where('category_translations.locale', '=', $locale->code);
            })
            ->leftJoin('category_translations as parent_category', function ($join) use ($locale, $channel) {
                $join->on('parent_category.category_id', '=', 'categories.parent_id')
                    ->where('parent_category.id', '!=', $channel->root_category_id)
                    ->where('parent_category.locale', '=', $locale->code);
            })
            ->select(
                'category_translations.name as translation_name',
                DB::raw('IFNULL(CONCAT(parent_category.name, " > ", category_translations.name), category_translations.name) as hierarchy'),
                DB::raw('SUM(order_items.base_total_invoiced - order_items.base_discount_refunded) as revenue')
            )
            ->where('product_categories.category_id', '!=', $channel->root_category_id)
            ->whereNull('order_items.parent_id')
            ->where('marketplace_orders.marketplace_seller_id', $seller->seller_id)
            ->whereBetween('order_items.created_at', [$this->startDate, $this->endDate])
            ->groupBy('product_categories.category_id', 'category_translations.name', 'parent_category.name')
            ->orderBy('revenue', 'DESC')
            ->limit($limit)
            ->get();
    }
}
