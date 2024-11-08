<?php

namespace Webkul\Category\Models\Traits;

use Webkul\Category\Models\Tag;

trait HasTags
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
