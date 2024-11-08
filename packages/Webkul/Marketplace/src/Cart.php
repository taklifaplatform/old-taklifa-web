<?php

namespace Webkul\Marketplace;

use Illuminate\Support\Facades\Event;
use Webkul\Checkout\Cart as BaseCart;
use Webkul\Checkout\Contracts\Cart as CartContract;
use Webkul\Checkout\Contracts\CartItem as ContractCartItem;
use Webkul\Product\Contracts\Product as ProductContract;
use Webkul\Tax\Facades\Tax;

class Cart extends BaseCart
{
    public $sellerId = 0;

    /**
     * Check current product seller product.
     */
    public function getCurrentProductSellerId(): int
    {
        return $this->sellerId;
    }

    /**
     * Get cart item by product.
     */
    public function getItemByProduct(array $data, ?array $parentData = null): ?ContractCartItem
    {
        $items = $this->getCart()->all_items;

        foreach ($items as $item) {
            if ($item->getTypeInstance()->compareOptions($item->additional, $data['additional'])) {
                if (! isset($data['additional']['parent_id'])
                    || $item->parent->getTypeInstance()->compareOptions($item->parent->additional, $parentData ?: request()->all())
                ) {
                    return $item;
                }
            } elseif (isset($data['additional']['seller_info'])
                && isset($item->additional['seller_info'])
            ) {
                if (($data['additional']['seller_info']['product_id'] == $item->additional['seller_info']['product_id'])
                    && ($data['additional']['seller_info']['seller_id'] == $item->additional['seller_info']['seller_id'])
                ) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * Add items in a cart with some cart and item details.
     */
    public function addProduct(ProductContract $product, array $data): CartContract|\Exception
    {
        $this->sellerId = 0;

        Event::dispatch('checkout.cart.add.before', $product->id);

        if (! $cart = $this->getCart()) {
            $cart = $this->createCart([]);
        }

        if (! empty($data['seller_info'])) {
            $this->sellerId = $data['seller_info']['seller_id'];

            $data['seller_info'] = [
                'product_id'    => isset($data['selected_configurable_option']) ? $data['selected_configurable_option'] : $data['seller_info']['product_id'],
                'seller_id'     => $data['seller_info']['seller_id'],
                'is_owner'      => 0,
            ];

            $sellerOwnerProduct = app('Webkul\Marketplace\Repositories\ProductRepository')->findOneWhere([
                'product_id'            => $product->id,
                'is_owner'              => 1,
                'marketplace_seller_id' => $data['seller_info']['seller_id'],
            ]);
        } else {
            $sellerOwnerProduct = app('Webkul\Marketplace\Repositories\ProductRepository')->findOneWhere([
                'product_id'    => $product->id,
                'is_owner'      => 1,
            ]);
        }

        if (! empty($sellerOwnerProduct)) {
            $this->sellerId = $sellerOwnerProduct->marketplace_seller_id;

            $data['seller_info'] = [
                'product_id'    => isset($data['selected_configurable_option']) ? $data['selected_configurable_option'] : $product->id,
                'seller_id'     => $sellerOwnerProduct->marketplace_seller_id,
                'is_owner'      => 1,
            ];
        }

        $cartProducts = $product->getTypeInstance()->prepareForCart($data);

        if (is_string($cartProducts)) {
            if (! $cart->all_items->count()) {
                $this->removeCart($cart);
            } else {
                $this->collectTotals();
            }

            throw new \Exception($cartProducts);
        } else {
            $parentCartItem = null;

            foreach ($cartProducts as $cartProduct) {
                $cartItem = $this->getItemByProduct($cartProduct, $data);

                if (isset($cartProduct['parent_id'])) {
                    $cartProduct['parent_id'] = $parentCartItem->id;
                }

                if (! $cartItem) {
                    $cartItem = $this->cartItemRepository->create(array_merge($cartProduct, ['cart_id' => $cart->id]));
                } else {
                    if (
                        isset($cartProduct['parent_id'])
                        && $cartItem->parent_id !== $parentCartItem->id
                    ) {
                        $cartItem = $this->cartItemRepository->create(array_merge($cartProduct, [
                            'cart_id' => $cart->id,
                        ]));
                    } else {
                        $cartItem = $this->cartItemRepository->update($cartProduct, $cartItem->id);
                    }
                }

                if (! $parentCartItem) {
                    $parentCartItem = $cartItem;
                }
            }
        }

        $this->collectTotals();

        Event::dispatch('checkout.cart.add.after', $cart);

        return $this->getCart();
    }

    /**
     * Update cart items information.
     */
    public function updateItems(array $data): bool|\Exception
    {
        foreach ($data['qty'] as $itemId => $quantity) {
            $item = $this->cartItemRepository->find($itemId);

            if (! $item) {
                continue;
            }

            $this->sellerId = empty($item->additional['seller_info']) ? 0 : $item->additional['seller_info']['seller_id'];

            if (! $item->product->status) {
                throw new \Exception(__('shop::app.checkout.cart.inactive'));
            }

            if ($quantity <= 0) {
                $this->removeItem($itemId);

                throw new \Exception(__('shop::app.checkout.cart.illegal'));
            }

            $item->quantity = $quantity;

            if (! $this->isItemHaveQuantity($item)) {
                throw new \Exception(__('shop::app.checkout.cart.inventory-warning'));
            }

            Event::dispatch('checkout.cart.update.before', $item);

            $this->cartItemRepository->update([
                'quantity'            => $quantity,
                'total'               => $total = core()->convertPrice($item->price_incl_tax * $quantity),
                'total_incl_tax'      => $total,
                'base_total'          => $item->price_incl_tax * $quantity,
                'base_total_incl_tax' => $item->base_price_incl_tax * $quantity,
                'total_weight'        => $item->weight * $quantity,
                'base_total_weight'   => $item->weight * $quantity,
            ], $itemId);

            Event::dispatch('checkout.cart.update.after', $item);
        }

        $this->collectTotals();

        return true;
    }

    /**
     * Calculates cart items tax.
     */
    public function calculateItemsTax(): void
    {
        if (! $cart = $this->getCart()) {
            $cart = $this->createCart([]);
        }

        Event::dispatch('checkout.cart.calculate.items.tax.before', $cart);

        $taxCategories = [];

        foreach ($cart->items as $key => $item) {
            $taxCategoryId = $item->tax_category_id;

            if (empty($taxCategoryId)) {
                $taxCategoryId = $item->product->tax_category_id;
            }

            if (empty($taxCategoryId)) {
                $taxCategoryId = core()->getConfigData('sales.taxes.categories.product');
            }

            if (empty($taxCategoryId)) {
                continue;
            }

            if (! isset($taxCategories[$taxCategoryId])) {
                $taxCategories[$taxCategoryId] = $this->taxCategoryRepository->find($taxCategoryId);
            }

            if (! $taxCategories[$taxCategoryId]) {
                continue;
            }

            $calculationBasedOn = core()->getConfigData('sales.taxes.calculation.based_on');

            $address = null;

            if ($calculationBasedOn == self::TAX_CALCULATION_BASED_ON_SHIPPING_ORIGIN) {
                $address = Tax::getShippingOriginAddress();
            } elseif ($calculationBasedOn == self::TAX_CALCULATION_BASED_ON_SHIPPING_ADDRESS) {
                if ($item->getTypeInstance()->isStockable()) {
                    $address = $cart->shipping_address;
                } else {
                    $address = $cart->billing_address;
                }
            } elseif ($calculationBasedOn == self::TAX_CALCULATION_BASED_ON_BILLING_ADDRESS) {
                $address = $cart->billing_address;
            }

            if ($address === null && $cart->customer) {
                $address = $cart->customer->addresses()
                    ->where('default_address', 1)->first();
            }

            if ($address === null) {
                $address = Tax::getDefaultAddress();
            }

            $item->applied_tax_rate = null;

            $item->tax_percent = $item->tax_amount = $item->base_tax_amount = 0;

            Tax::isTaxApplicableInCurrentAddress($taxCategories[$taxCategoryId], $address, function ($rate) use ($item, $taxCategoryId) {
                $item->applied_tax_rate = $rate->identifier;

                $item->tax_category_id = $taxCategoryId;

                $item->tax_percent = $rate->tax_rate;

                if (Tax::isInclusiveTaxProductPrices()) {
                    $item->tax_amount = round(($item->total_incl_tax * $rate->tax_rate) / 100, 4);

                    $item->base_tax_amount = round(($item->base_total_incl_tax * $rate->tax_rate) / 100, 4);

                    $item->total = $item->total_incl_tax - $item->tax_amount;

                    $item->base_total = $item->base_total_incl_tax - $item->base_tax_amount;

                    $item->price = $item->total / $item->quantity;

                    $item->base_price = $item->base_total / $item->quantity;
                } else {
                    $item->tax_amount = round(($item->total * $rate->tax_rate) / 100, 4);

                    $item->base_tax_amount = round(($item->base_total * $rate->tax_rate) / 100, 4);

                    $item->total_incl_tax = $item->total + $item->tax_amount;

                    $item->base_total_incl_tax = $item->base_total + $item->base_tax_amount;

                    $item->price_incl_tax = $item->price + ($item->tax_amount / $item->quantity);

                    $item->base_price_incl_tax = $item->base_price + ($item->base_tax_amount / $item->quantity);
                }
            });

            if (empty($item->applied_tax_rate)) {
                $item->price_incl_tax = $item->price;
                $item->base_price_incl_tax = $item->base_price;

                $item->total_incl_tax = $item->total;
                $item->base_total_incl_tax = $item->base_total;
            }

            $item->save();

            $cart->items->put($key, $item);
        }

        Event::dispatch('checkout.cart.calculate.items.tax.after', $cart);
    }

    /**
     * Calculates cart shipping tax.
     */
    public function calculateShippingTax(): void
    {
        if (! $cart = $this->getCart()) {
            $cart = $this->createCart([]);
        }

        $shippingRate = $cart->selected_shipping_rate;

        if (! $shippingRate) {
            return;
        }

        if (! $taxCategoryId = core()->getConfigData('sales.taxes.categories.shipping')) {
            return;
        }

        $taxCategory = $this->taxCategoryRepository->find($taxCategoryId);

        $calculationBasedOn = core()->getConfigData('sales.taxes.calculation.based_on');

        $address = null;

        if ($calculationBasedOn == self::TAX_CALCULATION_BASED_ON_SHIPPING_ORIGIN) {
            $address = Tax::getShippingOriginAddress();
        } elseif (
            $cart->haveStockableItems()
            && $calculationBasedOn == self::TAX_CALCULATION_BASED_ON_SHIPPING_ADDRESS
        ) {
            $address = $cart->shipping_address;
        } elseif ($calculationBasedOn == self::TAX_CALCULATION_BASED_ON_BILLING_ADDRESS) {
            $address = $cart->billing_address;
        }

        if ($address === null && $cart->customer) {
            $address = $cart->customer->addresses()
                ->where('default_address', 1)->first();
        }

        if ($address === null) {
            $address = Tax::getDefaultAddress();
        }

        Event::dispatch('checkout.cart.calculate.shipping.tax.before', $cart);

        Tax::isTaxApplicableInCurrentAddress($taxCategory, $address, function ($rate) use ($shippingRate) {
            $shippingRate->applied_tax_rate = $rate->identifier;

            $shippingRate->tax_percent = $rate->tax_rate;

            if (Tax::isInclusiveTaxShippingPrices()) {
                $shippingRate->tax_amount = round(($shippingRate->price_incl_tax * $rate->tax_rate) / 100);

                $shippingRate->base_tax_amount = round(($shippingRate->base_price_incl_tax * $rate->tax_rate) / 100);

                $shippingRate->price = $shippingRate->price_incl_tax - $shippingRate->tax_amount;

                $shippingRate->base_price = $shippingRate->base_price_incl_tax - $shippingRate->base_tax_amount;
            } else {
                $shippingRate->tax_amount = round(($shippingRate->price * $rate->tax_rate) / 100, 4);

                $shippingRate->base_tax_amount = round(($shippingRate->base_price * $rate->tax_rate) / 100, 4);

                $shippingRate->price_incl_tax = $shippingRate->price + $shippingRate->tax_amount;

                $shippingRate->base_price_incl_tax = $shippingRate->base_price + $shippingRate->base_tax_amount;
            }
        });

        if (empty($shippingRate->applied_tax_rate)) {
            $shippingRate->price_incl_tax = $shippingRate->price;

            $shippingRate->base_price_incl_tax = $shippingRate->base_price;
        }

        $shippingRate->save();

        Event::dispatch('checkout.cart.calculate.shipping.tax.after', $cart);
    }
}
