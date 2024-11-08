<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\RefundItem;

class RefundItemRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return RefundItem::class;
    }
}
