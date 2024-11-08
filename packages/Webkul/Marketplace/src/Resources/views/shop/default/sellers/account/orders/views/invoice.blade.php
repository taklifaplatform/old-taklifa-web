@foreach ($sellerOrder->invoices as $invoice)
    <div class="flex items-center justify-between md:mt-6">
        <div class="flex w-4/5 max-sm:flex-wrap">
            <p class="text-base font-medium">
                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.individual-invoice', ['invoice_id' => $invoice->increment_id ?? $invoice->id]),
            </p>&nbsp;
            <p class="text-base font-medium">
                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.created-on', ['date_time' => core()->formatDate($invoice->created_at, 'd/m/y h:i:s')])
            </p>
        </div>

        @if (seller()->hasPermission('orders.print_invoice'))
            <a
                class="text-[#0A49A7]"
                href="{{ route('shop.marketplace.seller.account.invoices.print', $invoice->id) }}"
            >
                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.print')
            </a>
        @endif
    </div>

    <div class="relative mt-6 overflow-x-auto rounded-xl border">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-[#E9E9E9] bg-[#F5F5F5] text-sm text-black">
                <tr>
                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.name')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.price')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.qty')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.total')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.tax-amount')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.discount')
                    </th>

                    <th
                        scope="col"
                        class="px-6 py-4 font-medium"
                    >
                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach ($invoice->items as $orderItem)
                    <tr class="border-b bg-white">
                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.orders.view.invoices.name')"
                        >
                            <div class="flex flex-col">
                                <p class="text-sm font-medium">
                                    {{ $orderItem->item->name }}
                                </p>
                                <p class="text-sm font-normal">
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.sku', ['sku' => $orderItem->item->sku])
                                </p>

                                @if (isset($orderItem->item->additional['attributes']))
                                    <div>
                                        @foreach ($orderItem->item->additional['attributes'] as $attribute)
                                            <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}<br>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.orders.view.invoices.price')"
                        >
                            {{ core()->formatPrice($orderItem->item->price, $sellerOrder->order->order_currency_code) }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.orders.view.invoices.qty')"
                        >
                            {{ $orderItem->item->qty }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')"
                        >
                            {{ core()->formatPrice($orderItem->item->total, $sellerOrder->order->order_currency_code) }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.orders.view.invoices.tax-amount')"
                        >
                            {{ core()->formatPrice($orderItem->item->tax_amount, $sellerOrder->order->order_currency_code) }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.orders.view.invoices.discount')"
                        >
                            {{ core()->formatPrice($orderItem->item->discount_amount, $sellerOrder->order->order_currency_code) }}
                        </td>

                        <td
                            class="px-6 py-4 font-medium text-black"
                            data-value="@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')"
                        >
                            {{ core()->formatPrice($orderItem->item->total + $orderItem->item->tax_amount, $sellerOrder->order->order_currency_code) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-start gap-10 py-8 max-lg:gap-5 max-md:grid">
        <div class="flex-auto max-md:mt-8">
            <div class="flex justify-end">
                <div class="grid max-w-max gap-2">
                    <!-- Sub Total -->
                    @if (core()->getConfigData('sales.taxes.sales.display_subtotal') != 'including_tax')
                        <div class="flex w-full justify-between gap-5">
                            <p class="text-sm">
                                @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total-excl-tax')
                                @else
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')
                                @endif
                            </p>

                            <div class="flex gap-5">
                                <p class="text-sm">-</p>
                                
                                <p class="text-sm">
                                    {{ core()->formatPrice($invoice->sub_total, $sellerOrder->order->order_currency_code) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax'
                        || core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both'
                    )
                        <div class="flex w-full justify-between gap-5">
                            <p class="text-sm">
                                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total-incl-tax')
                            </p>

                            <div class="flex gap-5">
                                <p class="text-sm">-</p>
                                
                                <p class="text-sm">
                                    {{ core()->formatPrice($invoice->sub_total_incl_tax, $sellerOrder->order->order_currency_code) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Shipping Handling -->
                    @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') != 'including_tax')
                        <div class="flex w-full justify-between gap-5">
                            <p class="text-sm">
                                @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.shipping-handling-excl-tax')
                                @else
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.shipping-handling')
                                @endif
                            </p>

                            <div class="flex gap-5">
                                <p class="text-sm">-</p>
                                
                                <p class="text-sm">
                                    {{ core()->formatPrice(0, $sellerOrder->order->order_currency_code) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'including_tax'
                        || core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both'
                    )
                        <div class="flex w-full justify-between gap-5">
                            <p class="text-sm">
                                @lang('marketplace::app.shop.sellers.account.orders.view.shipping-handling-incl-tax')
                            </p>

                            <div class="flex gap-5">
                                <p class="text-sm">-</p>
                                
                                <p class="text-sm">
                                    {{ core()->formatPrice(0, $sellerOrder->order->order_currency_code) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex w-full justify-between gap-5">
                        <p class="text-sm">
                            @lang('marketplace::app.shop.sellers.account.orders.view.invoices.discount')
                        </p>

                        <div class="flex gap-5">
                            <p class="text-sm">-</p>

                            <p class="text-sm">
                                {{ core()->formatPrice($invoice->discount_amount, $sellerOrder->order->order_currency_code) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex w-full justify-between gap-5">
                        <p class="text-sm">
                            @lang('marketplace::app.shop.sellers.account.orders.view.invoices.tax')
                        </p>

                        <div class="flex gap-5">
                            <p class="text-sm">-</p>

                            <p class="text-sm">
                                {{ core()->formatPrice($invoice->tax_amount, $sellerOrder->order->order_currency_code) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex w-full justify-between gap-5">
                        <p class="text-sm font-semibold">
                            @lang('marketplace::app.shop.sellers.account.orders.view.invoices.grand-total')
                        </p>

                        <div class="flex gap-5">
                            <p class="text-sm">-</p>

                            <p class="text-sm font-semibold">
                                {{ core()->formatPrice($invoice->grand_total - $invoice->shipping_amount, $sellerOrder->order->order_currency_code) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
