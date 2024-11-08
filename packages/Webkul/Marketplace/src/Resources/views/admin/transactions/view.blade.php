<x-admin::layouts>
    <x-slot:title>
        @lang('marketplace::app.admin.transactions.view.title')
    </x-slot:title>
    
    {!! view_render_event('marketplace.admin.transaction.view.before', ['transaction' => $transaction]) !!}
    
    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="py-2.5 text-xl font-bold text-gray-800 dark:text-white">
            @lang('marketplace::app.admin.transactions.view.title', ['transaction_id' => $transaction->transaction_id])
        </p>
    </div>

    <div class="flex items-center dark:text-white">
        <div class="flex gap-2 py-4">
            <span class="flex h-5 items-center rounded-xl bg-blue-600 p-2.5 text-xs font-medium text-white">
                @lang('marketplace::app.shop.sellers.account.transactions.view.payment-method', ['method' => $transaction->method])
            </span>

            <span class="text-xs font-medium">
                @lang('marketplace::app.shop.sellers.account.transactions.view.created-on', ['date' => core()->formatDate($transaction->created_at, 'd M Y')])
            </span>
        </div>
    </div>

    <div class="relative mt-8 overflow-x-auto rounded-xl border dark:border-gray-800">
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-[#F5F5F5] text-sm text-black dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <tr>
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
                </tr>
            </thead>

            <tbody>
                @foreach ($transaction->order->items as $item)
                    <tr class="border-b bg-white dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-950">
                        <td
                            class="px-6 py-4 font-medium"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.name')"
                        >
                            {{ $item->item->name }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.price')"
                        >
                            {{ core()->formatPrice($item->item->price, $transaction->order->order->order_currency_code) }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium"
                            data-value= "@lang('marketplace::app.shop.sellers.account.transactions.view.item-status')"
                        >
                            @if (in_array($item->item->type, ['downloadable', 'virtual']))
                                {{ 'N/A' }}
                            @else
                                {{ $item->item->qty_shipped}}
                            @endif
                        </td>

                        <td
                            class="px-6 py-4 font-medium"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.sub-total')"
                        >
                            <div class="flex items-center gap-2">
                                {{ core()->formatPrice($item->item->total, $transaction->order->order->order_currency_code) }}
                            </div>
                        </td>

                        <td
                            class="px-6 py-4 font-medium"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.sub-total')"
                        >
                            <div class="flex items-center gap-2">
                                {{ core()->formatPrice($item->commission, $transaction->order->order->order_currency_code) }}
                            </div>
                        </td>

                        <td
                            class="px-6 py-4 font-medium"
                            data-value="@lang('marketplace::app.shop.sellers.account.transactions.view.sub-total')"
                        >
                            <div class="flex items-center gap-2">
                                {{ core()->formatPrice($item->seller_total, $transaction->order->order->order_currency_code) }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex items-start gap-10 dark:text-white max-lg:gap-5 max-md:grid">
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

    {!! view_render_event('marketplace.admin.transaction.view.after', ['transaction' => $transaction]) !!}
</x-admin::layouts>
