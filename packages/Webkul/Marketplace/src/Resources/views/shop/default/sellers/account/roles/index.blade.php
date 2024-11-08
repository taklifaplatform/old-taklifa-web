<x-marketplace::shop.layouts>    
    <!-- Page Title -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.roles.index.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="roles" />
    @endSection

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <!-- Page Title -->
        <h2 class="text-2xl font-medium">
            @lang('marketplace::app.shop.sellers.account.roles.index.title')
        </h2>

        @if (seller()->hasPermission('roles.create'))
            <a
                href="{{ route('shop.marketplace.seller.account.roles.create') }}"
                class="primary-button px-5 py-2.5"
            >
                @lang('marketplace::app.shop.sellers.account.roles.index.create-btn')
            </a>
        @endif
    </div>

    {!! view_render_event('marketplace.seller.roles.list.before') !!}
    
    <!-- Datagrid -->
    <x-shop::datagrid :src="route('shop.marketplace.seller.account.roles.index')" />
    
    {!! view_render_event('marketplace.seller.roles.list.after') !!}
</x-marketplace::shop.layouts>
