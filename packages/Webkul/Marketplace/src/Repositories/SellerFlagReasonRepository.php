<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\SellerFlagReason;

class SellerFlagReasonRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return SellerFlagReason::class;
    }
}
