<v-inventories>
    <!-- Panel Content -->
    <div class="mb-5 text-sm text-gray-600">
        <div class="relative mb-2.5 flex items-center">
            <span class="inline-block rounded-full bg-yellow-500 p-1 ltr:mr-1 rtl:ml-1"></span>

            <input
                type="hidden"
                name="vendor_id"
                value="{{ $sellerProduct->marketplace_seller_id }}"
            >

            @lang('marketplace::app.shop.sellers.account.products.edit.inventories.pending-ordered-qty', [
                'qty' => $product->ordered_inventories->pluck('qty')->first() ?? 0,
            ])
            
            <i class="icon-information peer rounded-full bg-gray-700 text-lg font-bold text-white transition-all hover:bg-gray-800 ltr:ml-2.5 rtl:mr-2.5"></i>

            <div class="absolute bottom-6 hidden rounded-lg bg-black p-2.5 text-sm italic text-white opacity-80 peer-hover:block">
                @lang('marketplace::app.shop.sellers.account.products.edit.inventories.pending-ordered-qty-info')
            </div>
        </div>
    </div>

    @foreach ($inventorySources as $inventorySource)
        @php
            $qty = 0;
            
            foreach ($product->inventories as $inventory) {
                if ($inventory->inventory_source_id == $inventorySource->id
                    && $inventory->vendor_id == $sellerProduct->marketplace_seller_id
                ) {
                    $qty = $inventory->qty;
                    break;
                }
            }

            $qty = old('inventories['.$inventorySource->id.']') ?: $qty;
        @endphp

        <x-marketplace::shop.form.control-group>
            <x-marketplace::shop.form.control-group.label>
                {{ $inventorySource->name }}
            </x-marketplace::shop.form.control-group.label>

            <x-marketplace::shop.form.control-group.control
                type="text"
                :name="'inventories['.$inventorySource->id.']'"
                :rules="'numeric|min:0'"
                :label="$inventorySource->name"
                :value="$qty"
            />

            <x-marketplace::shop.form.control-group.error :control-name="'inventories['.$inventorySource->id.']'" />
        </x-marketplace::shop.form.control-group>
    @endforeach
</v-inventories>

@pushOnce('scripts')
    <script type="text/x-template" id="v-inventories-template">
        <div v-show="manageStock">
            <slot></slot>
        </div>
    </script>

    <script type="module">
        app.component('v-inventories', {
            template: '#v-inventories-template',

            data() {
                return {
                    manageStock: "{{ (boolean) $product->manage_stock }}",
                }
            },

            mounted: function() {
                let self = this;

                document.getElementById('manage_stock').addEventListener('change', function(e) {
                    self.manageStock = e.target.checked;
                });
            }
        });
    </script>
@endpushOnce