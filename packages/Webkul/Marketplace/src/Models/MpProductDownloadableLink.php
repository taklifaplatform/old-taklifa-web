<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\TranslatableModel;
use Webkul\Marketplace\Contracts\MpProductDownloadableLink as MpProductDownloadableLinkContract;

class MpProductDownloadableLink extends TranslatableModel implements MpProductDownloadableLinkContract
{
    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = ['title'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mp_product_downloadable_links';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'price',
        'url',
        'file',
        'file_name',
        'type',
        'sample_url',
        'sample_file',
        'sample_file_name',
        'sample_type',
        'sort_order',
        'product_id',
        'downloads',
    ];

    /**
     * With the translations given attributes
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Get image url for the file.
     */
    public function file_url()
    {
        return Storage::url($this->path);
    }

    /**
     * Get image url for the file.
     */
    public function getFileUrlAttribute()
    {
        return $this->file_url();
    }

    /**
     * Get image url for the sample file.
     */
    public function sample_file_url()
    {
        return Storage::url($this->path);
    }

    /**
     * Get image url for the sample file.
     */
    public function getSampleFileUrlAttribute()
    {
        return $this->sample_file_url();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        $translation = $this->translate(core()->getRequestedLocaleCode());

        $array['title'] = $translation ? $translation->title : '';

        $array['file_url'] = $this->file ? Storage::url($this->file) : null;

        $array['sample_file_url'] = $this->sample_file ? Storage::url($this->sample_file) : null;

        return $array;
    }
}
