<?php

namespace Webkul\Marketplace\Repositories\Sales;

use Illuminate\Support\Facades\DB;
use Webkul\Sales\Repositories\OrderItemRepository as BaseOrderItemRepository;

class OrderItemRepository extends BaseOrderItemRepository
{
    /**
     * Manage inventory.
     *
     * @param  \Webkul\Sales\Contracts\OrderItem  $orderItem
     * @return void
     */
    public function manageInventory($orderItem)
    {
        $orderItems = [];

        if ($orderItem->getTypeInstance()->isComposite()) {
            foreach ($orderItem->children as $child) {
                if (! $child->product->manage_stock) {
                    continue;
                }

                $orderItems[] = $child;
            }
        } else {
            if ($orderItem->product->manage_stock) {
                $orderItems[] = $orderItem;
            }
        }

        foreach ($orderItems as $item) {
            if (! $item->product) {
                continue;
            }

            if ($item->product->inventories->count()) {
                $orderedInventory = $item->product->ordered_inventories()
                    ->where('channel_id', $orderItem->order->channel_id);

                $sellerId = 0;

                if (! empty($item->additional['seller_info'])) {
                    $sellerId = $item->additional['seller_info']['seller_id'];

                    $orderedInventory->where('vendor_id', $sellerId);
                } else {
                    $orderedInventory->where('vendor_id', 0);
                }

                if (isset($item->qty_ordered)) {
                    $qty = $item->qty_ordered;
                } else {
                    $qty = $item?->parent?->qty_ordered ?? 1;
                }

                if (! empty($orderedInventory = $orderedInventory->first())) {
                    $orderedInventory->update([
                        'qty' => $orderedInventory->qty + $qty,
                    ]);
                } else {
                    $item->product->ordered_inventories()->create([
                        'qty'        => $qty,
                        'product_id' => $item->product_id,
                        'channel_id' => $orderItem->order->channel->id,
                        'vendor_id'  => $sellerId,
                    ]);
                }
            }
        }
    }

    /**
     * Returns qty to product inventory after order cancellation.
     *
     * @param  \Webkul\Sales\Contracts\OrderItem  $orderItem
     * @return void
     */
    public function returnQtyToProductInventory($orderItem)
    {
        if (! $orderItem->product) {
            return;
        }

        if (! $orderItem->product->manage_stock) {
            return;
        }

        $this->updateProductOrderedInventories($orderItem);

        if ($orderItem->getTypeInstance()->isStockable()) {
            $shipmentItems = $orderItem?->parent->shipment_items ?? $orderItem->shipment_items;

            foreach ($shipmentItems as $shipmentItem) {
                if ($orderItem->parent) {
                    $shippedQty = $orderItem->qty_ordered
                        ? ($orderItem->qty_ordered / $orderItem->parent->qty_ordered) * $shipmentItem->qty
                        : $orderItem->parent->qty_ordered;
                } else {
                    $shippedQty = $shipmentItem->qty;
                }

                $inventory = $orderItem->product->inventories()
                    ->where('inventory_source_id', $shipmentItem->shipment->inventory_source_id);

                if (! empty($orderItem->additional['seller_info'])) {
                    $inventory->where('vendor_id', $orderItem->additional['seller_info']['seller_id']);
                } else {
                    $inventory->where('vendor_id', 0);
                }

                $inventory->first()->update(['qty' => DB::raw('qty + '.$shippedQty)]);
            }
        }
    }

    /**
     * Update product ordered quantity.
     *
     * @param  \Webkul\Sales\Contracts\OrderItem  $orderItem
     * @return void
     */
    public function updateProductOrderedInventories($orderItem)
    {
        $orderedInventory = $orderItem->product->ordered_inventories()
            ->where('channel_id', $orderItem->order->channel->id);

        if (! empty($orderItem->additional['seller_info'])) {
            $orderedInventory->where('vendor_id', $orderItem->additional['seller_info']['seller_id']);
        } else {
            $orderedInventory->where('vendor_id', 0);
        }

        if (empty($orderedInventory = $orderedInventory->first())) {
            return;
        }

        $qty = (
            $orderedInventory->qty + ($orderItem->qty_shipped ?? $orderItem->parent->qty_shipped)
        ) - (
            $orderItem->qty_ordered ?? $orderItem->parent->qty_ordered
        );

        if ($qty < 0) {
            $qty = 0;
        }

        $orderedInventory->update(['qty' => $qty]);
    }
}
