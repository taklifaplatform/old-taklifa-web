<!-- Total Visitors Vue Component -->
<v-dashboard-total-visitors>
    <!-- Shimmer -->
    <x:marketplace::shop.shimmer.dashboard.total-sales />
</v-dashboard-total-visitors>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-dashboard-total-visitors-template"
    >
        <!-- Shimmer -->
        <template v-if="isLoading">
            <x:marketplace::shop.shimmer.dashboard.total-sales />
        </template>

        <!-- Total Visitors Section -->
        <template v-else>
            <div class="grid gap-4 rounded-xl border border-[#E9E9E9] p-7">
                <div class="flex max-h-11 justify-between gap-2">
                    <h3 class="text-xl font-medium text-navyBlue">
                        @{{ "@lang('marketplace::app.shop.sellers.account.dashboard.visitor-count')".replace(':count', report.statistics.total.current) }}
                    </h3>
                    <div class="secondary-button px-5 py-2.5">
                        <a href="{{route('shop.marketplace.seller.account.customers.index')}}">
                            @lang('marketplace::app.shop.sellers.account.dashboard.view-all-btn')
                        </a>
                    </div>
                </div>

                <!-- Bar Chart -->
                <x-admin::charts.bar
                    ::labels="chartLabels"
                    ::datasets="chartDatasets"
                    ::aspect-ratio="1.41"
                />
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-dashboard-total-visitors', {
            template: '#v-dashboard-total-visitors-template',

            data() {
                return {
                    report: [],

                    isLoading: true,
                }
            },

            computed: {
                chartLabels() {
                    return this.report.statistics.over_time.map(({ label }) => label);
                },

                chartDatasets() {
                    return [{
                        data: this.report.statistics.over_time.map(({ total }) => total),
                        barThickness: 6,
                        backgroundColor: '#f87171',
                    }];
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

                    filtets.type = 'total-visitors';

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
