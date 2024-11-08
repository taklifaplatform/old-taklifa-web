<?php

namespace Webkul\Marketplace\Models;

use Webkul\Product\Models\ProductOrderedInventory as BaseProductOrderedInventory;

class ProductOrderedInventory extends BaseProductOrderedInventory
{
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'qty',
        'product_id',
        'channel_id',
        'vendor_id',
    ];
}
