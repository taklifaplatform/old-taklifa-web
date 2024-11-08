<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Marketplace\Repositories\ShipmentRepository;

class Shipment
{
    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\ShipmentRepository  $order
     * @return void
     */
    public function __construct(protected ShipmentRepository $shipmentRepository) {}

    /**
     * After sales shipment creation, create marketplace shipment
     *
     * @param  mixed  $shipment
     */
    public function afterShipment($shipment)
    {
        $this->shipmentRepository->create(['shipment' => $shipment]);
    }
}
