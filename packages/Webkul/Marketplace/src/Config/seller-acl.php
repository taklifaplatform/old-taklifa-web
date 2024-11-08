<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    |
    | All ACLs related to dashboard will be placed here.
    |
    */
    [
        'key'   => 'dashboard',
        'name'  => 'marketplace::app.shop.sellers.account.acl.dashboard',
        'route' => 'shop.marketplace.seller.account.dashboard.index',
        'sort'  => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    |
    | All ACLs related to orders will be placed here.
    |
    */
    [
        'key'   => 'orders',
        'name'  => 'marketplace::app.shop.sellers.account.acl.orders',
        'route' => 'shop.marketplace.seller.account.orders.index',
        'sort'  => 2,
    ], [
        'key'   => 'orders.view',
        'name'  => 'marketplace::app.shop.sellers.account.acl.view',
        'route' => 'shop.marketplace.seller.account.orders.view',
        'sort'  => 1,
    ], [
        'key'   => 'orders.cancel',
        'name'  => 'marketplace::app.shop.sellers.account.acl.cancel',
        'route' => 'shop.marketplace.seller.account.orders.cancel',
        'sort'  => 2,
    ], [
        'key'   => 'orders.invoice',
        'name'  => 'marketplace::app.shop.sellers.account.acl.invoice',
        'route' => 'shop.marketplace.seller.account.invoices.store',
        'sort'  => 3,
    ], [
        'key'   => 'orders.print_invoice',
        'name'  => 'marketplace::app.shop.sellers.account.acl.print-invoice',
        'route' => 'shop.marketplace.seller.account.invoices.print',
        'sort'  => 4,
    ], [
        'key'   => 'orders.shipment',
        'name'  => 'marketplace::app.shop.sellers.account.acl.shipment',
        'route' => 'shop.marketplace.seller.account.shipments.store',
        'sort'  => 5,
    ], [
        'key'   => 'orders.payment_request',
        'name'  => 'marketplace::app.shop.sellers.account.acl.payment-request',
        'route' => 'shop.marketplace.seller.account.payment.request',
        'sort'  => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    |
    | All ACLs related to transactions will be placed here.
    |
    */
    [
        'key'   => 'transactions',
        'name'  => 'marketplace::app.shop.sellers.account.acl.transactions',
        'route' => 'shop.marketplace.seller.account.transaction.index',
        'sort'  => 3,
    ], [
        'key'   => 'transactions.view',
        'name'  => 'marketplace::app.shop.sellers.account.acl.view',
        'route' => 'shop.marketplace.seller.account.transaction.view',
        'sort'  => 1,
    ], [
        'key'   => 'transactions.print',
        'name'  => 'marketplace::app.shop.sellers.account.acl.print',
        'route' => 'shop.marketplace.seller.account.transaction.print',
        'sort'  => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    |
    | All ACLs related to products will be placed here.
    |
    */
    [
        'key'   => 'products',
        'name'  => 'marketplace::app.shop.sellers.account.acl.products',
        'route' => 'shop.marketplace.seller.account.products.index',
        'sort'  => 4,
    ], [
        'key'   => 'products.create',
        'name'  => 'marketplace::app.shop.sellers.account.acl.create',
        'route' => 'shop.marketplace.seller.account.products.create',
        'sort'  => 1,
    ], [
        'key'   => 'products.assign',
        'name'  => 'marketplace::app.shop.sellers.account.acl.assign',
        'route' => 'marketplace.account.products.assign.create',
        'sort'  => 2,
    ], [
        'key'   => 'products.edit',
        'name'  => 'marketplace::app.shop.sellers.account.acl.edit',
        'route' => 'marketplace.account.products.edit',
        'sort'  => 3,
    ], [
        'key'   => 'products.delete',
        'name'  => 'marketplace::app.shop.sellers.account.acl.delete',
        'route' => 'marketplace.account.products.delete',
        'sort'  => 4,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reviews
    |--------------------------------------------------------------------------
    |
    | All ACLs related to reviews will be placed here.
    |
    */
    [
        'key'   => 'product_reviews',
        'name'  => 'marketplace::app.shop.sellers.account.acl.product-reviews',
        'route' => 'shop.marketplace.seller.account.products.review',
        'sort'  => 5,
    ], [
        'key'   => 'product_reviews.edit',
        'name'  => 'marketplace::app.shop.sellers.account.acl.edit',
        'route' => 'shop.marketplace.seller.account.products.review.mass_update',
        'sort'  => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    |
    | All ACLs related to customers will be placed here.
    |
    */
    [
        'key'   => 'customers',
        'name'  => 'marketplace::app.shop.sellers.account.acl.customers',
        'route' => 'shop.marketplace.seller.account.customers.index',
        'sort'  => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Seller Info
    |--------------------------------------------------------------------------
    |
    | All ACLs related to seller info will be placed here.
    |
    */
    [
        'key'   => 'seller_info',
        'name'  => 'marketplace::app.shop.sellers.account.acl.seller-info',
        'route' => 'shop.marketplace.seller.account.seller_info',
        'sort'  => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Seller Reviews
    |--------------------------------------------------------------------------
    |
    | All ACLs related to seller reviews will be placed here.
    |
    */
    [
        'key'   => 'seller_reviews',
        'name'  => 'marketplace::app.shop.sellers.account.acl.seller-reviews',
        'route' => 'shop.marketplace.seller.account.seller_reviews.index',
        'sort'  => 9,
    ],
];
