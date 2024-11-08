<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/**
 * Seller Account
 */
Breadcrumbs::for('seller_account', function (BreadcrumbTrail $trail) {
    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.title'), route('shop.marketplace.seller.account.dashboard.index'));
});

/**
 * Dashboard
 */
Breadcrumbs::for('seller_dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.dashboard'));
});

/**
 * Orders
 */
Breadcrumbs::for('seller_orders', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.orders'), route('shop.marketplace.seller.account.orders.index'));
});

Breadcrumbs::for('seller_order_view', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_orders');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.view'));
});

/**
 * Transaction
 */
Breadcrumbs::for('seller_transactions', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.transaction'), route('shop.marketplace.seller.account.transaction.index'));
});

Breadcrumbs::for('seller_transactions_view', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_transactions');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.view'));
});

/**
 * Products
 */
Breadcrumbs::for('seller_products', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.products'), route('shop.marketplace.seller.account.products.index'));
});

Breadcrumbs::for('seller_product_add', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_products');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.add-new-product'));
});

Breadcrumbs::for('seller_product_assign', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_products');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.assign'));
});

Breadcrumbs::for('seller_product_edit', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_products');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.edit'));
});

/**
 * Products Rreviews
 */
Breadcrumbs::for('seller_product_reviews', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.product-reviews'));
});

/**
 * Customers
 */
Breadcrumbs::for('seller_customers', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.customers'));
});

/**
 * Manage Profile
 */
Breadcrumbs::for('seller_profile', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.manage-profile'));
});

/**
 * Seller Info
 */
Breadcrumbs::for('seller_info', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.seller-info'));
});

/**
 * Seller Review
 */
Breadcrumbs::for('seller_review', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.seller-reviews'));
});

/**
 * Seller Roles
 */
Breadcrumbs::for('roles', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.roles'), route('shop.marketplace.seller.account.roles.index'));
});

Breadcrumbs::for('roles.create', function (BreadcrumbTrail $trail) {
    $trail->parent('roles');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.create'));
});

Breadcrumbs::for('roles.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('roles');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.edit'));
});

/**
 * Seller Users
 */
Breadcrumbs::for('users', function (BreadcrumbTrail $trail) {
    $trail->parent('seller_account');

    $trail->push(trans('marketplace::app.shop.sellers.account.breadcrumb.users'));
});
