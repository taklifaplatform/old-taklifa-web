<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Marketplace\Contracts\Product;
use Webkul\Marketplace\Enum\Product as ProductEnum;
use Webkul\Marketplace\Helpers\Toolbar as ToolbarHelper;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;

class ProductRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        App $app,
        protected AttributeRepository $attributeRepository,
        protected BaseProductRepository $baseProductRepository,
        protected CustomerRepository $customerRepository,
        protected ElasticSearchRepository $elasticSearchRepository,
        protected ProductDownloadableLinkRepository $productDownloadableLinkRepository,
        protected ProductDownloadableSampleRepository $productDownloadableSampleRepository,
        protected ProductFlatRepository $productFlatRepository,
        protected ProductImageRepository $productImageRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected ProductVideoRepository $productVideoRepository,
        protected ReviewRepository $reviewRepository,
        protected SellerRepository $sellerRepository,
        protected ToolbarHelper $toolbarHelper,
    ) {
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Product::class;
    }

    /**
     * @return mixed
     */
    public function create(array $data, $seller = null)
    {
        Event::dispatch('marketplace.catalog.product.create.before');

        if (empty($seller)) {
            $seller = auth()->guard('seller')->user();
        }

        $sellerProduct = parent::create(array_merge($data, [
            'marketplace_seller_id' => $seller->seller_id,
            'is_approved'           => core()->getConfigData('marketplace.settings.general.product_approval_required') ? 0 : 1,
        ]));

        foreach ($sellerProduct->product->variants as $baseVariant) {
            parent::create([
                'parent_id'             => $sellerProduct->id,
                'product_id'            => $baseVariant->id,
                'is_owner'              => 1,
                'marketplace_seller_id' => $seller->seller_id,
                'is_approved'           => core()->getConfigData('marketplace.settings.general.product_approval_required') ? 0 : 1,
            ]);
        }

        Event::dispatch('marketplace.catalog.product.create.after', $sellerProduct);

        return $sellerProduct;
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        Event::dispatch('marketplace.catalog.product.update.before', $id);

        $sellerProduct = $this->find($id);

        $sellerProduct->update($data);

        foreach ($sellerProduct->product->variants as $baseVariant) {
            if (! $this->getMarketplaceProductByProduct($baseVariant->id, $sellerProduct->seller->id)) {
                parent::create([
                    'parent_id'             => $sellerProduct->id,
                    'product_id'            => $baseVariant->id,
                    'is_owner'              => 1,
                    'marketplace_seller_id' => $sellerProduct->seller->id,
                    'is_approved'           => $sellerProduct->is_approved,
                ]);
            }
        }

        Event::dispatch('marketplace.catalog.product.update.after', $sellerProduct);

        return $sellerProduct;
    }

    /**
     * Assign product to seller
     *
     * @return \Webkul\Marketplace\Contracts\Product
     */
    public function createAssign(array $data)
    {
        Event::dispatch('marketplace.catalog.assign-product.create.before');

        if (! empty($data['seller_id'])) {
            $seller = $this->sellerRepository->findOneByField('id', $data['seller_id']);

            unset($data['seller_id']);
        } elseif (auth()->guard('seller')->user()) {
            $seller = auth()->guard('seller')->user();
        }

        $sellerProduct = parent::create(array_merge($data, [
            'marketplace_seller_id' => $seller->seller_id,
            'is_approved'           => core()->getConfigData('marketplace.settings.general.product_approval_required') ? 0 : 1,
        ]));

        $this->productInventoryRepository->saveInventories(array_merge($data, [
            'vendor_id' => $sellerProduct->marketplace_seller_id,
        ]), $sellerProduct->product);

        $this->productImageRepository->uploadImages($data, $sellerProduct);

        $this->productVideoRepository->uploadVideos($data, $sellerProduct);

        if (! empty($data['downloadable_links'])) {
            $this->productDownloadableLinkRepository->saveLinks($data, $sellerProduct);

            $this->productDownloadableSampleRepository->saveSamples($data, $sellerProduct);
        }

        if (! empty($data['selected_variants'])) {
            foreach ($data['selected_variants'] as $baseVariantId) {
                $variantData = $data['variants'][$baseVariantId];

                $childProduct = parent::create(array_merge($variantData, [
                    'parent_id'             => $sellerProduct->id,
                    'condition'             => $sellerProduct->condition,
                    'product_id'            => $baseVariantId,
                    'is_owner'              => 0,
                    'marketplace_seller_id' => $seller->seller_id,
                    'is_approved'           => core()->getConfigData('marketplace.settings.general.product_approval_required') ? 0 : 1,
                ]));

                $this->productInventoryRepository->saveInventories(array_merge($variantData, [
                    'vendor_id' => $childProduct->marketplace_seller_id,
                ]), $childProduct->product);

                $this->productImageRepository->uploadImages($variantData, $childProduct);
            }
        }

        Event::dispatch('marketplace.catalog.assign-product.create.after', [$sellerProduct, $data]);

        return $sellerProduct;
    }

    /**
     * Update assigned product
     *
     * @return \Webkul\Marketplace\Contracts\Product
     */
    public function updateAssign(array $data, $id)
    {
        Event::dispatch('marketplace.catalog.assign-product.update.before', $id);

        $sellerProduct = $this->find($id);

        parent::update($data, $id);

        $this->productImageRepository->uploadImages($data, $sellerProduct);

        $this->productVideoRepository->uploadVideos($data, $sellerProduct);

        $this->productInventoryRepository->saveInventories(array_merge($data, [
            'vendor_id' => $sellerProduct->marketplace_seller_id,
        ]), $sellerProduct->product);

        if (! empty($data['downloadable_links'])) {
            $this->productDownloadableLinkRepository->saveLinks($data, $sellerProduct);

            $this->productDownloadableSampleRepository->saveSamples($data, $sellerProduct);
        }

        $previousBaseVariantIds = $sellerProduct->variants->pluck('product_id');

        if (isset($data['selected_variants'])) {
            foreach ($data['selected_variants'] as $baseVariantId) {
                $variantData = $data['variants'][$baseVariantId];

                if (is_numeric($index = $previousBaseVariantIds->search($baseVariantId))) {
                    $previousBaseVariantIds->forget($index);
                }

                $childProduct = $this->updateOrCreate([
                    'product_id'            => $baseVariantId,
                    'marketplace_seller_id' => $sellerProduct->marketplace_seller_id,
                    'is_owner'              => 0,
                ], array_merge($variantData, [
                    'parent_id'   => $sellerProduct->id,
                    'condition'   => $sellerProduct->condition,
                    'is_approved' => $sellerProduct->id_approved,
                ]));

                $this->productImageRepository->uploadImages($variantData, $childProduct);

                $this->productInventoryRepository->saveInventories(array_merge($variantData, [
                    'vendor_id' => $childProduct->marketplace_seller_id,
                ]), $childProduct->product);
            }
        }

        if ($previousBaseVariantIds->count()) {
            $sellerProduct->variants()
                ->whereIn('product_id', $previousBaseVariantIds)
                ->delete();
        }

        Event::dispatch('marketplace.catalog.assign-product.update.after', [$sellerProduct, $data]);

        return $sellerProduct;
    }

    /**
     * Returns top selling products
     *
     * @param  int  $sellerId
     * @return mixed
     */
    public function getTopSellingProducts($sellerId)
    {
        return app(OrderItemRepository::class)->getModel()
            ->leftJoin('marketplace_products', 'marketplace_order_items.marketplace_product_id', 'marketplace_products.id')
            ->leftJoin('order_items', 'marketplace_order_items.order_item_id', 'order_items.id')
            ->leftJoin('marketplace_orders', 'marketplace_order_items.marketplace_order_id', 'marketplace_orders.id')
            ->select(DB::raw('SUM(qty_ordered) as total_qty_ordered'), 'marketplace_products.product_id')
            ->where('marketplace_orders.marketplace_seller_id', $sellerId)
            ->where('marketplace_products.is_approved', 1)
            ->whereNull('order_items.parent_id')
            ->groupBy('marketplace_products.product_id')
            ->orderBy('total_qty_ordered', 'DESC')
            ->limit(5)
            ->pluck('product_id')
            ->toArray();
    }

    /**
     * Returns the total products of the seller
     *
     * @param  Seller  $seller
     * @return int
     */
    public function getTotalProducts($seller, $assignProducts = false)
    {
        $totalProducts = $this->with('product_flats')
            ->where('marketplace_seller_id', $seller->seller_id)
            ->where('is_approved', 1)
            ->whereHas('product_flats', function ($query) {
                $query->where('status', 1)
                    ->where('visible_individually', 1);
            });

        if (! $assignProducts) {
            $totalProducts->where('is_owner', 1);
        }

        return $totalProducts->count();
    }

    /**
     * Get all products.
     *
     * @param  object  $seller
     * @return Collection
     */
    public function getAll($seller)
    {
        if (
            core()->getConfigData('catalog.products.search.engine') == 'elastic'
            && core()->getConfigData('catalog.products.search.storefront_mode') == 'elastic'
        ) {
            return $this->searchFromElastic($seller);
        }

        return $this->searchFromDatabase($seller);
    }

    /**
     * Returns the all products of the seller
     *
     * @param  object  $seller
     * @return Collection
     */
    public function searchFromDatabase($seller)
    {
        $params = array_merge([
            'status'               => 1,
            'visible_individually' => 1,
            'url_key'              => null,
        ], request()->input());

        if (! empty($params['term'])) {
            $params['name'] = $params['term'];
        }

        $query = $this->baseProductRepository->with([
            'attribute_family',
            'images',
            'videos',
            'attribute_values',
            'price_indices',
            'inventory_indices',
            'reviews',
        ])->scopeQuery(function ($query) use ($params, $seller) {
            $prefix = DB::getTablePrefix();

            $qb = $query->distinct()
                ->select('products.*')
                ->join('marketplace_products', 'marketplace_products.product_id', '=', 'products.id')
                ->leftJoin('products as variants', DB::raw('COALESCE('.$prefix.'variants.parent_id, '.$prefix.'variants.id)'), '=', 'products.id')
                ->where('marketplace_products.marketplace_seller_id', $seller->seller_id)
                ->where('marketplace_products.is_approved', 1)
                ->leftJoin('product_price_indices', function ($join) {
                    $customerGroup = $this->customerRepository->getCurrentGroup();

                    $join->on('products.id', '=', 'product_price_indices.product_id')
                        ->where('product_price_indices.customer_group_id', $customerGroup->id);
                });

            if (! empty($params['category_id'])) {
                $qb->leftJoin('product_categories', 'product_categories.product_id', '=', 'products.id')
                    ->whereIn('product_categories.category_id', explode(',', $params['category_id']));
            }

            if (! empty($params['type'])) {
                $qb->where('products.type', $params['type']);
            }

            /**
             * Filter query by price.
             */
            if (! empty($params['price'])) {
                $priceRange = explode(',', $params['price']);

                $qb->whereBetween('product_price_indices.min_price', [
                    core()->convertToBasePrice(current($priceRange)),
                    core()->convertToBasePrice(end($priceRange)),
                ]);
            }

            /**
             * Retrieve all the filterable attributes.
             */
            $filterableAttributes = $this->attributeRepository->getProductDefaultAttributes(array_keys($params));

            /**
             * Filter the required attributes.
             */
            $attributes = $filterableAttributes->whereIn('code', [
                'name',
                'status',
                'visible_individually',
                'url_key',
            ]);

            /**
             * Filter collection by required attributes.
             */
            foreach ($attributes as $attribute) {
                $alias = $attribute->code.'_product_attribute_values';

                $qb->leftJoin('product_attribute_values as '.$alias, 'products.id', '=', $alias.'.product_id')
                    ->where($alias.'.attribute_id', $attribute->id);

                if ($attribute->code == 'name') {
                    $qb->where($alias.'.text_value', 'like', '%'.urldecode($params['name']).'%');
                } elseif ($attribute->code == 'url_key') {
                    if (empty($params['url_key'])) {
                        $qb->whereNotNull($alias.'.text_value');
                    } else {
                        $qb->where($alias.'.text_value', 'like', '%'.urldecode($params['url_key']).'%');
                    }
                } else {
                    if (is_null($params[$attribute->code])) {
                        continue;
                    }

                    $qb->where($alias.'.'.$attribute->column_name, 1);
                }
            }

            /**
             * Filter the filterable attributes.
             */
            $attributes = $filterableAttributes->whereNotIn('code', [
                'price',
                'name',
                'status',
                'visible_individually',
                'url_key',
            ]);

            /**
             * Filter query by attributes.
             */
            if ($attributes->isNotEmpty()) {
                $qb->leftJoin('product_attribute_values', 'products.id', '=', 'product_attribute_values.product_id');

                $qb->where(function ($filterQuery) use ($params, $attributes) {
                    foreach ($attributes as $attribute) {
                        $filterQuery->orWhere(function ($attributeQuery) use ($params, $attribute) {
                            $attributeQuery = $attributeQuery->where('product_attribute_values.attribute_id', $attribute->id);

                            $values = explode(',', $params[$attribute->code]);

                            if ($attribute->type == 'price') {
                                $attributeQuery->whereBetween('product_attribute_values.'.$attribute->column_name, [
                                    core()->convertToBasePrice(current($values)),
                                    core()->convertToBasePrice(end($values)),
                                ]);
                            } else {
                                $attributeQuery->whereIn('product_attribute_values.'.$attribute->column_name, $values);
                            }
                        });
                    }
                });

                $qb->groupBy('products.id');
                $qb->havingRaw('COUNT(*) = '.count($attributes));
            }

            /**
             * Sort collection.
             */
            $sortOptions = $this->getSortOptions($params);

            if ($sortOptions['order'] != 'rand') {
                $attribute = $this->attributeRepository->findOneByField('code', $sortOptions['sort']);

                if ($attribute) {
                    if ($attribute->code === 'price') {
                        $qb->orderBy('product_price_indices.min_price', $sortOptions['order']);
                    } else {
                        $alias = 'sort_product_attribute_values';

                        $qb->leftJoin('product_attribute_values as '.$alias, function ($join) use ($alias, $attribute) {
                            $join->on('products.id', '=', $alias.'.product_id')
                                ->where($alias.'.attribute_id', $attribute->id);

                            if ($attribute->value_per_channel) {
                                if ($attribute->value_per_locale) {
                                    $join->where($alias.'.channel', core()->getRequestedChannelCode())
                                        ->where($alias.'.locale', core()->getRequestedLocaleCode());
                                } else {
                                    $join->where($alias.'.channel', core()->getRequestedChannelCode());
                                }
                            } else {
                                if ($attribute->value_per_locale) {
                                    $join->where($alias.'.locale', core()->getRequestedLocaleCode());
                                }
                            }
                        })
                            ->orderBy($alias.'.'.$attribute->column_name, $sortOptions['order']);
                    }
                } else {
                    /* `created_at` is not an attribute so it will be in else case */
                    $qb->orderBy('products.created_at', $sortOptions['order']);
                }
            } else {
                return $qb->inRandomOrder();
            }

            return $qb->groupBy('products.id');
        });

        /**
         * Apply scope query so we can fetch the raw sql and perform a count.
         */
        $query->applyScope();

        $countQuery = clone $query->model;

        $count = collect(
            DB::select("select count(id) as aggregate from ({$countQuery->select('products.id')->reorder('products.id')->toSql()}) c",
                $countQuery->getBindings())
        )->pluck('aggregate')->first();

        $items = [];

        $limit = $this->getPerPageLimit($params);

        $currentPage = Paginator::resolveCurrentPage('page');

        if ($count > 0) {
            $query->scopeQuery(function ($query) use ($currentPage, $limit) {
                return $query->forPage($currentPage, $limit);
            });

            $items = $query->get();
        }

        return new LengthAwarePaginator($items, $count, $limit, $currentPage, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);
    }

    /**
     * Search product from elastic search.
     *
     * @param  object  $seller
     * @return Collection
     */
    public function searchFromElastic($seller)
    {
        $params = request()->input();

        $currentPage = Paginator::resolveCurrentPage('page');

        $limit = $this->getPerPageLimit($params);

        $sortOptions = $this->getSortOptions($params);

        $indices = $this->elasticSearchRepository->search($params['category_id'] ?? null, [
            'type'  => $params['type'] ?? '',
            'from'  => ($currentPage * $limit) - $limit,
            'limit' => $limit,
            'sort'  => $sortOptions['sort'],
            'order' => $sortOptions['order'],
        ], $seller->seller_id);

        $query = $this->baseProductRepository->with([
            'attribute_family',
            'images',
            'videos',
            'attribute_values',
            'price_indices',
            'inventory_indices',
            'reviews',
        ])->scopeQuery(function ($query) use ($indices) {
            $qb = $query->distinct()
                ->whereIn('products.id', $indices['ids']);

            //Sort collection
            $qb->orderBy(DB::raw('FIELD(id, '.implode(',', $indices['ids']).')'));

            return $qb;
        });

        $items = $indices['total'] ? $query->get() : [];

        $results = new LengthAwarePaginator($items, $indices['total'], $limit, $currentPage, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);

        return $results;
    }

    /**
     * Fetch per page limit from toolbar helper. Adapter for this repository.
     */
    public function getPerPageLimit(array $params): int
    {
        return $this->toolbarHelper->getLimit($params);
    }

    /**
     * Fetch sort option from toolbar helper. Adapter for this repository.
     */
    public function getSortOptions(array $params): array
    {
        return $this->toolbarHelper->getOrder($params);
    }

    /**
     * Search Product by Attribute
     *
     * @return Collection
     */
    public function searchProducts($term)
    {
        $results = $this->productFlatRepository->scopeQuery(function ($query) use ($term) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            $productType = ['simple', 'configurable', 'virtual', 'downloadable'];

            return $query->distinct()
                ->addSelect('product_flat.*')
                ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                ->leftJoin('marketplace_products', 'products.id', '=', 'marketplace_products.product_id')
                ->whereIn('products.type', $productType)
                ->where('product_flat.status', 1)
                ->where('product_flat.channel', $channel)
                ->where('product_flat.locale', $locale)
                ->whereNotNull('product_flat.url_key')
                ->where('product_flat.name', 'like', '%'.$term.'%')
                ->where(function ($query) {
                    $query->where('marketplace_products.marketplace_seller_id', null)
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('marketplace_products.marketplace_seller_id', '!=', null)
                                ->where('marketplace_products.is_owner', 0)
                                ->where('marketplace_products.marketplace_seller_id', '!=', auth()->guard('seller')->id());
                        });
                })
                ->orderBy('product_id', 'desc');
        })->paginate(10);

        return $results;
    }

    /**
     * Returns seller by product
     *
     * @param  int  $productId
     * @return mixed
     */
    public function getSellerByProductId($productId)
    {
        $product = parent::findOneWhere([
            'product_id' => $productId,
            'is_owner'   => 1,
        ]);

        if (! $product) {
            return;
        }

        return $product->seller;
    }

    /**
     * Returns count of seller that selling the same product
     *
     * @param  Product  $product
     * @return int
     */
    public function getSellerCount($product)
    {
        return $this->scopeQuery(function ($query) use ($product) {
            return $query
                ->where('marketplace_products.product_id', $product->id)
                ->join('marketplace_sellers', 'marketplace_sellers.id', 'marketplace_products.marketplace_seller_id')
                ->where('marketplace_products.is_owner', 0)
                ->where('marketplace_sellers.is_suspended', 0)
                ->where('marketplace_products.is_approved', ProductEnum::APPROVED->value());
        })->count();
    }

    /**
     * Returns the seller products of the product
     *
     * @param  Product  $product
     * @return Collection
     */
    public function getSellerProducts($product)
    {
        return $this->findWhere([
            'product_id'  => $product->id,
            'is_owner'    => 0,
            'is_approved' => ProductEnum::APPROVED->value(),
        ]);
    }

    /**
     * Returns the seller products of the product id
     *
     * @param  \Webkul\Marketplace\Contracts\Product  $product
     */
    public function hasPermission($product)
    {
        if (
            ! $product
            || ! $product->is_approved
            || $product->seller->is_suspend
            || ! $product->seller->is_approved
            || ! core()->getConfigData('marketplace.settings.general.status')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Returns the seller products of the product id
     *
     * @param  int  $productId
     * @param  int  $sellerId
     * @return Collection
     */
    public function getMarketplaceProductByProduct($productId, $sellerId = null)
    {
        if ($sellerId) {
            $seller = $this->sellerRepository->find($sellerId);
        } else {
            if (auth()->guard('seller')->check()) {
                $seller = auth()->guard('seller')->user();
            } else {
                return;
            }
        }

        return $this->findOneWhere([
            'product_id'            => $productId,
            'marketplace_seller_id' => $seller->seller_id,
        ]);
    }

    public function searchProductsToAdd($term)
    {
        $results = $this->productFlatRepository->scopeQuery(function ($query) use ($term) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                ->addSelect('product_flat.*')
                ->leftJoin('marketplace_products', 'product_flat.product_id', '=', 'marketplace_products.product_id')
                ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                ->where('product_flat.status', 1)
                ->where('product_flat.channel', $channel)
                ->where('product_flat.locale', $locale)
                ->where('products.type', 'simple')
                ->whereNotNull('product_flat.url_key')
                ->where('product_flat.name', 'like', '%'.$term.'%')
                ->orderBy('product_id', 'desc');
        })->paginate(16);

        return $results;
    }
}
