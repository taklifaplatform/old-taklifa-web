<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\MpProductDownloadableLink;
use Webkul\Product\Models\Product;

class ProductDownloadableLinkRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return MpProductDownloadableLink::class;
    }

    /**
     * Upload.
     */
    public function upload(array $data, int $productId): array
    {
        foreach ($data as $type => $file) {
            if (request()->hasFile($type)) {
                return [
                    $type         => $path = request()->file($type)->store('product_downloadable_links/'.$productId, 'private'),
                    $type.'_name' => $file->getClientOriginalName(),
                    $type.'_url'  => Storage::url($path),
                ];
            }
        }

        return [];
    }

    /**
     * Save links.
     *
     * @param  Product  $product
     * @return void
     */
    public function saveLinks(array $data, $product)
    {
        $previousLinkIds = $product->downloadable_links()->pluck('id');

        if (isset($data['downloadable_links'])) {
            foreach ($data['downloadable_links'] as $linkId => $data) {
                if (Str::contains($linkId, 'link_')) {
                    $this->create(array_merge([
                        'product_id' => $product->id,
                    ], $data));
                } else {
                    if (is_numeric($index = $previousLinkIds->search($linkId))) {
                        $previousLinkIds->forget($index);
                    }

                    $this->updateOrCreate([
                        'id'         => $linkId,
                        'product_id' => $product->id,
                    ], $data);
                }
            }
        }

        foreach ($previousLinkIds as $linkId) {
            $this->delete($linkId);
        }
    }
}
