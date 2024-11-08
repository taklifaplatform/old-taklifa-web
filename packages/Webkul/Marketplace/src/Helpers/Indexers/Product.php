<?php

namespace Webkul\Marketplace\Helpers\Indexers;

use Webkul\Product\Helpers\Indexers\ElasticSearch;

class Product extends ElasticSearch
{
    public function validate($seller = null, $reIndex = true)
    {
        $indexName = 'products_'.core()->getRequestedChannelCode().'_'.core()->getRequestedLocaleCode().'_index';

        while (true) {
            $paginator = $this->productRepository->scopeQuery(function ($query) use ($seller) {
                return $query->join('marketplace_products', 'products.id', '=', 'marketplace_products.product_id')
                    ->when($seller, function ($query) use ($seller) {
                        return $query->where('marketplace_products.marketplace_seller_id', $seller->id);
                    });
            })
                ->select('products.*')
                ->with([
                    'channels',
                    'categories',
                    'inventories',
                    'super_attributes',
                    'variants',
                    'variants.channels',
                    'attribute_family',
                    'attribute_values',
                    'variants.attribute_family',
                    'variants.attribute_values',
                    'price_indices',
                    'variants.price_indices',
                    'inventory_indices',
                    'variants.inventory_indices',
                ])
                ->cursorPaginate(self::BATCH_SIZE);

            if ($reIndex) {
                $this->reindexBatch($paginator->items());
            } else {
                $products = $paginator->items();

                foreach ($products as $product) {
                    $removeIndices[$indexName][] = $product->id;
                }

                if (isset($removeIndices)) {
                    $this->deleteIndices($removeIndices);
                }
            }

            if (! $cursor = $paginator->nextCursor()) {
                break;
            }

            request()->query->add(['cursor' => $cursor->encode()]);
        }

        request()->query->remove('cursor');
    }
}
