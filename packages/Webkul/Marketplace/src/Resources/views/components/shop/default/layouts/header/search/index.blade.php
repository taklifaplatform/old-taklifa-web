<v-mega-search>
    <div class="relative flex items-center md:w-[440px] md:max-w-[440px] md:max-lg:w-[400px] md:ltr:ml-2.5 md:rtl:mr-2.5">
        <i class="icon-search absolute top-1.5 flex items-center text-2xl ltr:left-2 rtl:right-2"></i>

        <input 
            type="text" 
            class="block w-full rounded-lg border border-transparent bg-[#F5F5F5] px-11 py-2.5 text-xs font-medium text-gray-900 transition-all hover:border-gray-400 focus:border-gray-400"
            placeholder="@lang('marketplace::app.shop.components.layouts.header.mega-search.title')" 
        >
    </div>
</v-mega-search>

@pushOnce('scripts')
    <script type="text/x-template" id="v-mega-search-template">
        <div class="relative flex items-center md:w-[440px] md:max-w-[440px] md:max-lg:w-[400px] md:ltr:ml-2.5 md:rtl:mr-2.5">
            <i class="icon-search absolute top-1.5 flex items-center text-2xl ltr:left-2 rtl:right-2"></i>

            <input 
                type="text"
                class="block w-full rounded-lg border border-transparent bg-[#F5F5F5] px-11 py-2.5 text-xs font-medium text-gray-900 transition-all hover:border-gray-400 focus:border-gray-400"
                :class="{'border-gray-400': isDropdownOpen}"
                placeholder="@lang('marketplace::app.shop.components.layouts.header.mega-search.title')"
                v-model.lazy="searchTerm"
                @click="searchTerm.length ? isDropdownOpen = true : {}"
                v-debounce="500"
            >

            <div
                class="absolute top-10 z-10 w-full rounded-xl border bg-white shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)]"
                v-if="isDropdownOpen"
            >
                <!-- Search Tabs -->
                <div class="flex border-b text-sm text-gray-600">
                    <div
                        class="cursor-pointer p-4 hover:bg-gray-100"
                        :class="{ 'border-b-2 border-blue-600': activeTab == tab.key }"
                        v-for="tab in tabs"
                        @click="activeTab = tab.key; search();"
                    >
                        @{{ tab.title }}
                    </div>
                </div>

                <!-- Searched Results -->
                <template v-if="activeTab == 'products'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.products />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <a
                                :href="'{{ route('marketplace.account.products.edit', ':id') }}'.replace(':id', product.id)"
                                class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100"
                                v-for="product in searchedResults.products.data"
                            >
                                <!-- Left Information -->
                                <div class="flex gap-2.5">
                                    <!-- Image -->
                                    <div
                                        class="relative h-[60px] max-h-[60px] w-full max-w-[60px] overflow-hidden rounded"
                                        :class="{'border border-dashed border-gray-300 rounded overflow-hidden': ! product.images.length}"
                                    >
                                        <template v-if="! product.images.length">
                                            <img src="{{ bagisto_asset('images/product-placeholders/front.svg', 'marketplace') }}">
                                        
                                            <p class="absolute bottom-1.5 w-full text-center text-[6px] font-semibold text-gray-400">
                                                @lang('marketplace::app.shop.catalog.products.edit.types.grouped.image-placeholder')
                                            </p>
                                        </template>

                                        <template v-else>
                                            <img :src="product.images[0].url">
                                        </template>
                                    </div>

                                    <!-- Details -->
                                    <div class="grid place-content-start gap-1.5">
                                        <p
                                            class="text-base font-semibold text-gray-600"
                                            v-text="product.name"
                                        >
                                        </p>

                                        <p class="text-gray-500">
                                            @{{ "@lang('marketplace::app.shop.components.layouts.header.mega-search.sku')".replace(':sku', product.sku) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Right Information -->
                                <div class="grid place-content-center gap-1 text-right">
                                    <p
                                        class="font-semibold text-gray-600"
                                        v-text="product.formatted_price"
                                    >
                                    </p>
                                </div>
                            </a>
                        </div>

                        <div class="flex border-t p-3">
                            <a
                                :href="'{{ route('shop.marketplace.seller.account.products.index') }}?search=:query'.replace(':query', searchTerm)"
                                class="cursor-pointer text-xs font-semibold text-blue-600 transition-all hover:underline"
                                v-if="searchedResults.products.data.length"
                            >
                                @{{ "@lang('marketplace::app.shop.components.layouts.header.mega-search.explore-all-matching-products')".replace(':query', searchTerm).replace(':count', searchedResults.products.total) }}
                            </a>

                            <a
                                href="{{ route('shop.marketplace.seller.account.products.index') }}"
                                class="cursor-pointer text-xs font-semibold text-blue-600 transition-all hover:underline"
                                v-else
                            >
                                @lang('marketplace::app.shop.components.layouts.header.mega-search.explore-all-products')
                            </a>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'orders'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.orders />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <a
                                :href="'{{ route('shop.marketplace.seller.account.orders.view', ':id') }}'.replace(':id', order.id)"
                                class="grid cursor-pointer place-content-start gap-1.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100"
                                v-for="order in searchedResults.orders.data"
                            >
                                <p class="text-base font-semibold text-gray-600">
                                    #@{{ order.increment_id }}
                                </p>

                                <p class="text-gray-500">
                                    @{{ order.formatted_created_at + ', ' + order.status_label + ', ' + order.customer_full_name }}
                                </p>
                            </a>
                        </div>

                        <div class="flex border-t p-3">
                            <a
                                :href="'{{ route('shop.marketplace.seller.account.orders.index') }}?search=:query'.replace(':query', searchTerm)"
                                class="cursor-pointer text-xs font-semibold text-blue-600 transition-all hover:underline"
                                v-if="searchedResults.orders.data.length"
                            >
                                @{{ "@lang('marketplace::app.shop.components.layouts.header.mega-search.explore-all-matching-orders')".replace(':query', searchTerm).replace(':count', searchedResults.orders.total) }}
                            </a>

                            <a
                                href="{{ route('shop.marketplace.seller.account.orders.index') }}"
                                class="cursor-pointer text-xs font-semibold text-blue-600 transition-all hover:underline"
                                v-else
                            >
                                @lang('marketplace::app.shop.components.layouts.header.mega-search.explore-all-orders')
                            </a>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'customers'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.customers />
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <div
                                class="grid cursor-pointer place-content-start gap-1.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100"
                                v-for="customer in searchedResults.customers.data"
                            >
                                <p class="text-base font-semibold text-gray-600">
                                    @{{ customer.full_name }}
                                </p>

                                <p class="text-gray-500">
                                    @{{ customer.email }}
                                </p>
                            </div>
                        </div>

                        <div class="flex border-t p-3">
                            <a
                                :href="'{{ route('shop.marketplace.seller.account.customers.index') }}?search=:query'.replace(':query', searchTerm)"
                                class="cursor-pointer text-xs font-semibold text-blue-600 transition-all hover:underline"
                                v-if="searchedResults.customers.data.length"
                            >
                                @{{ "@lang('marketplace::app.shop.components.layouts.header.mega-search.explore-all-matching-customers')".replace(':query', searchTerm).replace(':count', searchedResults.customers.total) }}
                            </a>

                            <a
                                href="{{ route('shop.marketplace.seller.account.customers.index') }}"
                                class="cursor-pointer text-xs font-semibold text-blue-600 transition-all hover:underline"
                                v-else
                            >
                                @lang('marketplace::app.shop.components.layouts.header.mega-search.explore-all-customers')
                            </a>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-mega-search', {
            template: '#v-mega-search-template',

            data() {
                return {
                    activeTab: 'products',

                    isDropdownOpen: false,

                    tabs: {
                        products: {
                            key: 'products',
                            title: "@lang('marketplace::app.shop.components.layouts.header.mega-search.products')",
                            is_active: true,
                            endpoint: "{{ route('marketplace.account.mega_search.products') }}"
                        },
                        
                        orders: {
                            key: 'orders',
                            title: "@lang('marketplace::app.shop.components.layouts.header.mega-search.orders')",
                            endpoint: "{{ route('marketplace.account.mega_search.orders') }}"
                        },
                        
                        customers: {
                            key: 'customers',
                            title: "@lang('marketplace::app.shop.components.layouts.header.mega-search.customers')",
                            endpoint: "{{ route('marketplace.account.mega_search.customers') }}"
                        }
                    },

                    isLoading: false,

                    searchTerm: '',

                    searchedResults: {
                        products: [],
                        orders: [],
                        customers: []
                    },
                }
            },

            watch: {
                searchTerm: function(newVal, oldVal) {
                    this.search()
                }
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);
            },

            methods: {
                search() {
                    if (! this.searchTerm.length) {
                        this.searchedResults[this.activeTab] = [];

                        this.isDropdownOpen = false;

                        return;
                    }

                    this.isDropdownOpen = true;

                    let self = this;

                    this.isLoading = true;
                    
                    this.$axios.get(this.tabs[this.activeTab].endpoint, {
                            params: {query: this.searchTerm}
                        })
                        .then(function(response) {
                            self.searchedResults[self.activeTab] = response.data;

                            self.isLoading = false;
                        })
                        .catch(function (error) {
                        })
                },

                handleFocusOut(e) {
                    if (! this.$el.contains(e.target)) {
                        this.isDropdownOpen = false;
                    }
                },
            }
        });
    </script>
@endpushOnce