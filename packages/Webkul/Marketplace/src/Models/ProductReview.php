<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Models\Customer;
use Webkul\Marketplace\Contracts\ProductReview as ReviewContract;
use Webkul\Product\Models\ProductReview as BaseProductReview;

class ProductReview extends Model implements ReviewContract
{
    protected $table = 'marketplace_product_reviews';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'product_review_id',
        'marketplace_seller_id',
        'customer_id',
    ];

    /**
     * Get the seller that belongs to the review.
     */
    public function seller()
    {
        return $this->belongsTo(SellerProxy::modelClass(), 'marketplace_seller_id');
    }

    /**
     * Get the customer that belongs to the review.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the customer that belongs to the review.
     */
    public function reviews()
    {
        return $this->hasOne(BaseProductReview::class, 'product_review_id');
    }
}
