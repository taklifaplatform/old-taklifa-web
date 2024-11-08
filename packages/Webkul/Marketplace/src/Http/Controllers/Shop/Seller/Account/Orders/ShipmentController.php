<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Seller\Account\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderItemRepository as BaseOrderItemRepository;
use Webkul\Sales\Repositories\ShipmentRepository;

class ShipmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected BaseOrderItemRepository $baseOrderItemRepository,
        protected OrderRepository $orderRepository,
        protected ShipmentRepository $shipmentRepository
    ) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(int $orderId)
    {
        $sellerOrder = $this->orderRepository->findOneWhere([
            'order_id'              => $orderId,
            'marketplace_seller_id' => auth()->guard('seller')->user()->seller_id,
        ]);

        if (! $sellerOrder->canShip()) {
            session()->flash('error', trans('marketplace::app.shop.sellers.account.orders.shipments.permission-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'shipment.carrier_title' => 'required',
            'shipment.track_number'  => 'required',
            'shipment.source'        => 'required',
            'shipment.items.*.*'     => 'required|numeric|min:0',
        ]);

        $data = array_merge(request()->input(), [
            'vendor_id' => $sellerOrder->marketplace_seller_id,
        ]);

        if (! $this->isInventoryValidate($data)) {
            session()->flash('error', trans('marketplace::app.shop.sellers.account.orders.shipments.qty-error'));

            return back();
        }

        Event::dispatch('marketplace.seller.account.orders.shipment.before', [$orderId, $sellerOrder]);

        $shipment = $this->shipmentRepository->create(array_merge($data, [
            'order_id' => $orderId,
        ]));

        Event::dispatch('marketplace.seller.account.orders.shipment.after', $shipment);

        session()->flash('success', trans('marketplace::app.shop.sellers.account.orders.shipments.shipment-success'));

        return back();
    }

    /**
     * Checks if requested quantity available or not
     *
     * @param  array  $data
     * @return bool
     */
    public function isInventoryValidate(&$data)
    {
        if (! isset($data['shipment']['items'])) {
            return;
        }

        $valid = false;

        $inventorySourceId = $data['shipment']['source'];

        foreach ($data['shipment']['items'] as $itemId => $inventorySource) {
            $qty = $inventorySource[$inventorySourceId];

            if ((int) $qty) {
                $orderItem = $this->baseOrderItemRepository->find($itemId);

                if ($orderItem->qty_to_ship < $qty) {
                    return false;
                }

                if ($orderItem->getTypeInstance()->isComposite()) {
                    foreach ($orderItem->children as $child) {
                        if (! $child->qty_ordered) {
                            continue;
                        }

                        $finalQty = ($child->qty_ordered / $orderItem->qty_ordered) * $qty;

                        $availableQty = $child->product->inventories()
                            ->where('inventory_source_id', $inventorySourceId)
                            ->where('vendor_id', $data['vendor_id'])
                            ->sum('qty');

                        if ($child->qty_to_ship < $finalQty
                            || $availableQty < $finalQty
                        ) {
                            return false;
                        }
                    }
                } else {
                    $availableQty = $orderItem->product->inventories()
                        ->where('inventory_source_id', $inventorySourceId)
                        ->where('vendor_id', $data['vendor_id'])
                        ->sum('qty');

                    if ($orderItem->qty_to_ship < $qty
                        || $availableQty < $qty
                    ) {
                        return false;
                    }
                }

                $valid = true;
            } else {
                unset($data['shipment']['items'][$itemId]);
            }
        }

        return $valid;
    }
}
