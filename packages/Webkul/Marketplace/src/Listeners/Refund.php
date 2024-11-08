<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Marketplace\Repositories\RefundRepository;

class Refund
{
    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\RefundRepository  $refund
     * @return void
     */
    public function __construct(protected RefundRepository $refundRepository) {}

    /**
     * After sales refund creation, create marketplace refund
     *
     * @param  object  $refund
     */
    public function afterRefund($refund)
    {
        $this->refundRepository->create($refund);
    }
}
