<?php

namespace Webkul\Marketplace\Listeners;

use Spatie\ResponseCache\Facades\ResponseCache;
use Webkul\Marketplace\Helpers\Indexers\Product as ProductIndexerHelper;

class Configuration
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(protected ProductIndexerHelper $productIndexerHelper) {}

    /**
     * After marketplace configuration update
     */
    public function afterUpdate()
    {
        if (! $data = request()->get('marketplace')) {
            return;
        }

        ResponseCache::forget('marketplace');

        try {
            $this->productIndexerHelper->validate(reIndex: $data['settings']['general']['status']);
        } catch (\Exception $e) {
        }
    }
}
