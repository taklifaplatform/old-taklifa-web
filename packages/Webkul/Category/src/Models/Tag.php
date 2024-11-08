<?php

namespace Webkul\Category\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\TranslatableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends TranslatableModel
{
    use HasFactory;

    public $translatedAttributes = [
        'name',
    ];

    protected $fillable = [];

    /**
     * Eager loading.
     *
     * @var array
     */
    protected $with = ['translations'];


     /**
     * Get banner url attribute.
     *
     * @return string
     */
    public function getBannerUrlAttribute()
    {
        if (! $this->banner_path) {
            return;
        }

        return Storage::url($this->banner_path);
    }

    /**
     * Use fallback for category.
     */
    protected function useFallback(): bool
    {
        return true;
    }

    /**
     * Get fallback locale for category.
     */
    protected function getFallbackLocale(?string $locale = null): ?string
    {
        if ($fallback = core()->getDefaultLocaleCodeFromDefaultChannel()) {
            return $fallback;
        }

        return parent::getFallbackLocale();
    }

}
