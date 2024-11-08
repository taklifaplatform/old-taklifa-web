<?php

namespace Webkul\Marketplace\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\Checkout\Contracts\CartItem as BaseCartItemContract;
use Webkul\Marketplace\Cart;
use Webkul\Marketplace\Console\Commands\InstallMarketplace;
use Webkul\Marketplace\Helpers\Indexers\Inventory;
use Webkul\Marketplace\Http\Controllers\API\ReviewController;
use Webkul\Marketplace\Http\Controllers\Shop\ProductsCategoriesProxyController;
use Webkul\Marketplace\Http\Middleware\Marketplace;
use Webkul\Marketplace\Http\Middleware\Seller;
use Webkul\Marketplace\Models\CartItem;
use Webkul\Marketplace\Models\Catalog\Product;
use Webkul\Marketplace\Models\ProductOrderedInventory;
use Webkul\Marketplace\ProductTypes\Simple as SimpleProductType;
use Webkul\Marketplace\Repositories\BaseProductRepository as BaseMpProductRepository;
use Webkul\Marketplace\Repositories\Sales\OrderItemRepository;
use Webkul\Marketplace\Repositories\Sales\RefundItemRepository;
use Webkul\Marketplace\Repositories\Sales\ShipmentItemRepository;
use Webkul\Marketplace\Repositories\Sales\ShipmentRepository;
use Webkul\Product\Contracts\Product as ProductContract;
use Webkul\Product\Contracts\ProductOrderedInventory as ProductOrderedInventoryContract;
use Webkul\Product\Helpers\Indexers\Inventory as BaseInventory;
use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;
use Webkul\Product\Type\Simple as BaseSimpleProductType;
use Webkul\Sales\Repositories\OrderItemRepository as BaseOrderItemRepository;
use Webkul\Sales\Repositories\RefundItemRepository as BaseRefundItemRepository;
use Webkul\Sales\Repositories\ShipmentItemRepository as BaseShipmentItemRepository;
use Webkul\Sales\Repositories\ShipmentRepository as BaseShipmentRepository;
use Webkul\Shop\Http\Controllers\API\ReviewController as BaseReviewController;
use Webkul\Shop\Http\Controllers\ProductsCategoriesProxyController as BaseProductsCategoriesProxyController;

class MarketplaceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        include __DIR__.'/../Http/helpers.php';

        $router->aliasMiddleware('seller', Seller::class);

        $router->aliasMiddleware('marketplace', Marketplace::class);

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        Route::middleware('web')->group(__DIR__.'/../Routes/web.php');

        $this->loadRoutesFrom(__DIR__.'/../Routes/breadcrumbs.php');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'marketplace');

        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components', 'marketplace');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'marketplace');

        $this->app->register(ModuleServiceProvider::class);

        $this->app->register(EventServiceProvider::class);

        $this->app->bind('cart', Cart::class);

        $this->app->bind(BaseProductsCategoriesProxyController::class, ProductsCategoriesProxyController::class);

        $this->app->bind(BaseProductRepository::class, BaseMpProductRepository::class);

        $this->app->bind(BaseReviewController::class, ReviewController::class);

        $this->app->bind(BaseInventory::class, Inventory::class);

        $this->app->bind(BaseOrderItemRepository::class, OrderItemRepository::class);

        $this->app->bind(BaseShipmentRepository::class, ShipmentRepository::class);

        $this->app->bind(BaseShipmentItemRepository::class, ShipmentItemRepository::class);

        $this->app->bind(BaseRefundItemRepository::class, RefundItemRepository::class);

        $this->app->bind(BaseSimpleProductType::class, SimpleProductType::class);

        $this->app->concord->registerModel(ProductContract::class, Product::class);

        $this->app->concord->registerModel(BaseCartItemContract::class, CartItem::class);

        $this->app->concord->registerModel(ProductOrderedInventoryContract::class, ProductOrderedInventory::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallMarketplace::class,
            ]);
        }

        $this->mergeAuthConfigs();

        $this->publishAssets();

        if (core()->getConfigData('marketplace.settings.general.status')) {
            $this->mergeConfigFrom(
                dirname(__DIR__).'/Config/admin-menu.php',
                'menu.admin'
            );

            $this->mergeConfigFrom(
                dirname(__DIR__).'/Config/seller-menu.php',
                'menu.seller'
            );
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php',
            'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/admin-acl.php', 'acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/seller-acl.php', 'marketplace_acl'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/bagisto-vite.php',
            'bagisto-vite.viters'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/response-cache.php',
            'responsecache.replacers'
        );
    }

    /**
     * Publish the assets.
     *
     * @return void
     */
    protected function publishAssets()
    {
        $this->publishes([
            __DIR__.'/../Http/Resources/CartItemResource.php' => __DIR__.'/../../../../Webkul/Shop/src/Http/Resources/CartItemResource.php',
        ]);

        $this->publishes([
            __DIR__.'/../../publishable/storage' => storage_path('app/public'),
        ]);

        $this->publishes([
            __DIR__ .'/../../publishable/build' => public_path('themes/marketplace/build')
        ], 'public');

        // Override shop dropdown component due show dropdown after hover on dropdown (Ex: Mass Update Action).
        $this->publishes([
            __DIR__.'/../Resources/views/components/dropdown/index.blade.php' => resource_path('themes/default/views/components/dropdown/index.blade.php'),
        ]);
    }

    /**
     * Merge Auth Configs.
     *
     * @return void
     */
    public function mergeAuthConfigs()
    {
        foreach (['guards', 'providers', 'passwords'] as $key) {
            $this->mergeConfigFrom(
                dirname(__DIR__).'/Config/auth/'.$key.'.php',
                'auth.'.$key
            );
        }
    }
}
