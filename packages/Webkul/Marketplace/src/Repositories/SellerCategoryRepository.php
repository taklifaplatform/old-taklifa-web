<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\SellerCategory;

class SellerCategoryRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return SellerCategory::class;
    }
}
