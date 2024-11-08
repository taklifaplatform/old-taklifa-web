<v-create-shipment>
    <a class="secondary-button px-5 py-2.5">
        @lang('marketplace::app.shop.sellers.account.orders.shipments.create-btn')
    </a>
</v-create-shipment>

@pushOnce('scripts')
    <script type="text/x-template" id="v-create-shipment-template">
        <div>
            <a
                class="secondary-button px-5 py-2.5"
                @click="$refs.shipment.open()"
            >
                @lang('marketplace::app.shop.sellers.account.orders.shipments.create-btn')
            </a>

            <!-- Shipment Create Modal -->
            <x-shop::form  
                method="POST"
                :action="route('shop.marketplace.seller.account.shipments.store', $sellerOrder->order_id)"
            >
                <x-marketplace::shop.modal ref="shipment">
                    <!-- Modal Header -->
                    <x-slot:header>
                        <p class="text-xl font-medium">
                            @lang('marketplace::app.shop.sellers.account.orders.shipments.title')
                        </p>
                    </x-slot:header>

                    <!-- Modal Content -->
                    <x-slot:content class="!p-4">
                        <div class="grid">
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                                <!-- Carrier Name -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required flex text-xs font-medium">
                                        @lang('marketplace::app.shop.sellers.account.orders.shipments.carrier-title')
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control
                                        type="text"
                                        name="shipment[carrier_title]"
                                        class="!mb-0 !shadow-none"
                                        id="shipment[carrier_title]"
                                        rules="required"
                                        :label="trans('marketplace::app.shop.sellers.account.orders.shipments.carrier-title')"
                                        :placeholder="trans('marketplace::app.shop.sellers.account.orders.shipments.carrier-title')"
                                    >
                                    </x-shop::form.control-group.control>

                                    <x-shop::form.control-group.error
                                        control-name="shipment[carrier_title]"
                                        class="mt-1 flex"
                                    >
                                    </x-shop::form.control-group.error>
                                </x-shop::form.control-group>

                                <!-- Tracking Number -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required flex text-xs font-medium">
                                        @lang('marketplace::app.shop.sellers.account.orders.shipments.tracking-id')
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control
                                        type="text"
                                        name="shipment[track_number]"
                                        class="!mb-0 !shadow-none"
                                        id="shipment[track_number]"
                                        rules="required"
                                        :label="trans('marketplace::app.shop.sellers.account.orders.shipments.tracking-id')"
                                        :placeholder="trans('marketplace::app.shop.sellers.account.orders.shipments.tracking-id')"
                                    >
                                    </x-shop::form.control-group.control>

                                    <x-shop::form.control-group.error
                                        class="mt-1 flex"
                                        control-name="shipment[track_number]"
                                    >
                                    </x-shop::form.control-group.error>
                                </x-shop::form.control-group>

                                <!-- Source -->
                                <x-shop::form.control-group>
                                    <x-shop::form.control-group.label class="required flex text-xs font-medium">
                                        @lang('marketplace::app.shop.sellers.account.orders.shipments.source')
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control
                                        type="select"
                                        name="shipment[source]"
                                        class="!mb-0 !shadow-none"
                                        id="shipment[source]" 
                                        rules="required"
                                        :label="trans('marketplace::app.shop.sellers.account.orders.shipments.source')"
                                        :placeholder="trans('marketplace::app.shop.sellers.account.orders.shipments.source')"
                                        v-model="source"
                                        @change="onSourceChange"
                                    >
                                    @foreach ($sellerOrder->order->channel->inventory_sources as $inventorySource)
                                        <option value="{{ $inventorySource->id }}">
                                            {{ $inventorySource->name }}
                                        </option>
                                    @endforeach
                                    </x-shop::form.control-group.control>

                                    <x-shop::form.control-group.error
                                        class="mt-1 flex"
                                        control-name="shipment[source]"
                                    >
                                    </x-shop::form.control-group.error>
                                </x-shop::form.control-group>
                            </div>

                            <div class="relative overflow-x-auto rounded-xl border">
                                <table class="w-full text-left text-sm">
                                    <thead class="border-b border-[#E9E9E9] bg-[#F5F5F5] text-sm text-black">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="py-4 pl-5 font-medium"
                                            >
                                                @lang('marketplace::app.shop.sellers.account.orders.shipments.product-name')
                                            </th>
            
                                            <th
                                                scope="col"
                                                class="py-4 pl-5 font-medium"
                                            >
                                                @lang('marketplace::app.shop.sellers.account.orders.shipments.ordered-qty')
                                            </th>
            
                                            <th
                                                scope="col"
                                                class="py-4 pl-5 font-medium"
                                            >
                                                @lang('marketplace::app.shop.sellers.account.orders.shipments.shipped-qty')
                                            </th>
            
                                            <th
                                                scope="col"
                                                class="py-4 pl-5 font-medium"
                                            >
                                                @lang('marketplace::app.shop.sellers.account.orders.shipments.qty')
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sellerOrder->items as $orderItem)
                                            <tr class="border-b bg-white">
                                                <td
                                                    class="py-4 pl-5 font-medium text-black"
                                                    data-value="@lang('marketplace::app.shop.sellers.account.orders.shipments.product-name')"
                                                >
                                                    <div class="grid justify-items-start">
                                                        <p class="text-sm font-medium">
                                                            {{ $orderItem->item->name }}
                                                        </p>
                                                        <p class="text-sm font-normal">
                                                            @lang('marketplace::app.shop.sellers.account.orders.shipments.sku', ['sku' => $orderItem->item->sku])
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
                                                    class="py-4 pl-5 font-medium text-black"
                                                    data-value="@lang('marketplace::app.shop.sellers.account.orders.shipments.ordered-qty')"
                                                >
                                                    {{ $orderItem->item->qty_ordered }}
                                                </td>
            
                                                <td
                                                    class="py-4 pl-5 font-medium text-black"
                                                    data-value= "@lang('marketplace::app.shop.sellers.account.orders.shipments.shipped-qty')"
                                                >
                                                    {{ $orderItem->item->qty_shipped }}
                                                </td>
            
                                                <td
                                                    class="py-4 pl-5 font-medium text-black"
                                                    data-value="@lang('marketplace::app.shop.sellers.account.orders.shipments.qty')"
                                                >
                                                    @foreach ($sellerOrder->order->channel->inventory_sources as $inventorySource)
                                                        <div class="grid justify-items-start gap-1">
                                                            @php
                                                                $inputName = "shipment[items][{$orderItem->item->id}][{$inventorySource->id}]";
                                                            @endphp
            
                                                            <!--Inventory Source -->
                                                            @php
                                                                $product = $orderItem->item->getTypeInstance()->getOrderedItem($orderItem->item)->product;
            
                                                                $sourceQty = $product?->type == 'bundle' ? $orderItem->item->qty_ordered : $product?->mp_inventory_source_qty($inventorySource->id);
                                                            @endphp

                                                            <!-- Quantity To Ship -->
                                                            <x-shop::form.control-group class="!mb-0">
                                                                <x-shop::form.control-group.control
                                                                    type="text"
                                                                    :name="$inputName"
                                                                    :id="$inputName" 
                                                                    :value="$orderItem->item->qty_to_ship"
                                                                    :rules="'required|numeric|min_value:0|max_value:'.$orderItem->item->qty_ordered"
                                                                    class="!mb-0 !w-16 !shadow-none"
                                                                    :label="trans('marketplace::app.shop.sellers.account.orders.shipments.qty')"
                                                                    data-original-quantity="{{ $orderItem->item->qty_to_ship }}"
                                                                    ::disabled="'{{ empty($sourceQty) }}' || source != '{{ $inventorySource->id }}'"
                                                                    :ref="$inputName"
                                                                >
                                                                </x-shop::form.control-group.control>
                                    
                                                                <x-shop::form.control-group.error
                                                                    :control-name="$inputName"
                                                                    class="mt-1"
                                                                >
                                                                </x-shop::form.control-group.error>
                                                            </x-shop::form.control-group>
            
                                                            <!-- Available Quantity -->
                                                            <p class="text-sm font-medium text-[#27AE60]">
                                                                @lang('marketplace::app.shop.sellers.account.orders.shipments.avl-qty', ['qty' => $sourceQty])
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </x-slot:content>

                    <x-slot:footer>
                        <div class="flex items-center justify-end py-4">    
                            <button
                                type="submit"
                                class="primary-button px-7 py-4"
                            >
                                @lang('marketplace::app.shop.sellers.account.orders.shipments.title')
                            </button>
                        </div>
                    </x-slot:footer>
                </x-marketplace::shop.modal>
            </x-shop::form>
        </div>
    </script>

    <script type="module">
    app.component('v-create-shipment', {
        template: '#v-create-shipment-template',

        data() {
            return {
                source: "",
            };
        },

        methods: {
            onSourceChange() {
                this.setOriginalQuantityToAllShipmentInputElements();
            },

            getAllShipmentInputElements() {
                let allRefs = this.$refs;

                let allInputElements = [];

                Object.keys(allRefs).forEach((key) => {
                    if (key.startsWith('shipment')) {
                        allInputElements.push(allRefs[key]);
                    }
                });

                return allInputElements;
            },

            setOriginalQuantityToAllShipmentInputElements() {
                this.getAllShipmentInputElements().forEach((element) => {
                    let data = Object.assign({}, element.dataset);

                    element.value = data.originalQuantity;
                });
            }
        },
    });
    </script>
@endPushOnce