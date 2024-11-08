<x-marketplace::shop.layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.transactions.index.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_transactions" />
    @endSection

    <!-- Page Header -->
    <h2 class="text-2xl font-medium">
        @lang('marketplace::app.shop.sellers.account.transactions.index.title')
    </h2>

    <div class="mt-8 grid gap-4 rounded-lg border p-5 md:grid-cols-3 md:gap-8">
        <div class="grid py-2.5">
            <h3 class="text-2xl font-medium">
                {{ core()->formatPrice($statistics['total_seller_sale']) }}
            </h3>

            <p class="text-sm font-normal text-[#757575]">
                @lang('marketplace::app.shop.sellers.account.transactions.index.total-seller-sale')
            </p>
        </div>

        <div class="grid py-2.5">
            <h3 class="text-2xl font-medium">
                {{ core()->formatPrice($statistics['total_commission']) }}
            </h3>

            <p class="text-sm font-normal text-[#757575]">
                @lang('marketplace::app.shop.sellers.account.transactions.index.total-admin-commission')
            </p>
        </div>

        <div class="grid py-2.5">
            <h3 class="text-2xl font-medium">
                {{ core()->formatPrice($statistics['total_sale']) }}
            </h3>

            <p class="text-sm font-normal text-[#757575]">
                @lang('marketplace::app.shop.sellers.account.transactions.index.total-sale-invoiced')
            </p>
        </div>

        <div class="grid py-2.5">
            <h3 class="text-2xl font-medium">
                {{ core()->formatPrice($statistics['total_payout']) }}
                @php
                    $payoutPercentage = 0;

                    if ($statistics['total_payout']) {
                        $payoutPercentage = ($statistics['total_payout'] / $statistics['total_seller_sale']) * 100;
                    }
                @endphp
                <span class="text-sm font-normal">
                    {{ number_format($payoutPercentage, 2) }} %
                </span>
            </h3>

            <p class="text-sm font-normal text-[#757575]">
                @lang('marketplace::app.shop.sellers.account.transactions.index.total-payout')
            </p>
        </div>

        <div class="grid py-2.5">
            <h3 class="text-2xl font-medium">
                {{ core()->formatPrice($statistics['remaining_payout']) }}
                @php
                    $remainingPercentage = 0;
    
                    if ($statistics['remaining_payout']) {
                        $remainingPercentage = $statistics['remaining_payout'] * 100 / $statistics['total_sale'];
                    }
                @endphp

                <span class="text-sm font-normal">
                    {{ number_format($remainingPercentage, 2) }} %
                </span>
            </h3>

            <p class="text-sm font-normal text-[#757575]">
                @lang('marketplace::app.shop.sellers.account.transactions.index.remaining-payout')
            </p>
        </div>
    </div>

    {!! view_render_event('marketplace.sellers.account.sales.transactions.list.before') !!}

    <!-- Datagrid -->
    <x-shop::datagrid :src="route('shop.marketplace.seller.account.transaction.index')" />

    {!! view_render_event('marketplace.sellers.account.sales.transactions.list.after') !!}

</x-marketplace::shop.layouts>