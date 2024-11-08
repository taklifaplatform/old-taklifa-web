<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\ProductFlagReason as ProductFlagReasonContract;

class ProductFlagReason extends Model implements ProductFlagReasonContract
{
    protected $table = 'marketplace_product_flag_reasons';

    /**
     * Summary of Active Status.
     *
     * @var int
     */
    public const STATUS_ACTIVE = 1;

    /**
     * Summary of Inactive Status.
     *
     * @var int
     */
    public const STATUS_INACTIVE = 0;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
}
