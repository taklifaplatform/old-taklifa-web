<?php

use Webkul\Marketplace\Acl;
use Webkul\Marketplace\Seller;

/**
 * Acl helper.
 */
if (! function_exists('marketplace_acl')) {
    function marketplace_acl(): Acl
    {
        return app(Acl::class);
    }
}

/**
 * Seller helper.
 */
if (! function_exists('seller')) {
    function seller(): Seller
    {
        return app(Seller::class);
    }
}
