<!-- Over Details Vue Component -->
<v-dashboard-overall-details>
    <!-- Shimmer -->
    <x:marketplace::shop.shimmer.dashboard.over-all-details />
</v-dashboard-overall-details>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-overall-details-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x:marketplace::shop.shimmer.dashboard.over-all-details />
        </template>

        <!-- Overall Details -->
        <template v-else>
            <div class="mt-8 grid gap-2.5 rounded-xl border border-[#E9E9E9] p-5">
                <h4 class="text-xl font-medium">
                    @lang('marketplace::app.shop.sellers.account.dashboard.overall-details')
                </h4>

                {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.before') !!}
                
                <div class="grid grid-cols-2 gap-2.5 md:grid-cols-3">
                    {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.total_sales.before') !!}
                    
                    <!-- Total Sales -->
                    <div class="py-2.5">
                        <div class="flex items-end gap-1">
                            <h3 class="text-2xl font-medium">
                                @{{ report.statistics.total_sales.formatted_total }}
                            </h3>

                            <p
                                class="text-sm font-normal"
                                :class="[report.statistics.total_sales.progress < 0 ?  'text-red-500' : 'text-emerald-500']"
                            >
                                @{{ Math.abs(report.statistics.total_sales.progress.toFixed(2)) }}%
                            </p>
                        </div>

                        <p class="text-sm font-normal text-[#757575]">
                            @lang('marketplace::app.shop.sellers.account.dashboard.total-sales')
                        </p>
                    </div>

                    {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.total_sales.after') !!}
                    
                    <!-- Total Payout -->
                    <div class="py-2.5">
                        <div class="flex items-end gap-1">
                            <h3 class="text-2xl font-medium">
                                @{{ report.statistics.total_payout.formatted_total }}
                            </h3>

                            <p class="text-sm font-normal text-[#060C3B]">
                                @{{ Math.abs(report.statistics.total_payout.percent.toFixed(2)) }}%
                            </p>
                        </div>

                        <p class="text-sm font-normal text-[#757575]">
                            @lang('marketplace::app.shop.sellers.account.dashboard.total-payout')
                        </p>
                    </div>

                    {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.total_payout.after') !!}

                    <!-- Remaining Payout -->
                    <div class="py-2.5">
                        <div class="flex items-end gap-1">
                            <h3 class="text-2xl font-medium">
                                @{{ report.statistics.remaining_payout.formatted_total }}
                            </h3>

                            <p class="text-sm font-normal text-[#060C3B]">
                                @{{ Math.abs(report.statistics.remaining_payout.percent.toFixed(2)) }}%
                            </p>
                        </div>

                        <p class="text-sm font-normal text-[#757575]">
                            @lang('marketplace::app.shop.sellers.account.dashboard.remaining-payout')
                        </p>
                    </div>

                    {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.remaining_payout.after') !!}

                    <!-- Total Orders -->
                    <div class="py-2.5">
                        <div class="flex items-end gap-1">
                            <h3 class="text-2xl font-medium">
                                @{{ report.statistics.total_orders.current }}
                            </h3>

                            <p
                                class="text-sm font-normal"
                                :class="[report.statistics.total_orders.progress < 0 ? 'text-red-500' : 'text-emerald-500']"
                            >
                                @{{ Math.abs(report.statistics.total_orders.progress.toFixed(2)) }}%
                            </p>
                        </div>

                        <p class="text-sm font-normal text-[#757575]">
                            @lang('marketplace::app.shop.sellers.account.dashboard.total-orders')
                        </p>
                    </div>

                    {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.total_orders.after') !!}

                    <!-- Total Customers -->
                    <div class="py-2.5">
                        <div class="flex items-end gap-1">
                            <h3 class="text-2xl font-medium">
                                @{{ report.statistics.total_customers.current }}
                            </h3>

                            <p
                                class="text-sm font-normal"
                                :class="[report.statistics.total_customers.progress < 0 ? 'text-red-500' : 'text-emerald-500']"
                            >
                                @{{ Math.abs(report.statistics.total_customers.progress.toFixed(2)) }}%
                            </p>
                        </div>

                        <p class="text-sm font-normal text-[#757575]">
                            @lang('marketplace::app.shop.sellers.account.dashboard.total-customers')
                        </p>
                    </div>

                    {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.total_customers.after') !!}
                    
                    <!-- Avg Sale -->
                    <div class="py-2.5">
                        <div class="flex items-end gap-1">
                            <h3 class="text-2xl font-medium">
                                @{{ report.statistics.avg_sales.formatted_total }}
                            </h3>

                            <p
                                class="text-sm font-normal"
                                :class="[report.statistics.avg_sales.progress < 0 ? 'text-red-500' : 'text-emerald-500']"
                            >
                                @{{ Math.abs(report.statistics.avg_sales.progress.toFixed(2)) }}%
                            </p>
                        </div>

                        <p class="text-sm font-normal text-[#757575]">
                            @lang('marketplace::app.shop.sellers.account.dashboard.avg-order-sell')
                        </p>
                    </div>

                    {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.avg_sale.after') !!}
                </div>

                {!! view_render_event('marketplace.seller.dashboard.stats.overall_details.after') !!}
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-overall-details', {
            template: '#v-dashboard-overall-details-template',

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

                    filtets.type = 'over-all';

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
