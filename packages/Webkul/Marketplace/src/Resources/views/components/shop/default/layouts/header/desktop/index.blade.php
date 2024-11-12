@php
    $seller = auth()->guard('seller')->user();
@endphp

<header class="sticky top-0 z-10 hidden items-center justify-between border-b bg-white px-4 py-2.5 lg:flex">
    <div class="flex items-center gap-1.5">
        <!-- Logo -->
        <a href="{{ route('shop.marketplace.seller.account.dashboard.index') }}">
            @if ($seller->logo)
                <img
                     class="w-24 h-auto md:w-26 md:h-auto"
                    src="{{ Storage::url($seller->logo) }}"
                    alt="Seller Logo"
                />
            @else
                <img
                 class="w-24 h-auto md:w-26 md:h-auto"
                    src="{{ bagisto_asset('images/logo.svg') }}"
                    alt="Seller Logo"
                />
            @endif
        </a>

        <!-- Search Bar Component -->
        <x-marketplace::shop.layouts.header.search />
    </div>

    <div class="flex gap-7">
        <a
            href="{{ route('shop.home.index') }}"
            target="_blank"
            class="flex"
        >
            <span
                class="mp-home-icon cursor-pointer rounded-md p-1 text-2xl transition-all hover:bg-gray-100"
                title="@lang('marketplace::app.shop.components.layouts.header.home-page')"
            >
            </span>
        </a>

        <a
            href="{{ route('marketplace.seller.show', $seller->shop_url)}}"
            target="_blank"
            class="flex"
        >
            <span
                class="mp-store-icon cursor-pointer rounded-md p-1 text-2xl transition-all hover:bg-gray-100"
                title="@lang('marketplace::app.shop.components.layouts.header.visit-shop')"
            >
            </span>
        </a>

        <!-- Seller profile -->
        <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
            <x-slot:toggle>
                <div class="flex items-baseline">
                    <span class="mp-customers-icon cursor-pointer rounded-md p-1 text-2xl transition-all hover:bg-gray-100">
                    </span>
                </div>
            </x-slot>

            <!-- Seller Dropdown -->
            <x-slot:content class="rounded-[20px] border !p-2.5 shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)]">
                <div class="grid gap-1">
                    <a
                        class="cursor-pointer rounded-xl px-5 py-2 text-base hover:bg-gray-100"
                        href="{{ route('shop.marketplace.seller.account.profile.index') }}"
                    >
                        @lang('marketplace::app.shop.components.layouts.header.my-profile')
                    </a>

                    <!--Seller logout-->
                    <x-shop::form
                        method="DELETE"
                        action="{{ route('marketplace.seller.session.destroy') }}"
                        id="sellerLogout"
                    >
                    </x-shop::form>

                    <a
                        class="cursor-pointer rounded-xl px-5 py-2 text-base hover:bg-gray-100"
                        href="{{ route('admin.session.destroy') }}"
                        onclick="event.preventDefault(); document.getElementById('sellerLogout').submit();"
                    >
                        @lang('marketplace::app.shop.components.layouts.header.logout')
                    </a>
                </div>
            </x-slot>
        </x-shop::dropdown>
    </div>
</header>
