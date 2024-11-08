@inject ('productRepository', 'Webkul\Marketplace\Repositories\ProductRepository')

@php
    $sellerProduct = $productRepository->with('seller')->findOneWhere([
        'product_id' => $product->id,
        'is_owner'   => 1,
    ]);
@endphp

@if ($sellerProduct)
    <x-shop::products.carousel
        :title="trans('marketplace::app.shop.products.top-selling')"
        :src="route('marketplace.seller.top_selling_products', $sellerProduct->seller->id)"
    >
    </x-shop::products.carousel>
@endif
