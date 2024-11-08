<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\Refund as RefundContract;
use Webkul\Sales\Models\RefundProxy;

class Refund extends Model implements RefundContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marketplace_refunds';

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
     * Get the Refund.
     */
    public function refund()
    {
        return $this->belongsTo(RefundProxy::modelClass(), 'refund_id');
    }

    /**
     * Get the Refund items record.
     */
    public function items()
    {
        return $this->hasMany(RefundItemProxy::modelClass(), 'marketplace_refund_id');
    }

    /**
     * Get the order that belongs to the refund.
     */
    public function order()
    {
        return $this->belongsTo(OrderProxy::modelClass(), 'marketplace_order_id');
    }
}
