<v-create-invoices>
    <a class="secondary-button px-5 py-2.5">
        @lang('marketplace::app.shop.sellers.account.orders.invoices.create-btn')
    </a>
</v-create-invoices>

@pushOnce('scripts')
    <script type="text/x-template" id="v-create-invoices-template">
        <div>
            <a
                class="secondary-button px-5 py-2.5"
                @click="$refs.invoice.open()"
            >
                @lang('marketplace::app.shop.sellers.account.orders.invoices.create-btn')
            </a>

            <!-- Invoice Create Modal -->
            <x-shop::form  
                method="POST"
                :action="route('shop.marketplace.seller.account.invoices.store', $sellerOrder->order_id)"
            >
                <x-marketplace::shop.modal ref="invoice">
                    <!-- Modal Header -->
                    <x-slot:header>
                        <p class="text-xl font-medium">
                            @lang('marketplace::app.shop.sellers.account.orders.invoices.title')         
                        </p>
                    </x-slot:header>
    
                    <!-- Modal Content -->
                    <x-slot:content class="!p-0">
                        <div class="relative overflow-x-auto border">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-[#E9E9E9] bg-[#F5F5F5] text-sm text-black">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-4 font-medium"
                                        >
                                            @lang('marketplace::app.shop.sellers.account.orders.invoices.product-name')
                                        </th>
        
                                        <th
                                            scope="col"
                                            class="px-6 py-4 font-medium"
                                        >
                                            @lang('marketplace::app.shop.sellers.account.orders.invoices.price')
                                        </th>
        
                                        <th
                                            scope="col"
                                            class="px-6 py-4 font-medium"
                                        >
                                            @lang('marketplace::app.shop.sellers.account.orders.invoices.qty')
                                        </th>
        
                                        <th
                                            scope="col"
                                            class="px-6 py-4 font-medium"
                                        >
                                            @lang('marketplace::app.shop.sellers.account.orders.invoices.total')
                                        </th>
                                    </tr>
                                </thead>
        
                                <tbody>
                                    @foreach ($sellerOrder->items as $orderItem)
                                        <tr class="border-b bg-white">
                                            <td
                                                class="px-6 py-4 font-medium text-black"
                                                data-value="@lang('marketplace::app.shop.sellers.account.orders.invoices.product-name')"
                                            >
                                                {{ $orderItem->item->name }}
                                            </td>
        
                                            <td
                                                class="px-6 py-4 font-medium text-black"
                                                data-value="@lang('marketplace::app.shop.sellers.account.orders.invoices.price')"
                                            >
                                                {{ core()->formatPrice($orderItem->item->price, $sellerOrder->order->order_currency_code) }}
                                            </td>
        
                                            <td
                                                class="px-6 py-4 font-medium text-black"
                                                data-value="@lang('marketplace::app.shop.sellers.account.orders.invoices.price')"
                                            >
                                                <x-shop::form.control-group class="!mb-0">          
                                                    <x-shop::form.control-group.control
                                                        type="text"
                                                        :name="'invoice[items]['.$orderItem->item->id.']'"
                                                        :id="'invoice[items]['.$orderItem->item->id.']'"
                                                        :value="$orderItem->item->qty_to_invoice"
                                                        rules="required|numeric|min:0" 
                                                        class="!mb-0 !w-[100px] !shadow-none"
                                                        label="Qty to invoiced"
                                                        placeholder="Qty to invoiced"
                                                    >
                                                    </x-admin::form.control-group.control>
                
                                                    <x-shop::form.control-group.error
                                                        :control-name="'invoice[items]['.$orderItem->item->id.']'"
                                                    >
                                                    </x-admin::form.control-group.error>
                                                </x-admin::form.control-group>
                                            </td>
        
                                            <td
                                                class="px-6 py-4 font-medium text-black"
                                                data-value="@lang('marketplace::app.shop.sellers.account.orders.invoices.total')"
                                            >
                                                <div class="flex items-center gap-2">
                                                    {{ core()->formatPrice($orderItem->item->total, $sellerOrder->order->order_currency_code) }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-slot:content>

                    <x-slot:footer>
                        <div class="grid gap-3">
                            <div class="flex items-center justify-between gap-4 px-4 py-8">
                                <div class="flex items-center gap-4">
                                    <p class="text-sm font-medium">
                                        @lang('marketplace::app.shop.sellers.account.orders.invoices.sub-total')         
                                    </p>

                                    <p class="text-3xl font-semibold">
                                        {{ core()->formatPrice($sellerOrder->sub_total, $sellerOrder->order->order_currency_code) }}         
                                    </p>
                                </div>

                                <button
                                    type="submit"
                                    class="primary-button px-4 py-4 md:px-7"
                                >
                                    @lang('marketplace::app.shop.sellers.account.orders.invoices.title')
                                </button>
                            </div>
                        </div>
                    </x-slot:footer>
                </x-marketplace::shop.modal>
            </x-shop::form>
        </div>
    </script>

    <script type="module">
        app.component('v-create-invoices', {
            template: '#v-create-invoices-template',
        });
    </script>
@endPushOnce