<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Contracts\MpProductDownloadableLinkTranslation as ProductDownloadableLinkTranslationContract;

class MpProductDownloadableLinkTranslation extends Model implements ProductDownloadableLinkTranslationContract
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['title'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mp_product_downloadable_link_translations';
}
