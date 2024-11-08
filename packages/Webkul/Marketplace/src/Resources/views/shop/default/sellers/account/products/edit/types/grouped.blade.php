<v-group-products :errors="errors"></v-group-products>

@pushOnce('scripts')
    <script type="text/x-template" id="v-group-products-template">
        <div class="box-shadow relative rounded-xl border bg-white p-5">
            <!-- Panel Header -->
            <div class="mb-2.5 grid grid-cols-3 justify-items-end gap-5">
                <div class="col-span-2 grid gap-2">
                    <p class="text-base font-semibold text-gray-800">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.title')
                    </p>

                    <p class="text-xs font-medium text-gray-500">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.info')
                    </p>
                </div>
                
                <!-- Add Button -->
                <div
                    class="secondary-button absolute"
                    @click="$refs.productSearch.openDrawer()"
                >
                    @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.add-btn')
                </div>
            </div>

            <!-- Panel Content -->
            <div
                class="grid"
                v-if="groupProducts.length"
            >
                <!-- Draggable Products -->
                <draggable
                    ghost-class="draggable-ghost"
                    v-bind="{animation: 200}"
                    :list="groupProducts"
                    item-key="id"
                >
                    <template #item="{ element, index }">
                        <div class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 py-4">
                            <!-- Information -->
                            <div class="flex gap-2.5">
                                <!-- Drag Icon -->
                                <i class="mp-drag-icon pointer-events-none text-xl text-gray-600 transition-all"></i>
                                
                                <!-- Image -->
                                <div
                                    class="relative h-15 max-h-15 w-full max-w-15 overflow-hidden rounded"
                                    :class="{'border border-dashed border-gray-300 rounded': ! element.associated_product.images.length}"
                                >
                                    <template v-if="! element.associated_product.images.length">
                                        <img src="{{ bagisto_asset('images/product-placeholders/front.svg', 'marketplace') }}">
                                    
                                        <p class="absolute bottom-1 w-full text-center text-[6px] font-semibold text-gray-400">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.image-placeholder')
                                        </p>
                                    </template>

                                    <template v-else>
                                        <img :src="element.associated_product.images[0].url">
                                    </template>
                                </div>

                                <!-- Details -->
                                <div class="grid content-start gap-1.5">
                                    <p class="font-semibold text-[16x] text-gray-800">
                                        @{{ element.associated_product.name }}
                                    </p>

                                    <p class="text-gray-600">
                                        @{{ "@lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.sku')".replace(':sku', element.associated_product.sku) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="grid gap-1 text-right">
                                <p class="font-semibold text-gray-800">
                                    @{{ $shop.formatPrice(element.associated_product.price) }}    
                                </p>
                                
                                <!-- Hidden Input -->
                                <input
                                    type="hidden"
                                    :name="'links[' + (element.id ? element.id : 'link_' + index) + '][associated_product_id]'"
                                    :value="element.associated_product.id"
                                />
                                
                                <input
                                    type="hidden"
                                    :name="'links[' + (element.id ? element.id : 'link_' + index) + '][sort_order]'"
                                    :value="index"
                                />

                                <x-shop::form.control-group class="!mb-0 grid justify-items-end">
                                    <x-shop::form.control-group.label class="required">
                                        @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.default-qty')
                                    </x-shop::form.control-group.label>

                                    <v-field
                                        type="text"
                                        :name="'links[' + (element.id ? element.id : 'link_' + index) + '][qty]'"
                                        v-model="element.qty"
                                        class="flex min-h-10 w-[86px] rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400"
                                        :class="[errors['links[' + (element.id ? element.id : 'link_' + index) + '][qty]'] ? 'border border-red-600 hover:border-red-600' : '']"
                                        rules="required|numeric|min_value:1"
                                        label="@lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.default-qty')"
                                    ></v-field>

                                    <v-error-message
                                        :name="'links[' + (element.id ? element.id : 'link_' + index) + '][qty]'"
                                        v-slot="{ message }"
                                    >
                                        <p class="mt-1 text-xs italic text-red-600">
                                            @{{ message }}
                                        </p>
                                    </v-error-message>
                                </x-shop::form.control-group>

                                <p
                                    class="cursor-pointer text-red-600 transition-all hover:underline"
                                    @click="remove(element)"
                                >
                                    @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.delete')
                                </p>
                            </div>
                        </div>
                    </template>
                </draggable>
            </div>

            <!-- For Empty Variations -->
            <div
                class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10"
                v-else
            >
                <!-- Placeholder Image -->
                <img
                    src="{{ bagisto_asset('images/icon-add-product.svg', 'marketplace') }}"
                    class="h-20 w-20"
                />

                <!-- Add Variants Information -->
                <div class="flex flex-col items-center">
                    <p class="text-base font-semibold text-gray-400">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.empty-title')
                    </p>

                    <p class="text-gray-400">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.empty-info')
                    </p>
                </div>
                
                <!-- Add Row Button -->
                <div
                    class="secondary-button text-sm"
                    @click="$refs.productSearch.openDrawer()"
                >
                    @lang('marketplace::app.shop.sellers.account.products.edit.types.grouped.add-btn')
                </div>
            </div>

            <!-- Product Search Blade Component -->
            <x-marketplace::shop.products.search
                ref="productSearch"
                ::added-product-ids="addedProductIds"
                ::query-params="{type: 'simple'}"
                @onProductAdded="addSelected($event)"
            >
            </x-marketplace::shop.products.search>
        </div>
    </script>

    <script type="module">
        app.component('v-group-products', {
            template: '#v-group-products-template',

            props: ['errors'],

            data() {
                return {
                    groupProducts: @json($product->grouped_products()->with(['associated_product.inventory_indices', 'associated_product.images'])->orderBy('sort_order', 'asc')->get())
                }
            },

            computed: {
                addedProductIds() {
                    return this.groupProducts.map(product => product.associated_product.id);
                }
            },

            methods: {
                addSelected(selectedProducts) {
                    let self = this;

                    selectedProducts.forEach(function (product) {
                        self.groupProducts.push({
                            associated_product: product,
                            qty: 1,
                        });
                    });
                },

                remove(product) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            let index = this.groupProducts.indexOf(product)

                            this.groupProducts.splice(index, 1);
                        }
                    });
                },
            }
        });
    </script>
@endPushOnce