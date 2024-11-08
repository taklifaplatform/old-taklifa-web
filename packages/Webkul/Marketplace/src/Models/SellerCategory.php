<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\SellerCategory as SellerCategoryContract;

class SellerCategory extends Model implements SellerCategoryContract
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'categories' => 'array',
    ];

    /**
     * Get the category that belongs to the seller.
     */
    public function seller()
    {
        return $this->belongsTo(SellerProxy::modelClass(), 'seller_id');
    }
}
