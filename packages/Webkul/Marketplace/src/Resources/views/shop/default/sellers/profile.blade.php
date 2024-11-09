@inject ('reviewRepository', 'Webkul\Marketplace\Repositories\ReviewRepository')
@inject ('productRepository', 'Webkul\Marketplace\Repositories\ProductRepository')
@inject ('sellerFlagReasonRepository', 'Webkul\Marketplace\Repositories\SellerFlagReasonRepository')

@php
    $totalProduct = $productRepository->getTotalProducts($seller, $assignProducts = true);

    $reviews = $reviewRepository->getRecentReviews($seller->seller_id);

    $avgRatings = $reviewRepository->getAverageRating($seller);

    $totalReviews = $reviewRepository->getTotalReviews($seller);

    $percentageRatings = $reviewRepository->getPercentageRating($seller);

    $flagReasons = $sellerFlagReasonRepository->findWhere([
        'status' => 1
    ]);
@endphp

<!-- SEO Meta Content -->
@push('meta')
    <meta
        name="title"
        content="{{ $seller->meta_title ?? '' }}"
    />

    <meta
        name="description"
        content="{{ trim($seller->meta_description) != ''
        ? $seller->meta_description
        : Illuminate\Support\Str::limit(strip_tags($seller->description), 120, '') }}"
    />

    <meta
        name="keywords"
        content="{{ $seller->meta_keywords }}"
    />
@endPush

