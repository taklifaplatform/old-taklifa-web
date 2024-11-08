<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\ShipmentItem;

class ShipmentItemRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return ShipmentItem::class;
    }
}
