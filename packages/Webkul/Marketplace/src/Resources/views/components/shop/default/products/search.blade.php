<v-product-search {{ $attributes }}></v-product-search>

@pushOnce('scripts')
    <script type="text/x-template" id="v-product-search-template">
        <!-- Search Drawer -->
        <x-marketplace::shop.drawer
            ref="searchProductDrawer"
            @close="searchTerm = ''; searchedProducts = [];"
        >
            <!-- Drawer Header -->
            <x-slot:header>
                <div class="grid gap-[12px]">
                    <div class="flex items-center justify-between">
                        <p class="text-[20px] font-medium">
                            @lang('admin::app.components.products.search.title')
                        </p>

                        <div
                            class="primary-button mr-[45px]"
                            @click="addSelected"
                        >
                            @lang('admin::app.components.products.search.add-btn')
                        </div>
                    </div>

                    <div class="relative w-full">
                        <input
                            type="text"
                            class="block w-full rounded-lg border bg-white py-[5px] leading-6 text-gray-600 transition-all hover:border-gray-400 ltr:pl-[12px] ltr:pr-[40px] rtl:pl-[40px] rtl:pr-[12px]"
                            placeholder="Search by name"
                            v-model.lazy="searchTerm"
                            v-debounce="500"
                        />

                        <span class="icon-search pointer-events-none absolute top-[6px] flex items-center text-[22px] ltr:right-[12px] rtl:left-[12px]"></span>
                    </div>
                </div>
            </x-slot:header>

            <!-- Drawer Content -->
            <x-slot:content class="!p-0">
                <div
                    class="grid"
                    v-if="filteredSearchedProducts.length"
                >
                    <div
                        class="flex justify-between gap-[10px] border-b-[1px] border-slate-300 px-[16px] py-[24px]"
                        v-for="product in filteredSearchedProducts"
                    >
                        <!-- Information -->
                        <div class="flex gap-[10px]">
                            <!-- Checkbox -->
                            <div class="">
                                <input
                                    type="checkbox"
                                    class="peer sr-only"
                                    :id="'searched-product' + product.id"
                                    v-model="product.selected"
                                />

                                <label
                                    class="mp-uncheckbox-icon peer-checked:mp-checked-icon cursor-pointer text-[24px] peer-checked:text-blue-600"
                                    :for="'searched-product' + product.id"
                                >
                                </label>
                            </div>

                            <!-- Image -->
                            <div
                                class="relative h-15 max-h-15 w-full max-w-15 overflow-hidden rounded-[4px]"
                                :class="{'border border-dashed border-gray-300': ! product.images.length}"
                            >
                                <template v-if="! product.images.length">
                                    <img src="{{ bagisto_asset('images/product-placeholders/front.svg', 'marketplace') }}">
                                
                                    <p class="absolute bottom-[5px] w-full text-center text-[6px] font-semibold text-gray-400">Product Image</p>
                                </template>

                                <template v-else>
                                    <img :src="product.images[0].url">
                                </template>
                            </div>

                            <!-- Details -->
                            <div class="grid gap-[6px]">
                                <p class="font-semibold text-[16x] text-gray-800">
                                    @{{ product.name }}
                                </p>

                                <p class="text-gray-600">
                                    @{{ "@lang('admin::app.components.products.search.sku')".replace(':sku', product.sku) }}
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="grid gap-[4px] text-right">
                            <p class="font-semibold text-gray-800">
                                @{{ product.formatted_price }}
                            </p>

                            <p class="text-green-600">
                                @{{ "@lang('admin::app.components.products.search.qty')".replace(':qty', totalQty(product)) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- For Empty Variations -->
                <div
                    class="grid justify-center justify-items-center gap-[14px] px-[10px] py-[40px]"
                    v-else
                >
                    <!-- Placeholder Image -->
                    <img
                        src="{{ bagisto_asset('images/icon-add-product.svg', 'marketplace') }}"
                        class="h-[80px] w-[80px]"
                    />

                    <!-- Add Variants Information -->
                    <div class="flex flex-col items-center">
                        <p class="text-[16px] font-semibold text-gray-400">
                            @lang('admin::app.components.products.search.empty-title')
                        </p>

                        <p class="text-gray-400">
                            @lang('admin::app.components.products.search.empty-info')
                        </p>
                    </div>
                </div>
            </x-slot:content>
        </x-marketplace::shop.drawer>
    </script>

    <script type="module">
        app.component('v-product-search', {
            template: '#v-product-search-template',

            props: {
                addedProductIds: {
                    type: Array,
                    default: []                    
                },

                queryParams: {
                    type: Object,
                    default: () => ({})
                },
            },

            data() {
                return {
                    searchTerm: '',

                    searchedProducts: [],
                }
            },

            computed: {
                filteredSearchedProducts() {
                    return this.searchedProducts.filter(product => ! this.addedProductIds.includes(product.id));
                }
            },

            watch: {
                searchTerm: function(newVal, oldVal) {
                    this.search()
                }
            },

            methods: {
                openDrawer() {
                    this.$refs.searchProductDrawer.open();
                },

                search() {
                    if (this.searchTerm.length <= 1) {
                        this.searchedProducts = [];

                        return;
                    }

                    let self = this;
                    
                    this.$axios.get("{{ route('marketplace.account.products.search_simple') }}", {
                            params: {
                                ...{query: this.searchTerm},
                                ...this.queryParams
                            }
                        })
                        .then(function(response) {
                            self.searchedProducts = response.data.data;
                        })
                        .catch(function (error) {
                        })
                },

                addSelected() {
                    let selectedProducts = this.searchedProducts.filter(product => product.selected);

                    this.$emit('onProductAdded', selectedProducts);

                    this.$refs.searchProductDrawer.close();
                },

                totalQty(product) {
                    let qty = 0;

                    product.inventories.forEach(function (inventory) {
                        qty += inventory.qty;
                    });

                    return qty;
                }
            }
        });
    </script>
@endPushOnce