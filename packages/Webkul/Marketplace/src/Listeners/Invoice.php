<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Marketplace\Repositories\InvoiceRepository;

class Invoice
{
    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\InvoiceRepository  $order
     * @return void
     */
    public function __construct(protected InvoiceRepository $invoiceRepository) {}

    /**
     * After sales invoice creation, create marketplace invoice
     *
     * @param  mixed  $invoice
     */
    public function afterInvoice($invoice)
    {
        $this->invoiceRepository->create(['invoice' => $invoice]);
    }
}
