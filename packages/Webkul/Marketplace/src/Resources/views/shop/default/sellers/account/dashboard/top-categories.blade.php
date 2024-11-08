<!-- Top Categories Vue Component -->
<v-dashboard-top-categories>
    <!-- Shimmer -->
    <x:marketplace::shop.shimmer.dashboard.top-categories />
</v-dashboard-top-categories>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-top-categories-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x:marketplace::shop.shimmer.dashboard.top-categories />
        </template>

        <!-- Top Categories Section -->
        <template v-else>
            <div class="grid content-start gap-4 rounded-xl border border-[#E9E9E9] p-7">
                <h3 class="py-2.5 text-xl font-medium text-navyBlue">
                    @lang('marketplace::app.shop.sellers.account.dashboard.top-categories')
                </h3>

                <template v-if="report.statistics.length">
                    {!! view_render_event('marketplace.seller.dashboard.stats.top_categories.before') !!}
                    
                    <div
                        class="grid gap-2 py-6"
                        v-for="category in report.statistics"
                    >
                        <h6 class="h-6 text-base font-medium leading-6">
                            @{{ category.hierarchy }}
                        </h6>
                        
                        <div class="flex gap-5">                                
                            <div class="flex w-4/5 items-center">
                                <div
                                    class="h-2 bg-[#10B981]"
                                    :style="{ width: Math.floor((category.revenue / categoryMaxSell) * 100) + '%' }"
                                >
                                </div>
                            </div>  
                            <span class="text-sm font-normal opacity-80">
                                @{{ $shop.formatPrice(category.revenue) }}
                            </span>
                        </div>
                    </div>

                    {!! view_render_event('marketplace.seller.dashboard.stats.top_categories.after') !!}
                </template>

                <template v-else>
                    <div class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10">
                        <img
                            src="{{ bagisto_asset('images/icon-add-product.svg', 'marketplace') }}"
                            class="h-20 w-20"
                        >

                        <div class="flex flex-col items-center">
                            <p class="text-base font-semibold text-[#6E6E6E]">
                                @lang('marketplace::app.shop.sellers.account.dashboard.no-category')
                            </p>

                            <p class="text-center text-[#6E6E6E]">
                                @lang('marketplace::app.shop.sellers.account.dashboard.category-info')
                            </p>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-top-categories', {
            template: '#v-dashboard-top-categories-template',

            data() {
                return {
                    report: [],

                    categoryMaxSell: 0,

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

                    filtets.type = 'top-categories';

                    this.$axios.get("{{ route('shop.marketplace.seller.account.dashboard.stats') }}", {
                            params: filtets
                        })
                        .then(response => {
                            this.report = response.data;

                            let categoryRevenuesArray = [];

                            this.report.statistics.forEach(category => {
                                categoryRevenuesArray.push(parseFloat(category.revenue))
                            });
                            
                            this.categoryMaxSell = Math.max(...categoryRevenuesArray);
                             
                            this.isLoading = false;
                        })
                        .catch(error => {});
                }
            }
        });
    </script>
@endPushOnce