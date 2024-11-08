<?php

namespace Webkul\Marketplace\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'marketplace.seller.create.after' => [
            'Webkul\Marketplace\Listeners\Seller@afterCreate',
        ],

        'marketplace.seller.update.after' => [
            'Webkul\Marketplace\Listeners\Seller@afterUpdate',
        ],

        'marketplace.product.update.after' => [
            'Webkul\Marketplace\Listeners\Product@afterSellerProductUpdate',
        ],

        'catalog.product.update.after' => [
            'Webkul\Marketplace\Listeners\Product@afterUpdate',
        ],

        'checkout.cart.collect.totals.before' => [
            'Webkul\Marketplace\Listeners\Cart@collectTotalsBefore',
        ],

        'checkout.cart.add.before' => [
            'Webkul\Marketplace\Listeners\Cart@cartItemAddBefore',
        ],

        'checkout.cart.add.after' => [
            'Webkul\Marketplace\Listeners\Cart@cartItemAddAfter',
        ],

        'checkout.order.save.after' => [
            'Webkul\Marketplace\Listeners\Order@afterPlaceOrder',
        ],

        'sales.order.cancel.after' => [
            'Webkul\Marketplace\Listeners\Order@afterOrderCancel',
        ],

        'marketplace.sales.order.save.after' => [
            'Webkul\Marketplace\Listeners\Order@sendNewOrderMail',
        ],

        'sales.invoice.save.after' => [
            'Webkul\Marketplace\Listeners\Invoice@afterInvoice',
        ],

        'sales.shipment.save.after' => [
            'Webkul\Marketplace\Listeners\Shipment@afterShipment',
        ],

        'sales.refund.save.after' => [
            'Webkul\Marketplace\Listeners\Refund@afterRefund',
        ],

        'core.configuration.save.after' => [
            'Webkul\Marketplace\Listeners\Configuration@afterUpdate',
        ],
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $eventTemplates = [
            [
                'event'    => 'bagisto.shop.layout.head.before',
                'template' => 'marketplace::components.shop.layouts.header.style',
            ],
            [
                'event'    => 'bagisto.admin.layout.head.before',
                'template' => 'marketplace::components.admin.layouts.header.style',
            ],
            [
                'event'    => 'bagisto.shop.products.view.additional_actions.after',
                'template' => 'marketplace::shop.products.product-sellers',
            ],
            [
                'event'    => 'bagisto.shop.products.view.additional_actions.after',
                'template' => 'marketplace::shop.sellers.products.report',
            ],
            [
                'event'    => 'bagisto.shop.products.view.after',
                'template' => 'marketplace::shop.products.top-selling',
            ],
            [
                'event'    => 'bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.after',
                'template' => 'marketplace::components.shop.layouts.header.sell',
            ],
            [
                'event'    => 'bagisto.shop.components.layouts.header.mobile.mini_cart.after',
                'template' => 'marketplace::components.shop.layouts.header.sell',
            ],
            [
                'event'    => 'bagisto.shop.marketplace.layout.content.before',
                'template' => 'marketplace::shop.sellers.account.suspended',
            ],
            [
                'event'    => 'bagisto.shop.checkout.mini-cart.drawer.content.remove_button.after',
                'template' => 'marketplace::shop.checkout.mini-cart.seller-info',
            ],
            [
                'event'    => 'bagisto.shop.checkout.cart.item_name.after',
                'template' => 'marketplace::shop.checkout.mini-cart.seller-info',
            ],
            [
                'event'    => 'bagisto.shop.checkout.onepage.summary.item_name.after',
                'template' => 'marketplace::shop.checkout.mini-cart.seller-info',
            ],
            [
                'event'    => 'bagisto.admin.catalog.product.edit.form.after',
                'template' => 'marketplace::admin.products.edit.vendor-id',
            ],
            [
                'event'    => 'bagisto.admin.catalog.product.edit.after',
                'template' => 'marketplace::admin.products.edit.flags',
            ],
        ];

        if (core()->getConfigData('marketplace.settings.general.status')) {
            foreach ($eventTemplates as $eventTemplate) {
                Event::listen(current($eventTemplate), fn ($e) => $e->addTemplate(end($eventTemplate)));
            }
        }

        Event::listen('checkout.load.index', 'Webkul\Marketplace\Listeners\Onepage@index');
    }
}
