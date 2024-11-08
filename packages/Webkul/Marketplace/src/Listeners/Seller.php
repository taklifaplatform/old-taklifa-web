<?php

namespace Webkul\Marketplace\Listeners;

use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Helpers\Indexers\Product as ProductIndexerHelper;
use Webkul\Marketplace\Mail\NewSellerNotification;
use Webkul\Marketplace\Mail\SellerApprovalNotification;
use Webkul\Marketplace\Mail\SellerUpdateNotification;
use Webkul\Marketplace\Mail\SellerWelcomeNotification;

class Seller
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(protected ProductIndexerHelper $productIndexerHelper) {}

    /**
     * After seller create.
     */
    public function afterCreate($seller)
    {
        try {
            if ($seller->is_approved) {
                Mail::queue(new SellerApprovalNotification($seller));
            }

            Mail::queue(new SellerWelcomeNotification($seller));

            Mail::to(core()->getAdminEmailDetails()['email'])
                ->send(new NewSellerNotification($seller));
        } catch (\Exception $e) {
        }
    }

    /**
     * After seller update.
     */
    public function afterUpdate($seller)
    {
        try {
            Mail::queue(new SellerUpdateNotification($seller));
        } catch (\Exception $e) {
        }

        try {
            $this->productIndexerHelper->validate($seller, $seller->is_approved);
        } catch (\Exception $e) {
        }
    }
}
