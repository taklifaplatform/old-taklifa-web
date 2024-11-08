<x-marketplace::shop.layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.dashboard.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_dashboard" />
    @endSection
 
    <div class="grid gap-2.5">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <div>
                <p class="text-2xl font-medium">
                    @lang('marketplace::app.shop.sellers.account.dashboard.hi-seller', ['seller_name' => $seller->name])
                </p>
    
                <div class="py-4">
                    <p class="text-xs font-medium opacity-80">
                        @lang('marketplace::app.shop.sellers.account.dashboard.hi-comment')
                    </p>
                </div>
            </div>
            
            {!! view_render_event('marketplace.seller.dashboard.filter.before') !!}
            
            <!-- Filter Component -->
            <v-dashboard-filters>
                <x:marketplace::shop.shimmer.dashboard.filter />
            </v-dashboard-filters>
            
            {!! view_render_event('marketplace.seller.dashboard.filter.after') !!}
        </div>
    </div>

    {!! view_render_event('marketplace.seller.dashboard.stats.before') !!}

    {!! view_render_event('marketplace.seller.dashboard.stats.over_all_details.before') !!}
    
    <!-- Over All Details -->
    @include('marketplace::shop.default.sellers.account.dashboard.over-all-details')

    {!! view_render_event('marketplace.seller.dashboard.stats.over_all_details.after') !!}

    <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- Total Sale -->
        @include('marketplace::shop.default.sellers.account.dashboard.total-sales')

        <!-- Total Visitors -->
        @include('marketplace::shop.default.sellers.account.dashboard.total-visitors')
    </div>

    {!! view_render_event('marketplace.seller.dashboard.stats.orders.before') !!}
    
    <!-- Orders Listing -->
    @include('marketplace::shop.sellers.account.dashboard.orders')

    {!! view_render_event('marketplace.seller.dashboard.stats.orders.after') !!}

    <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- Stock Threshold Products -->
        @include('marketplace::shop.default.sellers.account.dashboard.stock-threshold')

        <!-- Top Products -->
        @include('marketplace::shop.default.sellers.account.dashboard.top-products')
    </div>

    {!! view_render_event('marketplace.seller.dashboard.stats.stock_threshold_and_top_products.after') !!}

    <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
        <!-- Top Customers -->
        @include('marketplace::shop.default.sellers.account.dashboard.top-customers')

        <!-- Top Categories -->
        @include('marketplace::shop.default.sellers.account.dashboard.top-categories')
    </div>

    {!! view_render_event('marketplace.seller.dashboard.stats.after') !!}

    @pushOnce('scripts')
        <script type="module" src="{{ bagisto_asset('js/chart.js', 'admin') }}"></script>

        <script type="text/x-template" id="v-dashboard-filters-template">
            <div class="grid items-center gap-2.5 md:flex">
                <!-- Date Range -->
                <x-marketplace::shop.form.control-group class="!mb-0 w-full">
                    <x-marketplace::shop.form.control-group.control
                        type="select"
                        name="range"
                        class="!mb-0 md:w-36"
                        v-model="range"
                        @change="applyRangeFilter()"
                    >
                        <option value="">
                            @lang('marketplace::app.shop.sellers.account.dashboard.date-range')
                        </option>
                        
                        @foreach (['today', 'week', 'month', 'year'] as $type)
                            <option value="{{ $type }}">
                                @lang('marketplace::app.shop.sellers.account.dashboard.'.$type)
                            </option>
                        @endforeach
                    </x-marketplace::shop.form.control-group.control>
                </x-marketplace::shop.form.control-group>
    
                <date-filter>
                    <div class="flex gap-x-2.5">
                        <!-- Start Date -->
                        <x-shop::flat-picker.date class="w-36">
                            <input
                                name="startDate"
                                class="h-11 w-36 rounded-lg border-2 border-[#E9E9E9] bg-white px-3 py-2.5 text-sm font-normal text-navyBlue transition-all hover:border-gray-400 focus:border-gray-400"
                                v-model="filters.start"
                            >
                        </x-shop::flat-picker.date>
            
                        <!-- End Date -->
                        <x-shop::flat-picker.date class="w-36">
                            <input
                                name="endDate"
                                class="h-11 w-36 rounded-lg border-2 border-[#E9E9E9] bg-white px-3 py-2.5 text-sm font-normal text-navyBlue transition-all hover:border-gray-400 focus:border-gray-400"
                                v-model="filters.end"
                            >
                        </x-shop::flat-picker.date>
                    </div>
                </date-filter>
            </div>
        </script>

        <script type="module">
            app.component('v-dashboard-filters', {
                template: '#v-dashboard-filters-template',

                data() {
                    return {
                        filters: {
                            start: "{{ $startDate->format('Y-m-d') }}",
                            
                            end: "{{ $endDate->format('Y-m-d') }}",
                        }
                    }
                },

                watch: {
                    filters: {
                        handler() {
                            this.$emitter.emit('reporting-filter-updated', this.filters);
                        },

                        deep: true
                    }
                },

                methods: {
                    applyRangeFilter() {
                        if (this.range == 'week') {
                            this.filters.start = "{{ now()->startOfWeek()->format('Y-m-d') }}";
                        } else if (this.range == 'month') {
                            this.filters.start = "{{ now()->startOfMonth()->format('Y-m-d') }}";
                        } else if (this.range == 'year') {
                            this.filters.start = "{{ now()->startOfYear()->format('Y-m-d') }}";
                        } else {
                            this.filters.start = "{{ now()->format('Y-m-d') }}";
                        }
                    }
                }
            });
        </script>
    @endPushOnce
</x-marketplace::shop.layouts>
