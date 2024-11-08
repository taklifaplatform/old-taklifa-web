<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\SellerFlag;

class SellerFlagRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return SellerFlag::class;
    }
}
