<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Marketplace\Contracts\Product as ProductContract;
use Webkul\Product\Models\Product as BaseProduct;
use Webkul\Product\Models\ProductFlat;
use Webkul\Product\Models\ProductInventoryIndexProxy;

class Product extends Model implements ProductContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marketplace_products';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * Get the product that belongs to the product.
     */
    public function product()
    {
        return $this->belongsTo(BaseProduct::class);
    }

    /**
     * Get the product_flat that belongs to the product.
     */
    public function product_flats()
    {
        return $this->hasMany(ProductFlat::class, 'product_id', 'product_id');
    }

    /**
     * Get the product that belongs to the seller.
     */
    public function seller()
    {
        return $this->belongsTo(SellerProxy::modelClass(), 'marketplace_seller_id');
    }

    /**
     * Get the product variants that owns the product.
     */
    public function variants()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the product that owns the product.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * The images that belong to the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImageProxy::modelClass(), 'marketplace_product_id');
    }

    /**
     * Videos that belong to the product.
     */
    public function videos()
    {
        return $this->hasMany(ProductVideoProxy::modelClass(), 'marketplace_product_id');
    }

    /**
     * @param  string  $key
     * @return bool
     */
    public function isSaleable()
    {
        if (! $this->product->status
            || ! $this->is_approved
        ) {
            return false;
        }

        if ($this->haveSufficientQuantity(1)) {
            return true;
        }

        if ($this->product->downloadable_links()->count()) {
            return true;
        }

        return false;
    }

    /**
     * Check seller's product has sufficient inventory or not.
     */
    public function haveSufficientQuantity(int $qty): bool
    {
        $sellerInventory = $this->inventory_indices
            ->where('channel_id', core()->getCurrentChannel()->id)
            ->where('vendor_id', $this->seller->id)
            ->first();

        return $qty <= $sellerInventory?->qty ?? 0 ? true : false;
    }

    /**
     * Get the inventory indices that seller's own & assign product.
     */
    public function inventory_indices(): HasMany
    {
        return $this->hasMany(ProductInventoryIndexProxy::modelClass(), 'product_id', 'product_id')
            ->where('vendor_id', '!=', 0);
    }

    /**
     * The images that belong to the product.
     */
    public function downloadable_samples()
    {
        return $this->hasMany(MpProductDownloadableSampleProxy::modelClass());
    }

    /**
     * The images that belong to the product.
     */
    public function downloadable_links()
    {
        return $this->hasMany(MpProductDownloadableLinkProxy::modelClass());
    }
}
