<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\InvoiceItem;

class InvoiceItemRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return InvoiceItem::class;
    }
}