<!-- Page Layout -->
<x-marketplace::shop.layouts.full>
    <!-- Page Title -->
    <x-slot:title>
        {{ $seller->shop_title }}
    </x-slot>
    <div class="container px-[60px] max-lg:px-8 max-sm:px-4">
        <div class="mt-8 grid gap-8">
            <!-- Banner -->
            <div class="h-[250px] md:h-[300px]">
                <x-shop::media.images.lazy
                    :src="$seller->banner_url ?: bagisto_asset('images/default-banner.webp', 'marketplace')"
                    class="h-[250px] w-full object-cover md:h-[300px] md:rounded-lg"
                    alt="marketplace banner"
                    width="1320"
                    height="300"
                >
                </x-shop::media.images.lazy>
            </div>

            <div class="grid gap-5">
                <div class="grid md:flex md:items-center md:justify-between">
                    <div class="grid gap-2.5 md:flex">
                        <div class="h-20 w-20 rounded-xl border border-[#E9E9E9]">
                            <x-shop::media.images.lazy
                                :src="$seller->logo_url ?: bagisto_asset('images/default-logo.webp', 'marketplace')"
                                class="h-20 w-20 rounded-xl object-cover"
                                alt="seller logo"
                                width="80"
                                height="80"
                            >
                            </x-shop::media.images.lazy>
                        </div>

                        <div class="grid md:max-w-[500px]">
                            <h1 class="text-3xl font-medium">
                                {{$seller->shop_title}}
                            </h1>

                            <h6 class="text-base font-medium text-[#757575]">
                                {{ $seller->full_address }}
                            </h6>
                        </div>
                    </div>

                    <div class="grid gap-1.5">
                        <div class="flex items-center gap-2.5 md:justify-end">
                            <x-marketplace::shop.products.star-rating
                                :value="$avgRatings"
                                :is-editable=false
                            />

                            <div class="flex gap-4">
                                <p class="text-sm leading-8 text-[#757575]">
                                    ({{ $totalReviews }} @lang('reviews'))
                                </p>
                            </div>
                        </div>

                        @include('marketplace::shop.sellers.contacts')
                    </div>
                </div>

                <!-- seller Profile component -->
                <v-seller-profile>
                    <x-marketplace::shop.shimmer.profile-tab />

                    <x-marketplace::shop.shimmer.product />
                </v-seller-profile>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-seller-profile-template">
            <div class="grid justify-between gap-y-4 rounded-2xl md:flex md:border md:p-5">
                <div class="flex gap-1 md:gap-5">
                    {!! view_render_event('bagisto.shop.marketplace.seller.profile.tabs.before', ['seller' => $seller]) !!}

                    <button
                        class="rounded-xl px-2 py-2 text-lg font-normal leading-7 md:px-4"
                        :class="{ 'border border-navyBlue': activeTab == 'products' }"
                        @click="activeTab = 'products'"
                    >
                        @lang('marketplace::app.shop.sellers.profile.products-count', ['count' => $totalProduct])
                    </button>

                    <button
                        class="rounded-xl px-2 py-2 text-lg font-normal leading-7 md:px-4"
                        :class="{ 'border border-navyBlue': activeTab == 'reviews' }"
                        @click="activeTab = 'reviews'"
                    >
                        @lang('marketplace::app.shop.sellers.profile.reviews-count', ['count' => $totalReviews])
                    </button>

                    <button
                        class="rounded-xl px-2 py-2 text-lg font-normal leading-7 md:px-4"
                        :class="{ 'border border-navyBlue': activeTab == 'about' }"
                        @click="activeTab = 'about'"
                    >
                        @lang('marketplace::app.shop.sellers.profile.about')
                    </button>

                    {!! view_render_event('bagisto.shop.marketplace.seller.profile.tabs.after', ['seller' => $seller]) !!}
                </div>

                <div class="flex">
                    <!-- Serach Product Form -->
                    <form
                        action="{{ route('marketplace.seller.show', $seller->shop_url) }}"
                        class="flex w-full items-center"
                    >
                        <label
                            for="organic-search"
                            class="sr-only"
                        >
                            @lang('marketplace::app.shop.sellers.profile.reviews.search')
                        </label>

                        <div class="relative w-full">
                            <div
                                class="icon-search pointer-events-none absolute top-2.5 flex items-center text-2xl ltr:left-3 rtl:right-3">
                            </div>

                            <input
                                type="text"
                                class="block w-full rounded-xl border border-navyBlue px-11 py-3.5 text-xs font-medium"
                                name="term"
                                value="{{ request('term') }}"
                                placeholder="@lang('marketplace::app.shop.sellers.profile.search-text')"
                            >
                        </div>
                    </form>
                </div>
            </div>

            <template v-if="activeTab == 'products'">
                <v-seller-products>
                    <x-marketplace::shop.shimmer.product />
                </v-seller-products>
            </template>

            <template v-if="activeTab == 'reviews'">
                <div class="mt-3 grid justify-between gap-3 md:flex">
                    <h2 class="text-2xl font-medium">
                        @lang('marketplace::app.shop.sellers.profile.reviews.customer-reviews')
                    </h2>

                    @if (auth()->guard('customer')->check())
                        <span
                            class="icon-pen flex cursor-pointer items-center gap-2 rounded-3xl border border-navyBlue px-4 py-2 text-2xl"
                            @click="$refs.reviewModal.open()"
                        >
                            <span class="text-lg font-normal leading-8">
                                @lang('marketplace::app.shop.sellers.profile.reviews.write-review')
                            </span>
                        </span>
                    @endif
                </div>

                <div class="mt-8 grid gap-y-8 md:flex md:gap-x-8">
                    <div class="grid content-baseline">
                        <div class="grid gap-2">
                            <div class="mt-2.5 flex items-center gap-4">
                                <div class="flex gap-4">
                                    <p class="text-3xl font-medium text-[#232323]">
                                        {{$avgRatings}}
                                    </p>
                                </div>

                                <x-marketplace::shop.products.star-rating
                                    :value="$avgRatings"
                                    :is-editable=false
                                >
                                </x-marketplace::shop.products.star-rating>

                                <div class="flex gap-4">
                                    <p class="text-xs text-[#858585] underline">
                                        (@lang('marketplace::app.shop.sellers.profile.reviews.customer-review', ['count' => $totalReviews]))
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-y-4 md:max-w-[365px]">
                            @for ($i = 5; $i >= 1; $i--)
                                <div class="flex items-center justify-between gap-x-6 max-sm:flex-wrap">
                                    <div class="whitespace-nowrap text-base font-medium">{{ $i }} Stars</div>
                                    <div class="h-4 w-[275px] max-w-full rounded-sm bg-[#E5E5E5]">
                                        <div class="h-4 rounded-sm bg-[#FEA82B]" style="width: {{ $percentageRatings[$i] }}%"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="grid w-full gap-y-5">
                        @foreach ($reviews as $review)
                            <div class="flex gap-5 rounded-xl border border-[#e5e5e5] p-6 max-xl:mb-5 max-sm:flex-wrap">
                                <div>
                                    <div
                                        class="flex max-h-[100px] min-h-[100px] min-w-[100px] max-w-[100px] items-center justify-center rounded-xl bg-[#F5F5F5] max-sm:hidden"
                                        title="{{$review->customer->name}}"
                                    >
                                        @php
                                            $split_name = explode(' ', $review->customer->name);
                                            $uppercase_names = array_map(function ($name) {
                                                return strtoupper($name[0]);
                                            }, $split_name);
                                            $joined_name = implode('', $uppercase_names);
                                        @endphp
                                        <span class="text-2xl font-semibold text-[#6E6E6E]">
                                            {{$joined_name}}
                                        </span>
                                    </div>
                                </div>

                                <div class="w-full">
                                    <div class="flex justify-between">
                                        <p class="text-xl font-medium max-sm:text-base">
                                            {{$review->title}}
                                        </p>

                                        <div class="flex items-center">
                                            <x-marketplace::shop.products.star-rating
                                                :value="$review->rating"
                                            />
                                        </div>
                                    </div>

                                    <p class="mt-2.5 text-base text-[#757575] max-sm:text-xs">
                                        {{$review->comment}}
                                    </p>

                                    <p class="mt-2.5 text-sm text-[#666666] max-sm:text-xs">
                                        @lang('marketplace::app.shop.sellers.profile.reviews.review-by') <span class="font-medium">
                                            {{$review->customer->name}}
                                        </span>
                                        {{core()->formatDate($review->created_at, 'd M, Y')}}
                                    </p>
                                </div>
                            </div>

                            @if ($loop->iteration == 5)
                                <a
                                    href="{{ route('marketplace.seller.reviews.index', $seller->shop_url) }}"
                                    class="secondary-button mx-auto block w-max rounded-2xl px-11 py-2.5 text-center text-base"
                                >
                                    @lang('marketplace::app.shop.sellers.profile.reviews.view-all')
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <x-shop::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <form
                        @submit="handleSubmit($event, createReview)"
                        ref="reviewForm"
                    >
                        <!-- Seller Review Modal -->
                        <x-marketplace::shop.modal ref="reviewModal">
                            <x-slot:header>
                                <!-- Modal Header -->
                                <p class="text-2xl font-medium leading-9 text-[#151515]">
                                    @lang('marketplace::app.shop.sellers.profile.reviews.write-review')
                                </p>
                            </x-slot:header>

                            <x-slot:content class="!pb-2">
                                <input
                                    type="hidden"
                                    name="seller_url"
                                    value="{{$seller->shop_url}}"
                                />

                                {!! view_render_event('bagisto.shop.marketplace.seller.profile.review.create_form_controls.before', ['seller' => $seller]) !!}

                                <x-shop::form.control-group class="w-full">
                                    <x-shop::form.control-group.label class="required flex">
                                        @lang('shop::app.products.view.reviews.rating')
                                    </x-shop::form.control-group.label>

                                    <x-marketplace::shop.products.star-rating
                                        name="rating"
                                        :value="old('rating') ?? 5"
                                        :disabled="false"
                                        rules="required"
                                        :label="trans('shop::app.products.view.reviews.rating')"
                                    >
                                    </x-marketplace::shop.products.star-rating>

                                    <x-shop::form.control-group.error
                                        control-name="rating"
                                        class="flex"
                                    >
                                    </x-shop::form.control-group.error>
                                </x-shop::form.control-group>

                                <x-shop::form.control-group class="w-full">
                                    <x-shop::form.control-group.label class="required flex">
                                        @lang('marketplace::app.shop.sellers.profile.reviews.title')
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control
                                        type="text"
                                        name="title"
                                        class="! shadow-none"
                                        rules="required"
                                        :label="trans('marketplace::app.shop.sellers.profile.reviews.title')"
                                        :placeholder="trans('marketplace::app.shop.sellers.profile.reviews.title')"
                                    />

                                    <x-shop::form.control-group.error
                                        control-name="title"
                                        class="flex"
                                    />
                                </x-shop::form.control-group>

                                <x-shop::form.control-group class="w-full">
                                    <x-shop::form.control-group.label class="required flex">
                                        @lang('marketplace::app.shop.sellers.profile.reviews.comment')
                                    </x-shop::form.control-group.label>

                                    <x-shop::form.control-group.control
                                        type="textarea"
                                        name="comment"
                                        class="mb-0 !shadow-none"
                                        rows="4"
                                        rules="required"
                                        :label="trans('marketplace::app.shop.sellers.profile.reviews.comment')"
                                        :placeholder="trans('marketplace::app.shop.sellers.profile.reviews.comment')"
                                    />

                                    <x-shop::form.control-group.error
                                        control-name="comment"
                                        class="flex"
                                    />
                                </x-shop::form.control-group>

                                {!! view_render_event('bagisto.shop.marketplace.seller.profile.review.create_form_controls.after', ['seller' => $seller]) !!}
                            </x-slot:content>

                            <x-slot:footer>
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="w-28 rounded-2xl bg-navyBlue px-7 py-3 text-center text-base text-white"
                                    >
                                        @lang('marketplace::app.shop.sellers.profile.submit')
                                    </button>
                                </div>
                            </x-slot:footer>
                        </x-marketplace::shop.modal>
                    </form>
                </x-shop::form>
            </template>

            <template v-if="activeTab == 'about'">
                @include('marketplace::shop.sellers.about')
            </template>

            {!! view_render_event('bagisto.shop.marketplace.seller.profile.tab.content', ['seller' => $seller]) !!}
        </script>

        <script type="module">
            app.component('v-seller-profile', {
                template: '#v-seller-profile-template',

                data() {
                    return {
                        activeTab: 'products'
                    }
                },

                methods: {
                    createReview(params, { resetForm, setErrors }) {
                        let formData = new FormData(this.$refs.reviewForm);

                        this.$axios.post("{{route('marketplace.seller.reviews.store')}}", formData)
                            .then((response) => {
                                this.$refs.reviewForm.reset();

                                this.$refs.reviewModal.close();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch(error => {
                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                    }
                }
            });
        </script>

        <script
            type="text/x-template"
            id="v-seller-products-template"
        >
            <div>
                <div class="flex items-start gap-10 max-lg:gap-5 md:mt-4">
                    <!-- Product Listing Filters -->
                    @include('marketplace::shop.sellers.products.filters')

                    <!-- Product Listing Container -->
                    <div class="flex-1">
                        <!-- Desktop Product Listing Toolbar -->
                        <div class="max-md:hidden">
                            @include('marketplace::shop.sellers.products.toolbar')
                        </div>

                        <!-- Product List Card Container -->
                        <div
                            class="mt-8 grid grid-cols-1 gap-6"
                            v-if="filters.toolbar.mode === 'list'"
                        >
                            <!-- Product Card Shimmer Effect -->
                            <template v-if="isLoading">
                                <x-shop::shimmer.products.cards.list count="9" />
                            </template>

                            <!-- Product Card Listing -->
                            <template v-else>
                                <template v-if="products.length">
                                    <x-marketplace::shop.products.card
                                        ::mode="'list'"
                                        v-for="product in products"
                                    >
                                    </x-marketplace::shop.products.card>
                                </template>

                                <!-- Empty Products Container -->
                                <template v-else>
                                    <div class="m-auto grid h-[476px] w-[100%] place-content-center items-center justify-items-center text-center">
                                        <img src="{{ bagisto_asset('images/thank-you.png') }}"/>

                                        <p class="text-xl">
                                            @lang('marketplace::app.shop.sellers.profile.products.no-result')
                                        </p>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <!-- Product Grid Card Container -->
                        <div v-else>
                            <!-- Product Card Shimmer Effect -->
                            <template v-if="isLoading">
                                <div class="mt-8 grid grid-cols-3 gap-8 max-1060:grid-cols-2 max-sm:mt-5 max-sm:justify-items-center max-sm:gap-4">
                                    <x-shop::shimmer.products.cards.grid count="9" />
                                </div>
                            </template>

                            <!-- Product Card Listing -->
                            <template v-else>
                                <template v-if="products.length">
                                    <div class="mt-8 grid grid-cols-3 gap-8 max-1060:grid-cols-2 max-sm:mt-5 max-sm:justify-items-center max-sm:gap-4">
                                        <x-marketplace::shop.products.card
                                            ::mode="'grid'"
                                            v-for="product in products"
                                        >
                                        </x-marketplace::shop.products.card>
                                    </div>
                                </template>

                                <!-- Empty Products Container -->
                                <template v-else>
                                    <div class="m-auto grid h-[476px] w-[100%] place-content-center items-center justify-items-center text-center">
                                        <img src="{{ bagisto_asset('images/thank-you.png') }}"/>

                                        <p class="text-xl">
                                            @lang('marketplace::app.shop.sellers.profile.products.no-result')
                                        </p>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <!-- Load More Button -->
                        <button
                            class="secondary-button mx-auto mt-15 block w-max rounded-2xl px-11 py-2.5 text-center text-base"
                            @click="loadMoreProducts"
                            v-if="links.next"
                        >
                            @lang('shop::app.categories.view.load-more')
                        </button>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-seller-products', {
                template: '#v-seller-products-template',

                data() {
                    return {
                        isMobile: window.innerWidth <= 767,

                        isLoading: true,

                        isDrawerActive: {
                            toolbar: false,

                            filter: false,
                        },

                        filters: {
                            toolbar: {},

                            filter: {},
                        },

                        products: [],

                        links: {},
                    }
                },

                computed: {
                    queryParams() {
                        let queryParams = Object.assign({}, this.filters.filter, this.filters.toolbar);

                        return this.removeJsonEmptyValues(queryParams);
                    },

                    queryString() {
                        return this.jsonToQueryString(this.queryParams);
                    },
                },

                watch: {
                    queryParams() {
                        this.getProducts();
                    },

                    queryString() {
                        window.history.pushState({}, '', '?' + this.queryString);
                    },
                },

                methods: {
                    setFilters(type, filters) {
                        this.filters[type] = filters;

                        document.body.style.overflow = 'auto';
                    },

                    clearFilters(type, filters) {
                        this.filters[type] = {};

                        document.body.style.overflow = 'auto';
                    },

                    getProducts() {
                        this.isDrawerActive = {
                            toolbar: false,

                            filter: false,
                        };

                        this.$axios.get(("{{ route('marketplace.products.index', $seller->shop_url) }}"), {
                            params: this.queryParams
                        })
                            .then(response => {
                                this.isLoading = false;

                                this.products = response.data.data;

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                    },

                    loadMoreProducts() {
                        if (this.links.next) {
                            this.$axios.get(this.links.next).then(response => {
                                this.products = [...this.products, ...response.data.data];

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                        }
                    },

                    removeJsonEmptyValues(params) {
                        Object.keys(params).forEach(function (key) {
                            if ((! params[key] && params[key] !== undefined)) {
                                delete params[key];
                            }

                            if (Array.isArray(params[key])) {
                                params[key] = params[key].join(',');
                            }
                        });

                        return params;
                    },

                    jsonToQueryString(params) {
                        let parameters = new URLSearchParams();

                        for (const key in params) {
                            parameters.append(key, params[key]);
                        }

                        return parameters.toString();
                    }
                },
            });
        </script>
    @endPushOnce
</x-marketplace::shop.layouts.full>
