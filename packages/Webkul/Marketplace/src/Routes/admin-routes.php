<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\Admin\OrderController;
use Webkul\Marketplace\Http\Controllers\Admin\PaymentRequestController;
use Webkul\Marketplace\Http\Controllers\Admin\ProductController;
use Webkul\Marketplace\Http\Controllers\Admin\ProductFlagReasonController;
use Webkul\Marketplace\Http\Controllers\Admin\ProductReviewsController;
use Webkul\Marketplace\Http\Controllers\Admin\SellerCategoryController;
use Webkul\Marketplace\Http\Controllers\Admin\SellerController;
use Webkul\Marketplace\Http\Controllers\Admin\SellerFlagReasonController;
use Webkul\Marketplace\Http\Controllers\Admin\SellerReviewController;
use Webkul\Marketplace\Http\Controllers\Admin\TransactionController;

Route::group([
    'middleware' => ['admin', 'marketplace'],
    'prefix'     => config('app.admin_url').'/marketplace',
], function () {
    Route::controller(SellerController::class)->prefix('sellers')->group(function () {
        Route::get('', 'index')->name('admin.marketplace.sellers.index');

        Route::post('create', 'store')->name('admin.marketplace.sellers.store');

        Route::get('edit/{id}', 'edit')->name('admin.marketplace.sellers.edit');

        Route::put('edit/{id}', 'update')->name('admin.marketplace.sellers.update');

        Route::delete('delete/{id}', 'destroy')->name('admin.marketplace.sellers.delete');

        Route::post('mass-update', 'massUpdate')->name('admin.marketplace.sellers.mass_update');

        Route::post('mass-delete', 'massDestroy')->name('admin.marketplace.sellers.mass_delete');

        Route::prefix('product')->group(function () {
            Route::get('search/{id}', 'search')->name('admin.marketplace.sellers.products.search');

            Route::prefix('assign/{seller_id}/{product_id?}')->group(function () {

                Route::get('', 'assignProduct')->name('admin.marketplace.sellers.products.assign');

                Route::post('', 'saveAssignProduct')->name('admin.marketplace.sellers.products.save_assign');
            });
        });
    });

    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('', 'index')->name('admin.marketplace.products.index');

        Route::delete('delete/{id}', 'destroy')->name('admin.marketplace.products.delete');

        Route::post('mass-update', 'massUpdate')->name('admin.marketplace.products.mass_update');

        Route::post('mass-delete', 'massDestroy')->name('admin.marketplace.products.mass_delete');

        /*
         * Product Flag Routes to show flags on product edit page(admin).
         */
        Route::get('flags/{id}', 'flags')->name('admin.marketplace.products.edit');
    });

    Route::controller(ProductFlagReasonController::class)->prefix('product-flag-reasons')->group(function () {

        Route::get('', 'index')->name('admin.marketplace.product_flag_reasons.index');

        Route::post('', 'store')->name('admin.marketplace.product_flag_reasons.store');

        Route::get('edit/{id}', 'edit')->name('admin.marketplace.product_flag_reasons.edit');

        Route::put('edit', 'update')->name('admin.marketplace.product_flag_reasons.update');

        Route::delete('delete/{id}', 'destroy')->name('admin.marketplace.product_flag_reasons.delete');

        Route::post('mass-update', 'massUpdate')->name('admin.marketplace.product_flag_reasons.mass_update');

        Route::post('mass-delete', 'massDestroy')->name('admin.marketplace.product_flag_reasons.mass_delete');
    });

    Route::controller(ProductReviewsController::class)->prefix('product-reviews')->group(function () {
        Route::get('', 'index')->name('admin.marketplace.product_reviews.index');

        Route::put('edit/{id}', 'update')->name('admin.marketplace.product_reviews.update');

        Route::delete('delete/{id}', 'destroy')->name('admin.marketplace.product_reviews.delete');

        Route::post('mass-update', 'massUpdate')->name('admin.marketplace.product_reviews.mass_update');
    });

    Route::controller(SellerReviewController::class)->prefix('seller-reviews')->group(function () {
        Route::get('', 'index')->name('admin.marketplace.seller_reviews.index');

        Route::post('', 'massUpdate')->name('admin.marketplace.seller_reviews.mass_update');
    });

    Route::get('orders', [OrderController::class, 'index'])->name('admin.marketplace.orders.index');

    Route::controller(TransactionController::class)->prefix('transactions')->group(function () {
        Route::get('', 'index')->name('admin.marketplace.transactions.index');

        Route::get('{id}', 'view')->name('admin.marketplace.transactions.view');
    });

    Route::controller(SellerFlagReasonController::class)->prefix('seller-flag-reasons')->group(function () {
        Route::get('', 'index')->name('admin.marketplace.seller_flag_reasons.index');

        Route::post('', 'store')->name('admin.marketplace.seller_flag_reasons.store');

        Route::get('edit/{id}', 'edit')->name('admin.marketplace.seller_flag_reasons.edit');

        Route::put('edit', 'update')->name('admin.marketplace.seller_flag_reasons.update');

        Route::delete('delete/{id}', 'destroy')->name('admin.marketplace.seller_flag_reasons.delete');

        Route::post('mass-update', 'massUpdate')->name('admin.marketplace.seller_flag_reasons.mass_update');

        Route::post('mass-delete', 'massDestroy')->name('admin.marketplace.seller_flag_reasons.mass_delete');
    });

    Route::controller(SellerCategoryController::class)->prefix('seller-categories')->group(function () {
        Route::get('', 'index')->name('admin.marketplace.seller_categories.index');

        Route::get('create', 'create')->name('admin.marketplace.seller_categories.create');

        Route::post('', 'store')->name('admin.marketplace.seller_categories.store');

        Route::get('edit/{id}', 'edit')->name('admin.marketplace.seller_categories.edit');

        Route::put('edit/{id}', 'update')->name('admin.marketplace.seller_categories.update');

        Route::delete('delete/{id}', 'destroy')->name('admin.marketplace.seller_categories.delete');

        Route::post('mass-delete', 'massDestroy')->name('admin.marketplace.seller_categories.mass_delete');
    });

    Route::controller(PaymentRequestController::class)->prefix('payment-requests')->group(function () {
        Route::get('', 'index')->name('admin.marketplace.payment_request.index');

        Route::post('pay', 'pay')->name('admin.marketplace.payment_request.pay');
    });
});
