<?php

namespace Webkul\Marketplace\Listeners;

use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Mail\NewOrderNotification;
use Webkul\Marketplace\Repositories\OrderRepository;

class Order
{
    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(protected OrderRepository $orderRepository) {}

    /**
     * After sales order creation, add entry to marketplace order table
     *
     * @param  mixed  $order
     */
    public function afterPlaceOrder($order)
    {
        $this->orderRepository->create(['order' => $order]);
    }

    /**
     * After sales order cancellation
     *
     * @param  mixed  $order
     */
    public function afterOrderCancel($order)
    {
        $this->orderRepository->cancel(['order' => $order]);
    }

    /**
     * @param  mixed  $order
     *
     * Send new order confirmation mail to the customer
     */
    public function sendNewOrderMail($order)
    {
        try {
            Mail::queue(new NewOrderNotification($order));
        } catch (\Exception $e) {
        }
    }
}
