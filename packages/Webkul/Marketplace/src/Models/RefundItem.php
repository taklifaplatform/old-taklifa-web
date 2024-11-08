<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\RefundItem as RefundItemContract;
use Webkul\Sales\Models\RefundItemProxy;

class RefundItem extends Model implements RefundItemContract
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marketplace_refund_items';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'child',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the item that belongs to the item.
     */
    public function item()
    {
        return $this->belongsTo(RefundItemProxy::modelClass(), 'refund_item_id');
    }
}
