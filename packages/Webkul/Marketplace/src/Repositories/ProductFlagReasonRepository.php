<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Models\ProductFlagReason;

class ProductFlagReasonRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return ProductFlagReason::class;
    }
}
