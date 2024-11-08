<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Shetabit\Visitor\Traits\Visitor;
use Webkul\Marketplace\Contracts\Seller as SellerContract;
use Webkul\Marketplace\Mail\ResetPasswordNotification;

class Seller extends Authenticatable implements SellerContract
{
    use HasApiTokens, HasFactory, Notifiable, Visitor;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marketplace_sellers';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        '_token',
        'logo',
        'banner',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'allowed_product_types' => 'array',
        'password'              => 'hashed',
    ];

    /**
     * Append to the model attributes
     *
     * @var array
     */
    protected $appends = [
        'seller_id',
        'shop_url',
        'logo_url',
        'banner_url',
        'full_address',
    ];

    /**
     * Get parent id in case of children seller.
     */
    public function getSellerIdAttribute()
    {
        return $this->parent ? $this->parent->id : $this->id;
    }

    /**
     * Get parent url in case of children seller.
     */
    public function getShopUrlAttribute()
    {
        return $this->parent ? $this->parent->url : $this->url;
    }

    /**
     * Get logo image url attribute.
     */
    public function getLogoUrlAttribute()
    {
        if (! $this->logo) {
            return;
        }

        return Storage::url($this->logo);
    }

    /**
     * Get banner image url attribute.
     */
    public function getBannerUrlAttribute()
    {
        if (! $this->banner) {
            return;
        }

        return Storage::url($this->banner);
    }

    /**
     * Get the seller's full address attribute.
     */
    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            implode(', ', array_filter(explode(PHP_EOL, $this->address))),
            $this->city,
            $this->state,
            $this->postcode,
            $this->country ? "({$this->country})" : null,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Get the sellers's products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(ProductProxy::modelClass(), 'marketplace_seller_id');
    }

    /**
     * Get the seller's reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ReviewProxy::modelClass(), 'marketplace_seller_id');
    }

    /**
     * Get the product's reviews.
     */
    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReviewProxy::modelClass(), 'marketplace_seller_id');
    }

    /**
     * Get the seller's orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(OrderProxy::modelClass(), 'marketplace_seller_id');
    }

    /**
     * Get the seller's categories.
     */
    public function categories(): HasOne
    {
        return $this->hasOne(SellerCategoryProxy::modelClass(), 'seller_id');
    }

    /*
     * Get the parent seller
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the seller's role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(RoleProxy::modelClass(), 'marketplace_role_id');
    }

    /**
     * Checks if seller has permission to perform certain action.
     */
    public function hasPermission(string $permission): bool
    {
        if (
            $this->role->permission_type == 'custom'
            && ! $this->role->permissions
        ) {
            return false;
        }

        return in_array($permission, $this->role->permissions);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
