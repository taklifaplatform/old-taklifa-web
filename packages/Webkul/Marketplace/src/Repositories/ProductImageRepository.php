<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Marketplace\Contracts\ProductImage;

class ProductImageRepository extends ProductMediaRepository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ProductImage::class;
    }

    /**
     * Upload images.
     */
    public function uploadImages(array $data, object $product): void
    {
        $this->upload($data, $product, 'images');
    }
}
