<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Checkout\Facades\Cart;
use Webkul\Marketplace\Repositories\SellerRepository;

class Onepage
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository
    ) {}

    public function index()
    {
        if (core()->getConfigData('marketplace.settings.general.status')
            && core()->getConfigData('marketplace.settings.general.enable_minimum_order_amount')
        ) {
            if (! $this->checkCartTotal()) {
                return back();
            }
        }

        if (! auth()->guard('customer')->check()
            && ! core()->getConfigData('catalog.products.guest_checkout.allow_guest_checkout')
        ) {
            return redirect()->route('shop.customer.session.index');
        }
    }

    public function checkCartTotal()
    {
        $cart = Cart::getCart();

        if ($cart) {
            $productsAmount = [];

            foreach ($cart->items as $item) {
                if (
                    ! empty($item->additional['seller_info'])
                    && ! empty($item->additional['seller_info']['seller_id'])
                ) {
                    if (array_key_exists($item->additional['seller_info']['seller_id'], $productsAmount)) {
                        $productsAmount[$item->additional['seller_info']['seller_id']] += $item->total;
                    } else {
                        $productsAmount[$item->additional['seller_info']['seller_id']] = $item->total;
                    }
                }
            }

            $sellers = $this->sellerRepository->findWhereIn('id', array_keys($productsAmount));

            foreach ($sellers as $seller) {
                if (
                    $seller->min_order_amount
                    && $productsAmount[$seller->seller_id] < $seller->min_order_amount
                ) {
                    session()->flash('warning', trans('marketplace::app.shop.checkout.cart.minimum-order-message', [
                        'shop_title' => $seller->shop_title,
                        'amount'     => core()->currency($seller->min_order_amount),
                    ]));

                    return false;
                }
            }

            return true;
        }
    }
}
