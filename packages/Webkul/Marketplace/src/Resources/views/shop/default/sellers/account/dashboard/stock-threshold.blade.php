<!-- Top Selling Products Vue Component -->
<v-dashboard-stock-threshold-products>
    <!-- Shimmer -->
    <x:marketplace::shop.shimmer.dashboard.stock-threshold />
</v-dashboard-stock-threshold-products>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-stock-threshold-products-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x:marketplace::shop.shimmer.dashboard.stock-threshold />
        </template>

        <!-- Stock Threshold Products Section -->
        <template v-else>
            <div class="grid content-start gap-4 rounded-xl border border-[#E9E9E9] p-7">
                <div class="flex max-h-11 items-center justify-between">
                    <h3 class="py-2.5 text-xl font-medium text-navyBlue">
                        @lang('marketplace::app.shop.sellers.account.dashboard.stock-threshold')
                    </h3>

                    <div
                        class="secondary-button px-5 py-2.5"
                        v-if="report.statistics.length >= 5"
                    >
                        <a href="{{route('shop.marketplace.seller.account.products.index')}}">
                            @lang('marketplace::app.shop.sellers.account.dashboard.view-all-btn')
                        </a>
                    </div>
                </div>

                <template v-if="report.statistics.length ">
                    {!! view_render_event('marketplace.seller.dashboard.stats.stock_threshold.before') !!}
                    
                    <div
                        class="flex items-center justify-between border-b py-4 last:border-b-0"
                        v-for="item in report.statistics"
                    >
                        <div class="grid gap-1">
                            <span class="text-sm font-medium">
                                @{{ item.product.name}}
                            </span>
                            <span class="text-sm font-normal opacity-80">
                                @{{ item.product.sku }}
                            </span>
                            <span class="text-sm font-normal opacity-80">
                                @{{ item.product.product_number }}
                            </span>
                        </div>
            
                        <div class="flex gap-5">
                            <template v-if="! item.product.images.length">
                                <img
                                    class="h-15 min-w-15 max-w-15 rounded-xl object-cover"
                                    src="{{ bagisto_asset('images/small-product-placeholder.webp') }}"
                                    alt="Product Image"
                                    width="60"
                                    height="60"
                                >
                            </template>

                            <template v-else>
                                <img
                                    class="h-15 min-w-15 max-w-15 rounded-xl object-cover"
                                    :src="item.product.images[0].url"
                                    alt="Product Image"
                                    width="60"
                                    height="60"
                                >
                            </template>

                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-medium">
                                    @{{ $shop.formatPrice(item.price)}}
                                </span>
                                <span class="text-sm font-normal text-[#EB5757] opacity-80">
                                    @{{ "@lang('marketplace::app.shop.sellers.account.dashboard.qty-stock')".replace(':qty', item.total_qty) }}
                                </span>
                            </div>
            
                            <div class="flex items-center">
                                <a :href="`{{route('marketplace.account.products.edit', '')}}/${item.product.id}`">
                                    <span
                                        class="icon-arrow-right cursor-pointer rounded-md p-1 text-2xl transition-all hover:bg-gray-100 max-sm:place-self-center"
                                        title="View"
                                    >
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {!! view_render_event('marketplace.seller.dashboard.stats.stock_threshold.after') !!}
                </template>

                <template v-else>
                    <div class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10">
                        <img
                            src="{{ bagisto_asset('images/icon-add-product.svg', 'marketplace') }}"
                            class="h-20 w-20"
                        >
            
                        <div class="flex flex-col items-center">
                            <p class="text-base font-semibold text-[#6E6E6E]">
                                @lang('marketplace::app.shop.sellers.account.dashboard.empty-threshold')
                            </p>
            
                            <p class="text-center text-[#6E6E6E]">
                                @lang('marketplace::app.shop.sellers.account.dashboard.empty-threshold-desc')
                            </p>
                        </div>
                    </div>            
                </template>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-stock-threshold-products', {
            template: '#v-dashboard-stock-threshold-products-template',

            data() {
                return {
                    report: [],

                    isLoading: true,
                }
            },

            mounted() {
                this.getStats({});

                this.$emitter.on('reporting-filter-updated', this.getStats);
            },

            methods: {
                getStats(filtets) {
                    this.isLoading = true;

                    var filtets = Object.assign({}, filtets);

                    filtets.type = 'stock-threshold';

                    this.$axios.get("{{ route('shop.marketplace.seller.account.dashboard.stats') }}", {
                            params: filtets
                        })
                        .then(response => {
                            this.report = response.data;
                            
                            this.isLoading = false;
                        })
                        .catch(error => {});
                }
            }
        });
    </script>
@endPushOnce