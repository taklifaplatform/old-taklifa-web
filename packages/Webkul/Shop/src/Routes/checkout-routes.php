<?php

use Illuminate\Support\Facades\Route;
use Webkul\Shop\Http\Controllers\CartController;
use Webkul\Shop\Http\Controllers\QuoteController;
use Webkul\Shop\Http\Controllers\OnepageController;
use Webkul\Shop\Http\Controllers\CalculateController;

Route::group(['middleware' => ['locale', 'theme', 'currency']], function () {
    Route::get('calculate', [CalculateController::class, 'calculateCost'])->name('calculate');
    /**
     * Cart routes.
     */
    Route::controller(CartController::class)->prefix('checkout/cart')->group(function () {
        Route::get('', 'index')->name('shop.checkout.cart.index');
    });

    /**
     * Quote routes.
     */
    Route::controller(QuoteController::class)->prefix('checkout/quote')->group(function () {
        Route::get('', 'index')->name('shop.checkout.quote.index');
        Route::get('address', 'address')->name('shop.checkout.quote.address');
        Route::get('generate/{order}', 'generateQuotePDF')->name('shop.checkout.quote.pdf');
    });

    Route::controller(OnepageController::class)->prefix('checkout/onepage')->group(function () {
        Route::get('', 'index')->name('shop.checkout.onepage.index');
        Route::get('success', 'success')->name('shop.checkout.onepage.success');
    });
});
