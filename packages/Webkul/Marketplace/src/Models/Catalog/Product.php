<?php

namespace Webkul\Marketplace\Models\Catalog;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Marketplace\Models\Product as MpProduct;
use Webkul\Product\Models\Product as BaseProduct;

class Product extends BaseProduct
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'attribute_family_id',
        'sku',
        'parent_id',
        'additional',
    ];

    /**
     * Get the marketplace products that belongs to the product.
     */
    public function mp_products(): HasMany
    {
        return $this->hasMany(MpProduct::class);
    }

    /**
     * Get inventory source quantity.
     *
     * @return bool
     */
    public function mp_inventory_source_qty($inventorySourceId)
    {
        return $this->inventories()
            ->where('inventory_source_id', $inventorySourceId)
            ->where('vendor_id', auth()->guard('seller')->id())
            ->sum('qty');
    }
}
