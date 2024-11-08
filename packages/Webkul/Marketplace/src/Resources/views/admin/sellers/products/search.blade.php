@php
    $id = request()->route()->id;
@endphp

<x-admin::layouts>
    <x-slot:title>
        @lang('marketplace::app.admin.sellers.search.title')
    </x-slot:title>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="py-2.5 text-xl font-bold text-gray-800 dark:text-white">
            @lang('marketplace::app.admin.sellers.search.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            <!-- Cancel Button -->
            <a
                href="{{ route('admin.marketplace.sellers.index') }}"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            >
                @lang('marketplace::app.admin.sellers.search.back-btn')
            </a>
        </div>
    </div>

    <v-product-search>
        <div class="relative flex items-center mt-7">
            <i class="icon-search absolute top-2.5 text-2xl ltr:left-3 rtl:right-3"></i>

            <input 
                type="text" 
                class="block w-full py-3 leading-6 text-gray-600 transition-all bg-white border rounded-lg peer h-11 px-11 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                placeholder="@lang('marketplace::app.admin.sellers.search.title')"
            >
        </div>
    </v-product-search>

    @pushOnce('scripts')
        <script
            type="text/x-template" 
            id="v-product-search-template"
        >
            <div class="relative flex items-center mt-7">
                <i class="icon-search absolute top-2.5 text-2xl ltr:left-3 rtl:right-3"></i>

                <input 
                    type="text" 
                    class="block w-full py-3 leading-6 text-gray-600 transition-all bg-white border rounded-lg peer h-11 px-11 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                    placeholder="@lang('marketplace::app.admin.sellers.search.title')"
                    v-model.lazy="searchTerm"
                    v-debounce="500"
                    v-on:click="searchTerm.length >= 2 ? isDropdownOpen = true : {}"
                >
            
                <div
                    class="absolute top-11 z-10 max-h-96 w-full overflow-y-auto rounded-lg border bg-white shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)] dark:border-gray-800 dark:bg-gray-900"
                    v-if="isDropdownOpen"
                >
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.products/>
                    </template>

                    <template v-else>
                        <template v-if="products.length">
                            <div
                                v-for="product in products"
                                class="grid max-h-[400px] overflow-y-auto border-b border-slate-300 last:border-b-0"
                            >
                                <a
                                    :href="['{{ route('admin.marketplace.sellers.products.assign', $id) }}/' + product.id ]"
                                    class="flex cursor-pointer justify-between gap-2.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                >
                                    <!-- Left Information -->
                                    <div class="flex gap-2.5">
                                        <!-- Image -->
                                        <div
                                            class="relative w-full overflow-hidden rounded h-15 max-h-15 max-w-15"
                                            :class="{'border border-dashed border-gray-300 dark:border-gray-800 rounded dark:invert dark:mix-blend-exclusion overflow-hidden': ! product.base_image}"
                                        >
                                            <template v-if="! product.base_image">
                                                <img src="{{ bagisto_asset('images/product-placeholders/front.svg') }}">
                                            
                                                <p class="absolute bottom-1 w-full text-center text-[6px] font-semibold text-gray-400">
                                                    @lang('marketplace::app.admin.sellers.search.image-placeholder')
                                                </p>
                                            </template>

                                            <template v-else>
                                                <img :src="product.base_image">
                                            </template>
                                        </div>

                                        <!-- Details -->
                                        <div class="grid gap-1.5">
                                            <p class="font-semibold text-[16x] text-gray-600 dark:text-gray-300">
                                                @{{ product.name }}
                                            </p>

                                            <p class="text-gray-500">
                                                @{{ "@lang('marketplace::app.admin.sellers.search.sku')".replace(':sku', product.sku) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right Information -->
                                    <div class="grid gap-1 text-right place-content-center">
                                        <div
                                            class="font-semibold text-gray-600 dark:text-gray-300" v-html="product.formatted_price"
                                        >
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </template>
                        <template v-else>
                            <p class="p-4 dark:text-white">
                                @lang('marketplace::app.admin.sellers.search.no-result')
                            </p>
                        </template>
                    </template>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-product-search', {
                template: '#v-product-search-template',

                data() {
                    return {
                        products: [],
                        searchTerm: '',
                        isLoading: false,
                        isDropdownOpen: false,
                    };
                },

                watch: {
                    searchTerm: function (newVal, oldVal) {
                        this.search();
                    }
                },

                created() {
                    window.addEventListener('click', this.handleFocusOut);
                },

                beforeDestroy() {
                    window.removeEventListener('click', this.handleFocusOut);
                },

                methods: {
                    search () {
                        if (this.searchTerm.length > 2) {
                            const self = this;

                            this.isLoading = this.isDropdownOpen = true;

                            this.$axios.get("{{ route('admin.marketplace.sellers.products.search', $id) }}", {params: {query: this.searchTerm}})
                                .then (function(response) {
                                    self.products = response.data;
                                    
                                    self.isLoading = false;
                                })
                                .catch (function (error) {
                                    self.isLoading = self.isDropdownOpen = false;
                                })
                        } else {
                            this.isLoading = this.isDropdownOpen = false;
                        }
                    },

                    handleFocusOut(e) {
                        if (! this.$el.contains(e.target)) {
                            this.isDropdownOpen = false;
                        }
                    },
                }
            })
        </script>
    @endPushOnce
</x-admin::layouts>
