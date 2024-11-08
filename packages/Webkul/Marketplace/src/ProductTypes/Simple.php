<?php

namespace Webkul\Marketplace\ProductTypes;

use Webkul\Checkout\Facades\Cart;
use Webkul\Product\Contracts\ProductInventoryIndex;
use Webkul\Product\Type\Simple as BaseSimple;
use Webkul\Marketplace\Repositories\ProductRepository;

class Simple extends BaseSimple
{
    /**
     * Returns product inventory index of current channel.
     *
     * @return ProductInventoryIndex
     */
    public function getInventoryIndex()
    {
        $indices = $this->product->inventory_indices->filter(function ($index) {
            return $index->channel_id === core()->getCurrentChannel()->id;
        });

        $sellerId = Cart::getCurrentProductSellerId();

        if (! $sellerId) {
            $sellerProduct = app(ProductRepository::class)->findOneWhere([
                'product_id' => $this->product->id,
                'is_owner'   => 1,
            ]);

            $vendorId = $sellerProduct ? $sellerProduct->marketplace_seller_id : 0;
        } else {
            $vendorId = $sellerId;
        }

        return $indices->firstWhere('vendor_id', $vendorId);
    }
}
