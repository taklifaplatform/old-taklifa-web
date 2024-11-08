<x-marketplace::shop.layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.transactions.view.title', ['transaction_id' => $transaction->transaction_id])
    </x-slot>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_transactions_view" />
    @endSection

    {!! view_render_event('marketplace.seller.transaction.view.before', ['transaction' => $transaction]) !!}

    <!-- Page Header -->
    <h2 class="text-2xl font-medium">
        @lang('marketplace::app.shop.sellers.account.transactions.view.title', ['transaction_id' => $transaction->transaction_id])
    </h2>

    <div class="mt-4 flex items-center justify-between">
        <div class="flex items-center gap-2 py-4">
            <span class="flex h-5 items-center rounded-xl bg-[#02B1FD] p-2.5 text-xs font-medium text-white">
                @lang('marketplace::app.shop.sellers.account.transactions.view.payment-method', ['method' => $transaction->method])
            </span>

            <span class="text-xs font-medium opacity-80">
                @lang('marketplace::app.shop.sellers.account.transactions.view.created-on', ['date' => core()->formatDate($transaction->created_at, 'd M Y')])
            </span>
        </div>

        @if (seller()->hasPermission('transactions.print'))
            <a
                href="{{route('shop.marketplace.seller.account.transaction.print', $transaction->id)}}"
                class="secondary-button flex items-center gap-x-2.5 border-[#E9E9E9] px-5 py-3 font-normal"
            >
                @lang('marketplace::app.shop.sellers.account.transactions.view.print')
            </a>
        @endif
    </div>

    {!! view_render_event('marketplace.seller.transaction.list.before') !!}
    
    <div class="relative mt-4 overflow-x-auto rounded-xl border">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-[#E9E9E9] bg-[#F5F5F5] text-sm text-black">
                <tr>
                    {!! view_render_event('marketplace.seller.transaction.list.heading_row.before') !!}
                    
                    <th
                        scope="col"
                        class="w-3/5 px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.transactions.view.name')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.transactions.view.price')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.transactions.view.qty')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.transactions.view.total')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.transactions.view.commission')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.transactions.view.seller-total')
                    </th>

                    {!! view_render_event('marketplace.seller.transaction.list.heading_row.after') !!}
                </tr>
            </thead>

            <tbody>
                @foreach ($transaction->order->items as $item)
                    <tr class="border-b bg-white">
                        {!! view_render_event('marketplace.seller.transaction.list.record_row.before', ['item' => $item]) !!}
                        
                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.name')"
                        >
                            {{ $item->item->name }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.price')"
                        >
                            {{ core()->formatPrice($item->item->price, $transaction->order->order->order_currency_code) }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value= "@lang('marketplace::app.shop.sellers.account.transactions.view.item-status')"
                        >
                            @if (in_array($item->item->type, ['downloadable', 'virtual']))
                                {{ 'N/A' }}
                            @else
                                {{ $item->item->qty_shipped}}
                            @endif
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.sub-total')"
                        >
                            <div class="flex items-center gap-2">
                                {{ core()->formatPrice($item->item->total, $transaction->order->order->order_currency_code) }}
                            </div>
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.sub-total')"
                        >
                            <div class="flex items-center gap-2">
                                {{ core()->formatPrice($item->commission, $transaction->order->order->order_currency_code) }}
                            </div>
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.sub-total')"
                        >
                            <div class="flex items-center gap-2">
                                {{ core()->formatPrice($item->seller_total, $transaction->order->order->order_currency_code) }}
                            </div>
                        </td>

                        {!! view_render_event('marketplace.seller.transaction.list.record_row.after', ['item' => $item]) !!}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex items-start gap-10 max-lg:gap-5 max-md:grid">
        <div class="flex-auto max-md:mt-8">
            <div class="flex justify-end">
                <div class="grid max-w-max gap-2">
                    <div class="flex w-full justify-between gap-5">
                        <p class="text-sm">
                            @lang('marketplace::app.shop.sellers.account.transactions.view.subtotal')
                        </p>

                        <div class="flex gap-5">
                            <p class="text-sm">-</p>

                            <p class="text-sm">
                                {{ core()->formatPrice($transaction->order->sub_total, $transaction->order->order->order_currency_code) }}
                            </p>
                        </div>
                    </div>

                    @if ($transaction->order->order->haveStockableItems())
                        <div class="flex w-full justify-between gap-5">
                            <p class="text-sm">
                                @lang('marketplace::app.shop.sellers.account.transactions.view.shipping-handling')
                            </p>

                            <div class="flex gap-5">
                                <p class="text-sm">-</p>

                                <p class="text-sm">
                                    {{ core()->formatPrice(0, $transaction->order->order->order_currency_code) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex w-full justify-between gap-5">
                        <p class="text-sm">
                            @lang('marketplace::app.shop.sellers.account.transactions.view.tax')
                        </p>

                        <div class="flex gap-5">
                            <p class="text-sm">-</p>

                            <p class="text-sm">
                                {{ core()->formatPrice($transaction->order->tax_amount, $transaction->order->order->order_currency_code) }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex w-full justify-between gap-5">
                        <p class="text-sm">
                            @lang('marketplace::app.shop.sellers.account.transactions.view.commission')
                        </p>

                        <div class="flex gap-5">
                            <p class="text-sm">-</p>

                            <p class="text-sm">
                                {{ core()->formatPrice($transaction->order->base_commission, $transaction->order->order->order_currency_code) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex w-full justify-between gap-5">
                        <p class="text-sm font-semibold">
                            @lang('marketplace::app.shop.sellers.account.transactions.view.seller-total')
                        </p>

                        <div class="flex gap-5">
                            <p class="text-sm">-</p>

                            <p class="text-sm font-semibold">
                                {{ core()->formatPrice($transaction->order->base_seller_total, $transaction->order->order->order_currency_code) }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {!! view_render_event('marketplace.seller.transaction.list.after') !!}

    {!! view_render_event('marketplace.seller.transaction.view.after', ['transaction' => $transaction]) !!}
</x-marketplace::shop.layouts>