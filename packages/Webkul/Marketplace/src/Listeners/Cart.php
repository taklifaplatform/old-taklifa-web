<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Checkout\Facades\Cart as CartFacade;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Product\Repositories\ProductRepository as CoreProductRepository;

class Cart
{
    /**
     * Create a new customer event listener instance.
     */
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected ProductRepository $productRepository,
        protected CoreProductRepository $coreProductRepository
    ) {}

    /**
     * Product added to the cart
     *
     * @return void
     */
    public function cartItemAddBefore(int $productId)
    {
        $data = request()->all();

        if (isset($data['seller_info'])
            && ! $data['seller_info']['is_owner']
        ) {
            $sellerProduct = $this->productRepository->findOneWhere([
                'id'                    => $data['seller_info']['product_id'],
                'marketplace_seller_id' => $data['seller_info']['seller_id'],
            ]);
        } else {
            if (isset($data['selected_configurable_option'])) {
                $sellerProduct = $this->productRepository->findOneWhere([
                    'product_id' => $data['selected_configurable_option'],
                    'is_owner'   => 1,
                ]);
            } else {
                $sellerProduct = $this->productRepository->findOneWhere([
                    'product_id' => $productId,
                    'is_owner'   => 1,
                ]);
            }
        }

        if (! $sellerProduct) {
            return;
        }

        if (! isset($data['quantity'])) {
            $data['quantity'] = 1;
        }

        $product = $this->coreProductRepository->findOneByField('id', $productId);

        if ($cart = CartFacade::getCart()) {
            $cartItem = $cart->items()->where('product_id', $sellerProduct->product_id)->first();

            if ($cartItem) {
                if (! $sellerProduct->haveSufficientQuantity($data['quantity'])
                && $product->haveSufficientQuantity($data['quantity'])) {
                    return;
                } elseif (! $sellerProduct->haveSufficientQuantity($data['quantity'])) {

                    throw new \Exception('Requested quantity not available.');
                }

                $quantity = $cartItem->quantity + $data['quantity'];
            } else {
                $quantity = $data['quantity'];
            }
        } else {
            $quantity = $data['quantity'];
        }

        if (! $sellerProduct->haveSufficientQuantity((int) $quantity)
            && $product->haveSufficientQuantity((int) $quantity)
        ) {
            return;
        } elseif (! $sellerProduct->haveSufficientQuantity($quantity)) {
            throw new \Exception('Requested quantity not available.');
        }
    }

    /**
     * Product added to the cart
     */
    public function cartItemAddAfter($cart)
    {
        foreach ($cart->items as $item) {
            if (
                isset($item->additional['seller_info'])
                && ! $item->additional['seller_info']['is_owner']
            ) {
                $product = $this->productRepository->findOneWhere([
                    'marketplace_seller_id' => $item->additional['seller_info']['seller_id'],
                    'id'                    => $item->additional['seller_info']['product_id'],
                ]);

                if (! $product) {
                    continue;
                }

                $item->price = core()->convertPrice($product->price);

                $item->base_price = $product->price;
                $item->custom_price = $product->price;
                $item->total = core()->convertPrice($product->price * $item->quantity);
                $item->base_total = $product->price * $item->quantity;

                if ($item->product->type == 'downloadable') {
                    foreach ($product->downloadable_links as $link) {
                        if (! in_array($link->id, $item->additional['links'])) {
                            continue;
                        }

                        $item->price += core()->convertPrice($link->price);
                        $item->base_price += $link->price;
                        $item->custom_price += $link->price;
                        $item->total += (core()->convertPrice($link->price) * $item->quantity);
                        $item->base_total += ($link->price * $item->quantity);
                    }
                }

                $item->save();
            }
        }
    }

    /**
     * Collect totals before
     */
    public function collectTotalsBefore()
    {
        $cart = CartFacade::getCart();

        $cartItems = $cart->items;

        foreach ($cartItems as $item) {
            if (
                isset($items->additional['seller_info'])
                && ! $items->additional['seller_info']['is_owner']
            ) {
                $product = $this->productRepository->findOneWhere([
                    'marketplace_seller_id' => $item->additional['seller_info']['seller_id'],
                    'id'                    => $item->additional['seller_info']['product_id'],
                ]);

                if (! $this->productRepository->hasPermission($product)) {
                    CartFacade::removeItem($item->id);
                }
            }
        }
    }
}
