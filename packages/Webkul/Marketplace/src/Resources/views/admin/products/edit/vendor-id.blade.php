@inject('mpProductRepository', 'Webkul\Marketplace\Repositories\ProductRepository')

@php
    $sellerProduct = $mpProductRepository->findOneWhere([
        'is_owner'   => 1,
        'product_id' => $product->id,
    ])
@endphp

@if (! empty($sellerProduct))
    <input
        type="hidden"
        name="vendor_id"
        value="{{ $sellerProduct->marketplace_seller_id }}"
    />
@endif