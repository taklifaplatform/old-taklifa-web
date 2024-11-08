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

        @stack('meta')

        <link
            rel="icon"
            sizes="16x16"
            href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico') }}"
        />

        {!! view_render_event('bagisto.shop.marketplace.layout.head.before') !!}
        
        @bagistoVite(['src/Resources/assets/css/app.css'])

        @bagistoVite(['src/Resources/assets/css/shop.css', 'src/Resources/assets/js/app.js'], 'marketplace')

        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" as="style">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap">

        <link rel="preload" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" as="style">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap">

        @stack('styles')

        {!! view_render_event('bagisto.shop.marketplace.layout.head.after') !!}
    </head>

    <body>
        {!! view_render_event('bagisto.shop.marketplace.layout.body.before') !!}

        <div id="app">
            <!-- Flash Message Blade Component -->
            <x-shop::flash-group />

            <!-- Confirm Modal Blade Component -->
            <x-shop::modal.confirm />

            <!-- Page Header Blade Component -->
            <x-marketplace::shop.layouts.header />

            {!! view_render_event('bagisto.shop.marketplace.layout.content.before') !!}

            <!-- Page Content Blade Component -->
            <div
                class="flex gap-4 group/container {{ (request()->cookie('sidebar_collapsed') ?? 0) ? 'sidebar-collapsed' : 'sidebar-not-collapsed' }}"
                ref="appLayout"
            >
                <!-- Page Sidebar Blade Component -->
                <x-marketplace::shop.layouts.sidebar />

                <div class="max-w-full flex-1 bg-white px-4 pb-6 pt-3 transition-all duration-300 max-lg:!px-4 ltr:pl-[286px] group-[.sidebar-collapsed]/container:ltr:pl-[85px] rtl:pr-[286px] group-[.sidebar-collapsed]/container:rtl:pr-[85px]">
                    <x-marketplace::shop.layouts.account.breadcrumb />
                    
                    <!-- Page Content Blade Component -->
                    {{ $slot }}
                </div>
            </div>

            {{-- <x-marketplace::shop.layouts.footer /> --}}

            {!! view_render_event('bagisto.shop.marketplace.layout.content.after') !!}
        </div>

        {!! view_render_event('bagisto.shop.marketplace.layout.body.after') !!}

        @stack('scripts')
    </body>
</html>