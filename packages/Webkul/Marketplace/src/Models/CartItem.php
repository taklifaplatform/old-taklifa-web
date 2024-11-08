<?php

namespace Webkul\Marketplace\Models;

use Webkul\Checkout\Models\CartItem as BaseCartItem;
use Webkul\Product\Type\AbstractType;

class CartItem extends BaseCartItem
{
    /**
     * Retrieve type instance
     */
    public function getTypeInstance(): AbstractType
    {
        if ($this->typeInstance) {
            return $this->typeInstance;
        }

        $this->typeInstance = app(config('product_types.'.$this->type.'.class'));

        if ($this->product) {
            $this->typeInstance->setProduct($this->getCartItemModifyProduct());
        }

        return $this->typeInstance;
    }

    public function getCartItemModifyProduct()
    {
        if (! empty($this->additional['seller_info'])) {
            $this->product->seller_info = $this->additional['seller_info'];
        }

        return $this->product;
    }
}
