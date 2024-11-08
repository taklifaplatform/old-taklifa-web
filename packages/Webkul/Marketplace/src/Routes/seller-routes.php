<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\CustomerController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\DashboardController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\MegaSearchController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Orders\InvoiceController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Orders\OrderController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Orders\PaymentRequestController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Orders\ShipmentController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\ProductReviewController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Products\AssignProductController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Products\ProductController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\ProfileController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\RoleController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\SellerInfoController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\SellerReviewController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\SessionController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\TransactionController;
use Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\UserController;

Route::group([
    'middleware' => ['locale', 'theme', 'currency', 'marketplace', 'seller'],
    'prefix'     => 'seller/account',
], function () {
    Route::controller(MegaSearchController::class)->prefix('mega-search')->group(function () {
        Route::get('products', 'products')->name('marketplace.account.mega_search.products');

        Route::get('orders', 'orders')->name('marketplace.account.mega_search.orders');

        Route::get('customers', 'customers')->name('marketplace.account.mega_search.customers');
    });

    Route::delete('logout', [SessionController::class, 'destroy'])->name('marketplace.seller.session.destroy');

    Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
        Route::get('', 'index')->name('shop.marketplace.seller.account.dashboard.index');

        Route::get('stats', 'stats')->name('shop.marketplace.seller.account.dashboard.stats');
    });

    Route::prefix('orders')->group(function () {
        Route::controller(OrderController::class)->group(function () {
            Route::get('', 'index')->name('shop.marketplace.seller.account.orders.index');

            Route::get('view/{order_id}', 'view')->name('shop.marketplace.seller.account.orders.view');

            Route::get('cancel/{order_id}', 'cancel')->name('shop.marketplace.seller.account.orders.cancel');
        });

        Route::controller(InvoiceController::class)->prefix('invoices')->group(function () {
            Route::post('create/{id}', 'store')->name('shop.marketplace.seller.account.invoices.store');

            Route::get('print/{id}', 'print')->name('shop.marketplace.seller.account.invoices.print');
        });

        Route::post('shipments/create/{id}', [ShipmentController::class, 'store'])->name('shop.marketplace.seller.account.shipments.store');

        Route::get('payment/request/{id}', [PaymentRequestController::class, 'requestPayment'])->name('shop.marketplace.seller.account.payment.request');
    });

    Route::controller(TransactionController::class)->prefix('transactions')->group(function () {
        Route::get('', 'index')->name('shop.marketplace.seller.account.transaction.index');

        Route::get('view/{id}', 'view')->name('shop.marketplace.seller.account.transaction.view');

        Route::get('print/{id}', 'print')->name('shop.marketplace.seller.account.transaction.print');
    });

    Route::prefix('products')->group(function () {
        Route::controller(ProductController::class)->group(function () {
            Route::get('', 'index')->name('shop.marketplace.seller.account.products.index');

            Route::get('create', 'create')->name('shop.marketplace.seller.account.products.create');

            Route::get('search', 'search')->name('marketplace.account.products.search');

            Route::get('search-simple', 'searchSimple')->name('marketplace.account.products.search_simple');

            Route::post('create', 'store')->name('marketplace.account.products.store');

            Route::get('edit/{id}', 'edit')->name('marketplace.account.products.edit');

            Route::put('edit/{id}', 'update')->name('marketplace.account.products.update');

            Route::delete('edit/{id}', 'destroy')->name('marketplace.account.products.delete');

            Route::post('mass-delete', 'massDestroy')->name('marketplace.account.products.mass_delete');

            /**
             * Upload file if seller is owner.
             */
            Route::post('upload-file/{id}', 'uploadLink')->name('marketplace.account.products.upload_link');

            Route::post('upload-sample/{id}', 'uploadSample')->name('marketplace.account.products.upload_sample');
        });

        Route::controller(AssignProductController::class)->prefix('assign')->group(function () {
            Route::get('{id?}', 'create')->name('marketplace.account.products.assign.create');

            Route::get('edit/{id}', 'edit')->name('marketplace.account.products.assign.edit');

            Route::post('{id?}', 'store')->name('marketplace.account.products.assign.store');

            Route::put('{id}', 'update')->name('marketplace.account.products.assign.update');

            Route::post('upload-file/{id}', 'uploadLink')->name('marketplace.account.products.assign.upload_link');

            Route::post('upload-sample/{id}', 'uploadSample')->name('marketplace.account.products.assign.upload_sample');
        });
    });

    Route::get('customers', [CustomerController::class, 'index'])->name('shop.marketplace.seller.account.customers.index');

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('', 'index')->name('shop.marketplace.seller.account.profile.index');

        Route::put('{id}', 'update')->name('shop.marketplace.seller.account.profile.update');
    });

    Route::controller(ProductReviewController::class)->prefix('product-reviews')->group(function () {
        Route::get('', 'index')->name('shop.marketplace.seller.account.products.review');

        Route::post('mass-update', 'massUpdate')->name('shop.marketplace.seller.account.products.review.mass_update');
    });

    Route::get('seller-info', [SellerInfoController::class, 'index'])->name('shop.marketplace.seller.account.seller_info');

    Route::get('seller-reviews', [SellerReviewController::class, 'index'])->name('shop.marketplace.seller.account.seller_reviews.index');

    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('', 'index')->name('shop.marketplace.seller.account.roles.index');

        Route::get('create', 'create')->name('shop.marketplace.seller.account.roles.create');

        Route::post('create', 'store')->name('shop.marketplace.seller.account.roles.store');

        Route::get('edit/{id}', 'edit')->name('shop.marketplace.seller.account.roles.edit');

        Route::put('edit/{id}', 'update')->name('shop.marketplace.seller.account.roles.update');

        Route::delete('edit/{id}', 'destroy')->name('shop.marketplace.seller.account.roles.delete');
    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('', 'index')->name('shop.marketplace.seller.account.users.index');

        Route::post('', 'store')->name('shop.marketplace.seller.account.users.store');

        Route::get('edit/{id}', 'edit')->name('shop.marketplace.seller.account.users.edit');

        Route::put('edit', 'update')->name('shop.marketplace.seller.account.users.update');

        Route::delete('edit/{id}', 'destroy')->name('shop.marketplace.seller.account.users.delete');
    });
});
