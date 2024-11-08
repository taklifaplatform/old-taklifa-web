<?php

return [
    [
        'key'   => 'marketplace',
        'name'  => 'marketplace::app.admin.acl.title',
        'route' => 'admin.marketplace.sellers.index',
        'sort'  => 2,
    ], [
        'key'   => 'marketplace.sellers',
        'name'  => 'marketplace::app.admin.acl.sellers',
        'route' => 'admin.marketplace.sellers.index',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.sellers.assign-product',
        'name'  => 'marketplace::app.admin.acl.assign-product',
        'route' => 'admin.marketplace.seller.assign_product',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.sellers.create',
        'name'  => 'marketplace::app.admin.acl.common.create',
        'route' => 'admin.marketplace.sellers.create',
        'sort'  => 2,
    ], [
        'key'   => 'marketplace.sellers.edit',
        'name'  => 'marketplace::app.admin.acl.common.edit',
        'route' => 'admin.marketplace.seller.edit',
        'sort'  => 3,
    ], [
        'key'   => 'marketplace.sellers.delete',
        'name'  => 'marketplace::app.admin.acl.common.delete',
        'route' => 'admin.marketplace.sellers.delete',
        'sort'  => 4,
    ], [
        'key'   => 'marketplace.sellers.mass-update',
        'name'  => 'marketplace::app.admin.acl.common.mass-update',
        'route' => 'admin.marketplace.sellers.mass_update',
        'sort'  => 5,
    ], [
        'key'   => 'marketplace.sellers.mass-delete',
        'name'  => 'marketplace::app.admin.acl.common.mass-delete',
        'route' => 'admin.marketplace.sellers.mass_delete',
        'sort'  => 6,
    ], [
        'key'   => 'marketplace.products',
        'name'  => 'marketplace::app.admin.acl.products',
        'route' => 'admin.marketplace.products.index',
        'sort'  => 2,
    ], [
        'key'   => 'marketplace.products.delete',
        'name'  => 'marketplace::app.admin.acl.common.delete',
        'route' => 'admin.marketplace.products.delete',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.products.mass-update',
        'name'  => 'marketplace::app.admin.acl.common.mass-update',
        'route' => 'admin.marketplace.products.mass_update',
        'sort'  => 2,
    ], [
        'key'   => 'marketplace.products.mass-delete',
        'name'  => 'marketplace::app.admin.acl.common.mass-delete',
        'route' => 'admin.marketplace.products.mass_delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketplace.product-reviews',
        'name'  => 'marketplace::app.admin.acl.product-reviews',
        'route' => 'admin.marketplace.product_reviews.index',
        'sort'  => 3,
    ], [
        'key'   => 'marketplace.product-flag-reasons',
        'name'  => 'marketplace::app.admin.acl.product-flag-reasons',
        'route' => 'admin.marketplace.product_flag_reasons.index',
        'sort'  => 4,
    ], [
        'key'   => 'marketplace.product-flag-reasons.create',
        'name'  => 'marketplace::app.admin.acl.common.create',
        'route' => 'admin.marketplace.product_flag_reasons.create',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.product-flag-reasons.edit',
        'name'  => 'marketplace::app.admin.acl.common.edit',
        'route' => 'admin.marketplace.product_flag_reasons.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketplace.product-flag-reasons.delete',
        'name'  => 'marketplace::app.admin.acl.common.delete',
        'route' => 'admin.marketplace.product_flag_reasons.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketplace.product-flag-reasons.mass-update',
        'name'  => 'marketplace::app.admin.acl.common.mass-update',
        'route' => 'admin.marketplace.product_flag_reasons.mass_update',
        'sort'  => 4,
    ], [
        'key'   => 'marketplace.product-flag-reasons.mass-delete',
        'name'  => 'marketplace::app.admin.acl.common.mass-delete',
        'route' => 'admin.marketplace.product_flag_reasons.mass_delete',
        'sort'  => 5,
    ], [
        'key'   => 'marketplace.seller-reviews',
        'name'  => 'marketplace::app.admin.acl.reviews',
        'route' => 'admin.marketplace.seller_reviews.index',
        'sort'  => 5,
    ], [
        'key'   => 'marketplace.seller-reviews.mass-update',
        'name'  => 'marketplace::app.admin.acl.common.mass-update',
        'route' => 'admin.marketplace.seller_reviews.mass_update',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.orders',
        'name'  => 'marketplace::app.admin.acl.orders',
        'route' => 'admin.marketplace.orders.index',
        'sort'  => 6,
    ], [
        'key'   => 'marketplace.orders.pay',
        'name'  => 'marketplace::app.admin.acl.common.pay',
        'route' => 'admin.marketplace.orders.pay',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.orders.view',
        'name'  => 'marketplace::app.admin.acl.common.view',
        'route' => 'admin.sales.orders.view',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.transactions',
        'name'  => 'marketplace::app.admin.acl.transactions',
        'route' => 'admin.marketplace.transactions.index',
        'sort'  => 7,
    ], [
        'key'   => 'marketplace.transactions.view',
        'name'  => 'marketplace::app.admin.acl.common.view',
        'route' => 'admin.marketplace.transactions.view',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.seller-flag-reasons',
        'name'  => 'marketplace::app.admin.acl.seller-flag-reasons',
        'route' => 'admin.marketplace.seller_flag_reasons.index',
        'sort'  => 8,
    ], [
        'key'   => 'marketplace.seller-flag-reasons.create',
        'name'  => 'marketplace::app.admin.acl.common.create',
        'route' => 'admin.marketplace.seller_flag_reasons.create',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.seller-flag-reasons.edit',
        'name'  => 'marketplace::app.admin.acl.common.edit',
        'route' => 'admin.marketplace.seller_flag_reasons.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketplace.seller-flag-reasons.delete',
        'name'  => 'marketplace::app.admin.acl.common.delete',
        'route' => 'admin.marketplace.seller_flag_reasons.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketplace.seller-flag-reasons.mass-update',
        'name'  => 'marketplace::app.admin.acl.common.mass-update',
        'route' => 'admin.marketplace.seller_flag_reasons.mass_update',
        'sort'  => 4,
    ], [
        'key'   => 'marketplace.seller-flag-reasons.mass-delete',
        'name'  => 'marketplace::app.admin.acl.common.mass-delete',
        'route' => 'admin.marketplace.seller_flag_reasons.mass_delete',
        'sort'  => 5,
    ], [
        'key'   => 'marketplace.seller-categories',
        'name'  => 'marketplace::app.admin.acl.seller-categories',
        'route' => 'admin.marketplace.seller_categories.index',
        'sort'  => 9,
    ], [
        'key'   => 'marketplace.seller-categories.create',
        'name'  => 'marketplace::app.admin.acl.common.create',
        'route' => 'admin.marketplace.seller_categories.create',
        'sort'  => 1,
    ], [
        'key'   => 'marketplace.seller-categories.edit',
        'name'  => 'marketplace::app.admin.acl.common.edit',
        'route' => 'admin.marketplace.seller_categories.edit',
        'sort'  => 2,
    ], [
        'key'   => 'marketplace.seller-categories.delete',
        'name'  => 'marketplace::app.admin.acl.common.delete',
        'route' => 'admin.marketplace.seller_categories.delete',
        'sort'  => 3,
    ], [
        'key'   => 'marketplace.seller-categories.mass-delete',
        'name'  => 'marketplace::app.admin.acl.common.mass-delete',
        'route' => 'admin.marketplace.seller_categories.mass_delete',
        'sort'  => 4,
    ], [
        'key'   => 'marketplace.payment-request',
        'name'  => 'marketplace::app.admin.acl.payment-request',
        'route' => 'admin.marketplace.payment_request.index',
        'sort'  => 10,
    ], [
        'key'   => 'marketplace.payment-request.pay',
        'name'  => 'marketplace::app.admin.acl.common.pay',
        'route' => 'admin.marketplace.payment_request.index',
        'sort'  => 1,
    ],
];
