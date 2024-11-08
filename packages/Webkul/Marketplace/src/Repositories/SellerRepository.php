<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\Seller;
use Webkul\Product\Repositories\ProductInventoryRepository;

class SellerRepository extends Repository
{
    /**
     * Create a new repository instance.
     */
    public function __construct(
        App $app,
        protected OrderItemRepository $orderItemRepository,
        protected ProductInventoryRepository $productInventoryRepository
    ) {
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Seller::class;
    }

    /**
     * Retrieve seller from url
     */
    public function findByUrlOrFail(string $url)
    {
        if ($seller = $this->where('url', $url)->where('is_suspended', 0)->first()) {
            return $seller;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($this->model),
            $url
        );
    }

    /**
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $seller = $this->findOrFail($id);

        parent::update($data, $id);

        $this->uploadImages($data, $seller, 'logo');

        $this->uploadImages($data, $seller, 'banner');

        return $seller;
    }

    /**
     * Upload seller images.
     */
    public function uploadImages(array $data, object $seller, string $type): void
    {
        if (isset($data[$type])) {
            $request = request();

            foreach ($data[$type] as $imageId => $image) {
                $file = $type.'.'.$imageId;
                $dir = 'seller/'.$seller->seller_id;

                if ($request->hasFile($file)) {
                    if ($seller->{$type}) {
                        Storage::delete($seller->{$type});
                    }

                    $seller->{$type} = $request->file($file)->store($dir);
                    $seller->save();
                }
            }
        } else {
            if ($seller->{$type}) {
                Storage::delete($seller->{$type});
            }

            $seller->{$type} = null;
            $seller->save();
        }
    }

    /**
     * Returns top six popular sellers
     *
     * @return Collection
     */
    public function getPopularSellers()
    {
        return $this->getModel()
            ->leftJoin('marketplace_orders', 'marketplace_sellers.id', 'marketplace_orders.marketplace_seller_id')
            ->leftJoin('marketplace_seller_reviews', function ($join) {
                $join->on('marketplace_seller_reviews.marketplace_seller_id', 'marketplace_sellers.id')
                    ->where('marketplace_seller_reviews.status', 'approved');
            })
            ->select('marketplace_sellers.*')
            ->addSelect(
                DB::raw('SUM(total_qty_ordered) as total_qty_ordered'),
                DB::raw('AVG(rating) as avg_rating'),
                DB::raw('COUNT(DISTINCT marketplace_seller_reviews.id) as total_rating')
            )
            ->groupBy('marketplace_sellers.id')
            ->where('marketplace_sellers.shop_title', '<>', null)
            ->whereNull('marketplace_sellers.parent_id')
            ->where('marketplace_sellers.is_approved', 1)
            ->where('marketplace_sellers.is_suspended', 0)
            ->orderBy('total_qty_ordered', 'DESC')
            ->limit(6)
            ->get();
    }

    /**
     * @return mixed
     */
    public function deleteInventory(int $id)
    {
        $inventories = $this->productInventoryRepository->findWhere([
            'vendor_id' => $id,
        ]);

        if (count($inventories)) {
            foreach ($inventories as $inventory) {
                if (isset($inventory)) {
                    $this->productInventoryRepository->delete($inventory->id);
                }
            }
        }
    }

    /**
     * Returns seller allowed Product Types
     *
     * @return object
     */
    public function getAllowedProducts($seller = null)
    {
        $seller = $seller ?? auth()->guard('seller')->user();

        return collect(config('product_types'))->only($seller->allowed_product_types ?: []);
    }

    /**
     * Count sellers with all access.
     */
    public function countSellersWithAllAccess(object $seller): int
    {
        return $this->getModel()
            ->leftJoin('marketplace_roles', 'marketplace_sellers.marketplace_role_id', '=', 'marketplace_roles.id')
            ->where('marketplace_roles.permission_type', 'all')
            ->where('marketplace_roles.marketplace_seller_id', $seller->seller_id)
            ->count();
    }
}
