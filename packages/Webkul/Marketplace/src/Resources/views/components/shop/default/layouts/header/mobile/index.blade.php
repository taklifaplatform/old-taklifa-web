@php
    $seller = auth()->guard('seller')->user();
@endphp

<header class="sticky top-0 z-10 hidden border-b bg-white p-4 max-lg:block">
    <!-- Mobile Menu -->
    <div class="grid gap-2">
        <div class="flex items-center justify-between gap-1.5">
            <div class="flex gap-2.5">
                <!-- Hamburger Menu -->
                <i
                    class="icon-hamburger hidden cursor-pointer rounded-md py-1 text-2xl hover:bg-gray-100 max-lg:block"
                    @click="$refs.sideMenuDrawer.open()"
                >
                </i>

                <!-- Logo -->
                <a href="{{ route('shop.marketplace.seller.account.dashboard.index') }}">
                    @if ($seller->logo)
                        <img
                            class="h-10"
                            src="{{ Storage::url($seller->logo) }}"
                            alt="Seller Logo"
                        />
                    @else
                        <img
                            src="{{ bagisto_asset('images/logo.svg') }}"
                            alt="Seller Logo"
                        />
                    @endif
                </a>
            </div>
    
            <div class="flex gap-2.5">
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
        </div>
    
        <!-- Search Bar Component -->
        <x-marketplace::shop.layouts.header.search />
    </div>
</header>

<!-- Menu Sidebar Drawer -->
<x-marketplace::shop.drawer
    position="left"
    width="270px"
    ref="sideMenuDrawer"
>
    <!-- Drawer Header -->
    <x-slot:header>
        <div class="flex items-center justify-between">
            @if ($seller->logo)
                <img
                    class="h-10"
                    src="{{ Storage::url($seller->logo) }}"
                    alt="Seller Logo"
                />
            @else
                <img
                    src="{{ bagisto_asset('images/logo.svg') }}"
                    alt="Seller Logo"
                />
            @endif
        </div>
    </x-slot>

    <!-- Drawer Content -->
    <x-slot:content>
        <div class="journal-scroll h-[calc(100vh-120px)] overflow-auto">
            <!-- Account Navigation Menus -->
            @foreach (menu()->getItems('seller') as $menuItem)
                @if (seller()->hasPermission($menuItem->key))
                    <div class="{{ $menuItem->isActive() ? 'active' : 'inactive' }}">
                        <a
                            href="{{ $menuItem->getUrl() }}"
                            class="flex cursor-pointer justify-between border-[#E9E9E9] p-5 hover:bg-[#f3f4f682]"
                        >
                            <div class="flex items-center gap-x-4">
                                <span class="{{ $menuItem->getIcon() }} text-2xl"></span>

                                <span class="whitespace-nowrap font-medium group-[.sidebar-collapsed]/container:hidden">
                                    @lang($menuItem->getName())
                                </span>
                            </div>
                            
                            @if ($menuItem->isActive())
                                <span class="mp-arrow-right-icon text-2xl max-md:hidden"></span>
                            @endif
                        </a>

                        @if ($menuItem->haveChildren())
                            <div class="{{ $menuItem->isActive() ? '!grid bg-gray-100' : '' }} hidden min-w-[180px] ltr:pl-12 rtl:pr-12 rounded-b-lg z-[100] overflow-hidden group-[.sidebar-collapsed]/container:!hidden">
                                @foreach ($menuItem->getChildren() as $subMenuItem)
                                    <a
                                        href="{{ $subMenuItem->getUrl() }}"
                                        class="{{ $subMenuItem->isActive() ? 'text-navyBlue' : '' }} p-2.5 font-medium"
                                    >
                                        @lang($subMenuItem->getName())
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </x-slot>
</x-marketplace::shop.drawer>