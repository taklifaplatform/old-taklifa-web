<?php

namespace Webkul\Marketplace\Helpers\Indexers;

use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Product\Contracts\Product;
use Webkul\Product\Helpers\Indexers\Inventory as BaseInventory;

class Inventory extends BaseInventory
{
    protected $vendorId = 0;

    /**
     * Reindex products by batch size
     *
     * @return void
     */
    public function reindexBatch($products)
    {
        $newIndices = [];

        foreach ($products as $product) {
            $this->setProduct($product);

            /*
             * Find the seller product where the seller is the owner.
             */
            $sellerProduct = app(ProductRepository::class)
                ->where('is_owner', 1)
                ->where('product_id', $product->id)
                ->first();

            /*
             * Get seller IDs for assigned products (non-owners).
             */
            $sellerAssignProductIds = app(ProductRepository::class)
                ->where('is_owner', 0)
                ->where('product_id', $product->id)
                ->pluck('marketplace_seller_id');

            /*
             * Set vendor ID for the seller's own product if it exists.
             */
            if (! empty($sellerProduct)) {
                $this->vendorId = $sellerProduct->marketplace_seller_id;
            }

            /*
             * If there are assigned products, loop through the seller IDs and merge with admin (0).
             */
            if ($sellerAssignProductIds->isNotEmpty()) {
                foreach ($sellerAssignProductIds->merge([0]) as $vendorId) {
                    $this->vendorId = $vendorId;

                    $this->updateOrCreateInventoryIndices($product, $newIndices);
                }
            } else {
                $this->updateOrCreateInventoryIndices($product, $newIndices);
            }
        }

        $this->productInventoryIndexRepository->insert($newIndices);
    }

    /**
     * Returns product remaining quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        $channelInventorySourceIds = $this->channel->inventory_sources->where('status', 1)->pluck('id');

        $qty = 0;

        foreach ($this->product->inventories->where('vendor_id', $this->vendorId) as $inventory) {
            if (is_numeric($channelInventorySourceIds->search($inventory->inventory_source_id))) {
                $qty += $inventory->qty;
            }
        }

        $orderedInventory = $this->product->ordered_inventories
            ->where('vendor_id', $this->vendorId)
            ->where('channel_id', $this->channel->id)
            ->first();

        if ($orderedInventory) {
            $qty -= $orderedInventory->qty;
        }

        return $qty;
    }

    /**
     * Update or Create inventory Indices.
     *
     * @return void
     */
    public function updateOrCreateInventoryIndices(Product $product, array &$newIndices)
    {
        foreach ($this->getChannels() as $channel) {
            $this->setChannel($channel);

            $channelIndex = $product->inventory_indices
                ->where('channel_id', $channel->id)
                ->where('product_id', $product->id)
                ->where('vendor_id', $this->vendorId)
                ->first();

            $newIndex = $this->getIndices();

            data_set($newIndex, 'vendor_id', $this->vendorId);

            if ($channelIndex) {
                $oldIndex = collect($channelIndex->toArray())
                    ->except('id', 'created_at', 'updated_at')
                    ->toArray();

                $isIndexChanged = $this->isIndexChanged(
                    $oldIndex,
                    $newIndex
                );

                if ($isIndexChanged) {
                    $this->productInventoryIndexRepository->update($newIndex, $channelIndex->id);
                }
            } else {
                $newIndices[] = $newIndex;
            }
        }
    }
}
