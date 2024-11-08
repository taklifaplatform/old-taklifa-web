<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Models\ProductFlag;

class ProductFlagRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return ProductFlag::class;
    }
}
