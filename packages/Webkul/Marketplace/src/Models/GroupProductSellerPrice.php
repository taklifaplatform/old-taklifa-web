<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\GroupProductSellerPrice as GroupProductSellerPriceContract;

class GroupProductSellerPrice extends Model implements GroupProductSellerPriceContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mp_grouped_product_price';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'product_grouped_product_id',
        'marketplace_seller_id',
        'seller_sell_price',
        'created_at',
        'updated_at',
    ];
}
