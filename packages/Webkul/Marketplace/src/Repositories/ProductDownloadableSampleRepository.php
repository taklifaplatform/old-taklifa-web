<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Contracts\MpProductDownloadableSample;

class ProductDownloadableSampleRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return MpProductDownloadableSample::class;
    }

    /**
     * upload file
     */
    public function upload(array $data, int $productId)
    {
        if (
            isset($data['file'])
            && $data['file'] instanceof UploadedFile
        ) {
            $path = $data['file']->store("product_downloadable_links/{$productId}");

            return [
                'file'      => $path,
                'file_name' => $data['file']->getClientOriginalName(),
                'file_url'  => Storage::url($path),
            ];
        }

        return [];
    }

    /**
     * Save downloadable samples
     */
    public function saveSamples(array $data, object $product)
    {
        $previousSampleIds = $product->downloadable_samples()->pluck('id');

        if (isset($data['downloadable_samples'])) {
            foreach ($data['downloadable_samples'] as $sampleId => $data) {
                if (Str::contains($sampleId, 'sample_')) {
                    $this->create(array_merge([
                        'product_id' => $product->id,
                    ], $data));
                } else {
                    if (is_numeric($index = $previousSampleIds->search($sampleId))) {
                        $previousSampleIds->forget($index);
                    }

                    $this->updateOrCreate([
                        'id'         => $sampleId,
                        'product_id' => $product->id,
                    ], $data);
                }
            }
        }

        foreach ($previousSampleIds as $sampleId) {
            $this->delete($sampleId);
        }
    }
}
