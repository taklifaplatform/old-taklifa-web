<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Marketplace\Contracts\ProductVideo;

class ProductVideoRepository extends ProductMediaRepository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return ProductVideo::class;
    }

    /**
     * Upload videos.
     */
    public function uploadVideos(array $data, object $product): void
    {
        $this->upload($data, $product, 'videos');
    }

    /**
     * Upload variant videos.
     */
    public function uploadVariantVideos(array $variants, object $product): void
    {
        $this->upload($variants, $product, 'videos');
    }
}
