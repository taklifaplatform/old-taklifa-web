@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productRepository', 'Webkul\Marketplace\Repositories\ProductRepository')
@inject ('reviewRepository', 'Webkul\Marketplace\Repositories\ReviewRepository')

@php
    $avgRatings = round($reviewHelper->getAverageRating($product));

    $baseProduct = $product->parent_id ? $product->parent : $product;

    $productBaseImage = product_image()->getProductBaseImage($product);
@endphp

<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="{{ trim($product->meta_description) != "" ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}"/>

    <meta name="keywords" content="{{ $product->meta_keywords }}"/>

    @if (core()->getConfigData('catalog.rich_snippets.products.enable'))
        <script type="application/ld+json">
            {{ app('Webkul\Product\Helpers\SEO')->getProductJsonLd($product) }}
        </script>
    @endif

    <meta name="twitter:card" content="summary_large_image" />

    <meta name="twitter:title" content="{{ $product->name }}" />

    <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta name="twitter:image:alt" content="" />

    <meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:type" content="og:product" />

    <meta property="og:title" content="{{ $product->name }}" />

    <meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta property="og:url" content="{{ route('shop.product_or_category.index', $product->url_key) }}" />
@endPush

<!-- Page Layout -->
<x-marketplace::shop.layouts.full>
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
    </x-slot>

    <!-- Breadcrumbs -->
    <div class="flex justify-center max-lg:hidden">
        <x-shop::breadcrumbs name="product" :entity="$product"></x-shop::breadcrumbs>
    </div>

    <div class="flex w-full flex-col gap-10 px-4 py-8 lg:flex-row lg:p-16">
        <div class="flex justify-center">
            <img
                src="{{$productBaseImage['medium_image_url']}}"
                class="top-[-0.5] h-auto max-h-[609px] w-auto max-w-[500px] rounded lg:h-[550px] lg:w-[560px]"
                alt="Product Image"
                width="560"
                height="609"
            />
        </div>

        <div class="grid w-full content-baseline items-baseline gap-5">
            <div class="grid gap-5">
                <h1 class="text-3xl font-medium text-[#000000]">
                    {{ $baseProduct->name }}
                </h1>

                @if ($baseProduct->type === 'configurable')
                    <div class="text-base font-normal text-navyBlue">
                        @php
                            $attributes = [];

                            $options = collect($baseProduct->super_attributes)
                                ->map(function ($attribute) use ($product, &$attributes) {
                                    $selectedOption = $attribute->options
                                        ->where('id', $product->{$attribute->code})
                                        ->first();

                                    if ($selectedOption) {
                                        $attributes[$attribute->id] = $selectedOption->id;

                                        return $attribute->name . ' : ' . $selectedOption->label;
                                    }

                                    return null;
                                })
                                ->filter()
                                ->toArray();
                        @endphp

                        {{ implode(', ', $options) }}
                    </div>
                @endif
    
                <div class="flex items-center gap-2.5">
                    <x-marketplace::shop.products.star-rating 
                        :value="$avgRatings"
                        :is-editable=false
                    />
    
                    <p class="text-sm text-[#6E6E6E]">
                        ({{ $product->approvedReviews->count() }} @lang('reviews'))
                    </p>
                </div>
            </div>

            <p class="text-lg text-navyBlue">
                @lang('marketplace::app.shop.products.seller-count', ['count' => $sellerCount])
            </p>

            <v-more-seller></v-more-seller>
        </div>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-more-seller-template">
            @foreach ($productRepository->getSellerProducts($product) as $key => $sellerProduct)
                <x-shop::form action="{{ route('marketplace.cart.add', $sellerProduct->product_id) }}">

                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $sellerProduct->product_id }}"
                    >

                    <input
                        type="hidden"
                        name="seller_info[product_id]"
                        value="{{ $sellerProduct->id }}"
                    >

                    <input
                        type="hidden"
                        name="seller_info[seller_id]"
                        value="{{ $sellerProduct->seller->id }}"
                    >

                    <input
                        type="hidden"
                        name="seller_info[is_owner]"
                        value="0"
                    >

                    @if ($baseProduct->type == 'configurable')
                        <input
                            type="hidden"
                            name="selected_configurable_option"
                            value="{{ $product->id }}"
                        >

                        @foreach ($attributes as $attributeId => $optionId)
                            <input
                                type="hidden"
                                name="super_attribute[{{$attributeId}}]"
                                value="{{$optionId}}"
                            />
                        @endforeach
                    @endif

                    <div class="rounded-md border p-5">
                        <div class="flex justify-between gap-2.5">
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-col gap-2">
                                    <a
                                        href="{{route('marketplace.seller.show', $sellerProduct->seller->url)}}"
                                        class="text-xl font-medium text-[#000000D4]"
                                    >
                                        {{ $sellerProduct->seller->shop_title}}
                                    </a>

                                    <x-marketplace::shop.products.star-rating 
                                        :value="$reviewRepository->getAverageRating($sellerProduct->seller)"
                                        :is-editable=false
                                    >
                                    </x-marketplace::shop.products.star-rating>
                                </div>
                                
                                <p class="text-2xl font-medium">
                                    {{ core()->currency($sellerProduct->price) }}
                                </p>

                                <p class="text-base font-normal">
                                    @lang('marketplace::app.shop.products.delivery-info')
                                </p>
                            </div>

                            <div class="flex flex-col gap-2">
                                @if ($product->type == 'downloadable')
                                    <input type="hidden" name="quantity" value="1">

                                    @if ($sellerProduct->downloadable_samples->count())
                                        <div class="sample-list">
                                            <label class="mb-3 flex font-medium">
                                                @lang('shop::app.products.view.type.downloadable.samples')
                                            </label>

                                            <ul>
                                                @foreach ($sellerProduct->downloadable_samples as $sample)
                                                    <li class="mb-2">
                                                        <a 
                                                            href="{{ route('marketplace.downloadable.download_sample', ['type' => 'sample', 'id' => $sample->id]) }}" 
                                                            class="text-blue-700"
                                                            target="_blank"
                                                        >
                                                            {{ $sample->title }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if ($sellerProduct->downloadable_links->count())
                                        <label class="flex font-medium">
                                            @lang('shop::app.products.view.type.downloadable.links')
                                        </label>

                                        <div class="flex flex-col gap-4 max-sm:gap-1">
                                            @foreach ($sellerProduct->downloadable_links as $link)
                                                <div class="flex select-none items-center gap-x-4">
                                                    <div class="flex items-center">
                                                        <v-field
                                                            type="checkbox"
                                                            name="links[]"
                                                            value="{{ $link->id }}"
                                                            id="{{ $link->id }}"
                                                            class="peer hidden"
                                                            rules="required"
                                                            label="@lang('shop::app.products.view.type.downloadable.links')"
                                                        >
                                                        </v-field>
                                                        
                                                        <label
                                                            class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-2xl text-navyBlue peer-checked:text-navyBlue"
                                                            for="{{ $link->id }}"
                                                        ></label>
                                                        
                                                        <label
                                                            for="{{ $link->id }}"
                                                            class="cursor-pointer max-sm:text-sm ltr:ml-1 rtl:mr-1"
                                                        >
                                                            {{ $link->title . ' + ' . core()->currency($link->price) }}
                                                        </label>
                                                    </div>

                                                    @if (
                                                        $link->sample_file
                                                        || $link->sample_url
                                                    )
                                                        <a 
                                                            href="{{ route('marketplace.downloadable.download_sample', ['type' => 'link', 'id' => $link->id]) }}"
                                                            target="_blank"
                                                            class="text-blue-700 max-sm:text-sm"
                                                        >
                                                            @lang('shop::app.products.view.type.downloadable.sample')
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach

                                            <v-error-message
                                                name="links[]"
                                                v-slot="{ message }"
                                            >
                                                <p class="text-xs italic text-red-500">
                                                    @{{ message }}
                                                </p>
                                            </v-error-message>
                                        </div>
                                    @endif
                                @endif

                                <div class="flex flex-col gap-2.5 sm:flex-row">
                                    @if (
                                        $product->type != 'bundle'
                                        && $product->type != 'downloadable'
                                    )
                                        @if ($product->type == "booking")
                                            <x-shop::quantity-changer
                                                name="booking[qty][1]"
                                                value="1"
                                                class="flex justify-center gap-2.5 rounded-2xl border border-navyBlue p-1"
                                                readonly="true"
                                            >
                                            </x-shop::quantity-changer>
                                        @else
                                            <x-shop::quantity-changer
                                                name="quantity"
                                                value="1"
                                                class="flex justify-center gap-2.5 rounded-2xl border border-navyBlue p-1"
                                            >
                                            </x-shop::quantity-changer>
                                        @endif
                                    @endif

                                    <button
                                        type="submit"
                                        class="secondary-button w-full max-w-full !px-4 !py-3"
                                        {{ ! $sellerProduct->isSaleable(1) ? 'disabled' : '' }}
                                    >
                                        @lang('marketplace::app.shop.products.add-to-cart')
                                    </button>
                                </div>

                                <p
                                    class="mt-1 cursor-pointer text-base font-medium text-navyBlue sm:mt-0"
                                    @click="showHide({{$key}})"
                                >
                                    @lang('marketplace::app.shop.products.more-info')
                                </p>

                                <!-- Add product flag form -->
                                @include('marketplace::shop.sellers.products.report', ['product' => $product, 'seller_id' => $sellerProduct->seller->id])
                            </div>
                        </div>

                        <div
                            class="grid gap-y-6"
                            v-if="showMore[{{$key}}]"
                        >
                            <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-3 1180:grid-cols-5">
                                @forelse($sellerProduct->images as $image)
                                    <img
                                        class="aspect-square rounded"
                                        src="{{ Storage::url($image->path) }}"
                                    />
                                @empty
                                    <img
                                        src="{{ bagisto_asset('images/small-product-placeholder-64b7f208.webp', 'marketplace') }}"
                                    >
                                @endforelse
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                @foreach($sellerProduct->videos as $video)
                                    <video controls width="100%">
                                        <source
                                            src="{{ Storage::url($video->path) }}"
                                            type="video/mp4"
                                        />
                                    </video>
                                @endforeach
                            </div>
    
                            <p class="text-base text-[#000000]">
                                {!! $sellerProduct->description !!}
                            </p>
    
                            <hr>
    
                            <p class="text-base text-[#757575]">
                                @lang('marketplace::app.shop.products.seller-info')
                                <br>
                                {{$sellerProduct->seller->shop_title}}
                                <br>
                                {{ $sellerProduct->seller->full_address }}
                            </p>
                        </div>
                    </div>                    
                </x-shop::form>
            @endforeach
        </script>

        <script type="module">
            app.component('v-more-seller', {
                template: '#v-more-seller-template',

                data() {
                    return {
                        showMore: {}
                    }
                },

                methods: {
                    showHide(key) {
                        this.showMore[key] = ! this.showMore[key];
                        
                        event.target.innerText = this.showMore[key] ? "@lang('marketplace::app.shop.products.hide')" : "@lang('marketplace::app.shop.products.more-info')";
                    }
                }
            });
        </script>
    @endPushOnce
</x-marketplace::shop.layouts.full>