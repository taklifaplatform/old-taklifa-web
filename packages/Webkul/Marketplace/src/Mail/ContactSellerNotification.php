<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactSellerNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  object  $seller
     * @param  array  $data
     * @return void
     */
    public function __construct(
        private $seller,
        private $data
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->seller->email,
            replyTo: [new Address($this->data['email'], $this->data['name'])],
            subject: trans('marketplace::app.emails.contact-seller.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'marketplace::shop.default.emails.contact-seller',
            with: [
                'seller' => $this->seller,
                'data'   => $this->data,
            ],
        );
    }
}
