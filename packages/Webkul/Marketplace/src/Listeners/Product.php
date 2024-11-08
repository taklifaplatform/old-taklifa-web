<?php

namespace Webkul\Marketplace\Listeners;

use Elastic\Elasticsearch\Exception\ClientResponseException;
use Webkul\Core\Facades\ElasticSearch;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Product\Helpers\Indexers\ElasticSearch as ElasticSearchHelper;

class Product
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected ElasticSearchHelper $elasticSearchHelper
    ) {}

    /**
     * Update product for seller if Seller is owner
     */
    public function afterUpdate($product)
    {
        if (
            (
                request()->get('value') == 1
                && request()->route()->getName() == 'admin.catalog.products.mass_update'
            ) || (
                request()->get('status') == 1
                && request()->route()->getName() == 'admin.catalog.products.update'
            )
        ) {
            $sellerProduct = $this->productRepository->findOneWhere([
                'product_id' => $product->id,
                'is_owner'   => 1,
            ]);

            if ($sellerProduct) {
                $this->productRepository->where('product_id', $product->id)
                    ->update(['is_approved' => 1]);
            }
        }
    }

    /**
     * Update product for seller if Seller is owner
     */
    public function afterSellerProductUpdate($product)
    {
        if (! $product->is_owner) {
            return;
        }

        try {
            $indexName = 'products_'.core()->getRequestedChannelCode().'_'.core()->getRequestedLocaleCode().'_index';

            $results = Elasticsearch::search([
                'index' => $indexName,
                'body'  => [
                    'query' => [
                        'bool' => ['must' => [['match' => ['id' => $product->product_id]]]],
                    ],
                ],
            ]);

            if (
                isset($results['hits']['hits'][0])
                && ! $product->is_approved
            ) {
                $params = [
                    'index' => $indexName,
                    'id'    => $results['hits']['hits'][0]['_id'],
                ];

                try {
                    Elasticsearch::delete($params);
                } catch (ClientResponseException $e) {
                }
            } elseif ($product->is_approved) {
                $this->elasticSearchHelper->reindexBatch([$product->product]);
            }
        } catch (\Exception $e) {
        }
    }
}
