<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\ProductFlag as ProductFlagContract;

class ProductFlag extends Model implements ProductFlagContract
{
    protected $table = 'marketplace_product_flags';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Add accessors and mutators for Seller Info field.
     */
    protected function sellerInfo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }
}
