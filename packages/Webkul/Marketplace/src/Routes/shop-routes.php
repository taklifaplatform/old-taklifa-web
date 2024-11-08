<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\Shop\CartController;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Http\Controllers\Shop\FlagController;
use Webkul\Marketplace\Http\Controllers\Shop\MarketplaceController;
use Webkul\Marketplace\Http\Controllers\Shop\ProductController;
use Webkul\Marketplace\Http\Controllers\Shop\ProductFlagController;
use Webkul\Marketplace\Http\Controllers\Shop\ReviewController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\ForgotPasswordController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\RegistrationController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\ResetPasswordController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\SessionController;
use Webkul\Marketplace\Http\Controllers\Shop\SellerController;

Route::group(['middleware' => ['theme', 'locale', 'currency', 'marketplace']], function () {
    Route::prefix('seller')->group(function () {
        /**
         * Redirect route.
         */
        Route::get('', [Controller::class, 'redirectToLogin']);

        Route::controller(SessionController::class)->prefix('login')->group(function () {
            Route::get('', 'show')->name('marketplace.seller.session.index');

            Route::post('', 'create')->name('marketplace.seller.session.create');
        });

        Route::controller(RegistrationController::class)->prefix('register')->group(function () {
            Route::get('', 'index')->name('marketplace.seller.register.create');

            Route::post('', 'store')->name('marketplace.seller.register.store');
        });

        Route::controller(ForgotPasswordController::class)->prefix('forgot-password')->group(function () {
            Route::get('', 'create')->name('marketplace.seller.forgot_password.create');

            Route::post('', 'store')->name('marketplace.seller.forgot_password.store');
        });

        Route::controller(ResetPasswordController::class)->prefix('reset-password')->group(function () {
            Route::get('{token}', 'create')->name('marketplace.seller.reset_password.create');

            Route::post('', 'store')->name('marketplace.seller.reset_password.store');
        });
    });

    Route::prefix('marketplace')->group(function () {
        Route::controller(MarketplaceController::class)->group(function () {
            Route::get('', 'index')->name('marketplace.seller_central.index')->middleware('cacheResponse');

            Route::get('/popular-sellers', 'getPopularSellers')->name('marketplace.seller_central.popular_sellers');
        });

        /**
         * Add cart items.
         */
        Route::controller(CartController::class)->prefix('checkout/cart/add')->group(function () {
            Route::post('{id}', 'add')->name('marketplace.cart.add');

            Route::post('', 'store')->name('marketplace.api.cart.store');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('products/{id}/offers', 'offers')->name('marketplace.product.offers.index');

            Route::get('/downloadable/download-sample/{type}/{id}', 'downloadSample')->name('marketplace.downloadable.download_sample');

            Route::get('seller/{url}/products', 'index')->name('marketplace.products.index');

            Route::get('top-selling-products/{sellerId}', 'topSellingProducts')->name('marketplace.seller.top_selling_products');
        });

        /**
         * Seller related routes.
         */
        Route::controller(SellerController::class)->prefix('seller')->group(function () {
            Route::get('profile/{url}', 'show')->name('marketplace.seller.show');

            Route::post('contact', 'contact')->name('marketplace.seller.contact');
        });

        /**
         * Seller flag routes.
         */
        Route::post('flag', [FlagController::class, 'store'])->name('marketplace.seller.flag.store');

        /**
         * Product flag routes.
         */
        Route::post('product-flag', [ProductFlagController::class, 'store'])->name('marketplace.product.flag.store');

        Route::controller(ReviewController::class)->prefix('reviews')->group(function () {
            Route::get('{url}', 'index')->name('marketplace.seller.reviews.index');

            Route::post('create', 'store')->name('marketplace.seller.reviews.store');
        });
    });
});
