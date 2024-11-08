@props([
    'hasHeader' => true,
    'hasFeature' => true,
    'hasFooter' => true,
])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ core()->getCurrentLocale()->direction }}">

<head>
    <title>{{ $title ?? '' }}</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ url()->to('/') }}">
    <meta name="currency-code" content="{{ core()->getCurrentCurrencyCode() }}">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">
    <script defer data-domain="taklifa.com" src="https://plausible.io/js/script.js"></script>
    @stack('meta')

    <link rel="icon" sizes="16x16"
        href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico') }}" />

    @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        as="style">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap">

    <link rel="preload" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" as="style">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap">

    @stack('styles')

    <style>
        {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
    </style>

    {!! view_render_event('bagisto.shop.layout.head') !!}
</head>

<body>
    {!! view_render_event('bagisto.shop.layout.body.before') !!}

    <a href="#main" class="skip-to-main-content-link">Skip to main content</a>

    <div id="app">
        <!-- Flash Message Blade Component -->
        <x-shop::flash-group />

        <!-- Confirm Modal Blade Component -->
        <x-shop::modal.confirm />
        <!-- Page Header Blade Component -->

        @if ($hasHeader)
            <x-shop::layouts.header />

            @php
                if ($attributes->has('category')) {
                    $mainCategory = $attributes->get('category');
                } else {
                    $mainCategory = \Webkul\Category\Models\Category::where('position', 0)->first();
                }

                if (!\Webkul\Category\Models\Category::where('parent_id', $mainCategory?->id)->exists()) {
                    $mainCategory = \Webkul\Category\Models\Category::find($mainCategory?->parent_id);
                }
            @endphp

            @if ($mainCategory && !Request::is('/'))
                <div class="w-full">
                    <x-shop::categories.carousel title="Our Services" :src="route('shop.api.categories.index', [
                        'parent_id' => $mainCategory->id,
                        'limit' => 1000,
                    ])" :navigation-link="route('shop.home.index')" />
                </div>
            @endif

            @php
                $rootCategoryId = core()->getCurrentChannel()->root_category_id;
                $rootCategory = \Webkul\Category\Models\Category::find($rootCategoryId);

                $parentCat = \Webkul\Category\Models\Category::where('parent_id', $rootCategoryId)->first();
            @endphp

            @if (Request::is('/'))
                <div class="w-full bg-green-900">
                    <div class="grid grid-cols-2">
                        <div class="border-b-2 border-l-2 p-8">
                            <h2 class="text-center text-white text-lg font-bold mb-8">مشاريع للبناء والتشطيب</h2>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 justify-items-center">
                                @foreach ($parentCat->childs->slice(0, ceil($parentCat->childs->count() / 2)) as $category)
                                    <div class="flex flex-col items-center mb-4 text-center">
                                        @if (isset($category->logo_path))
                                            <a href="{{ url($category->slug) }}"
                                                class="img-gradient max-md:w-[70px] w-[90px] max-md:h-[70px] h-[90px] rounded-full p-1"
                                                aria-label="{{ $category->name }}">
                                                <x-shop::media.images.lazy
                                                    src="{{ asset('storage/' . $category->logo_path) }}" width="100%"
                                                    height="100%"
                                                    class="max-md:w-[64px] w-[84px] max-md:h-[64px] h-[84px] rounded-full"
                                                    alt="{{ $category->name }}" style="z-index: 2;" />
                                            </a>
                                        @else
                                            <div
                                                class="w-[84px] h-[84px] bg-white rounded-full mb-2 border border-gray-300">
                                            </div>
                                        @endif
                                        <span class="text-white text-sm mt-2">{{ $category->name }}</span>
                                    </div>
                                @endforeach
                                @foreach ($parentCat->childs->slice(ceil($parentCat->childs->count() / 2)) as $category)
                                    <div class="flex flex-col items-center mb-4 text-center">
                                        @if (isset($category->logo_path))
                                            <a href="category.slug"
                                                class="img-gradient max-md:w-[70px] w-[90px] max-md:h-[70px] h-[90px] rounded-full p-1"
                                                aria-label="category.name">
                                                <x-shop::media.images.lazy
                                                    src="{{ asset('storage/' . $category->logo_path) }}" width="100%"
                                                    height="100%"
                                                    class="max-md:w-[64px] w-[84px] max-md:h-[64px] h-[84px] rounded-full"
                                                    alt="{{ $category->name }}" style="z-index: 2;" />
                                            </a>
                                        @else
                                            <div
                                                class="w-[84px] h-[84px] bg-white rounded-full mb-2 border border-gray-300">
                                            </div>
                                        @endif
                                        <span class="text-white text-sm mt-2">{{ $category->name }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-b-2 border-l-2 p-8">
                            <h2 class="text-center text-white text-lg font-bold mb-8">مصانع ومحلّات المورد</h2>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 justify-items-center">
                                @foreach ($rootCategory->children as $category)
                                    @if ($category->name !== 'خدماتنا')
                                        <div class="flex flex-col items-center mb-4 text-center">
                                            <a href="{{ url($category->slug) }}"
                                                class="img-gradient max-md:w-[70px] w-[90px] max-md:h-[70px] h-[90px] rounded-full p-1"
                                                aria-label="{{ $category->name }}">
                                                <x-shop::media.images.lazy
                                                    src="{{ asset('storage/' . $category->logo_path) }}" width="100%"
                                                    height="100%"
                                                    class="max-md:w-[64px] w-[84px] max-md:h-[64px] h-[84px] rounded-full"
                                                    alt="{{ $category->name }}" style="z-index: 2;" />
                                            </a>
                                            <span class="text-white text-sm mt-2">{{ $category->name }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif


        {!! view_render_event('bagisto.shop.layout.content.before') !!}

        <!-- Page Content Blade Component -->
        <main id="main" class="bg-white">
            @if (!Request::is('/'))
                {{ $slot }}
            @endif
        </main>


        {!! view_render_event('bagisto.shop.layout.content.after') !!}


        <!-- Page Services Blade Component -->
        @if ($hasFeature)
            <x-shop::layouts.services />
        @endif

        <!-- Page Footer Blade Component -->
        @if ($hasFooter)
            <x-shop::layouts.footer />
        @endif
    </div>

    {!! view_render_event('bagisto.shop.layout.body.after') !!}

    @stack('scripts')

    <script type="text/javascript">
        {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
    </script>
</body>

</html>
