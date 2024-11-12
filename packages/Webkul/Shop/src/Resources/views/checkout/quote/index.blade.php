<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.checkout.quote.index.quote')" />

    <meta name="keywords" content="@lang('shop::app.checkout.quote.index.quote')" />
@endPush

<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.checkout.quote.index.quote')
    </x-slot>

    {!! view_render_event('bagisto.shop.checkout.quote.header.before') !!}

    <!-- Page Header -->
    <div class="flex flex-wrap">
        <div
            class="w-full flex justify-between px-[60px] border border-t-0 border-b border-l-0 border-r-0 py-4 max-lg:px-8 max-sm:px-4">
            <div class="flex items-center gap-x-14 max-[1180px]:gap-x-9">
                {!! view_render_event('bagisto.shop.checkout.quote.logo.before') !!}

                <a href="{{ route('shop.home.index') }}" class="flex min-h-[30px]" aria-label="@lang('shop::app.checkout.quote.index.bagisto')">
                    <img src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                        alt="{{ config('app.name') }}" class="w-16 h-auto md:w-26 md:h-auto" height="29">
                </a>

                {!! view_render_event('bagisto.shop.checkout.quote.logo.after') !!}
            </div>
        </div>
    </div>

    {!! view_render_event('bagisto.shop.checkout.quote.header.after') !!}

    <div class="flex-auto">
        <div class="container px-[60px] max-lg:px-8">

            {!! view_render_event('bagisto.shop.checkout.quote.breadcrumbs.before') !!}

            <x-shop::breadcrumbs name="cart" />

            {!! view_render_event('bagisto.shop.checkout.quote.breadcrumbs.after') !!}

            <v-quote ref="vCart">
                <!-- Cart Shimmer Effect -->
                <x-shop::shimmer.checkout.quote :count="3" />
            </v-quote>
        </div>
    </div>

    {!! view_render_event('bagisto.shop.checkout.quote.cross_sell_carousel.before') !!}

    <!-- Cross-sell Product Carousal -->
    <x-shop::products.carousel :title="trans('shop::app.checkout.quote.index.cross-sell.title')" :src="route('shop.api.checkout.quote.cross-sell.index')">
    </x-shop::products.carousel>

    {!! view_render_event('bagisto.shop.checkout.quote.cross_sell_carousel.after') !!}

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-quote-template"
        >
            <div>
                <!-- Cart Shimmer Effect -->
                <template v-if="isLoading">
                    <x-shop::shimmer.checkout.quote :count="3" />
                </template>

                <!-- Cart Information -->
                <template v-else>
                    <div
                        class="flex flex-wrap gap-20 mt-8 pb-8 max-1060:flex-col"
                        v-if="quote?.items?.length"
                    >
                        <div class="grid gap-6 flex-1">

                            {!! view_render_event('bagisto.shop.checkout.quote.quote_mass_actions.before') !!}

                            <!-- Cart Mass Action Container -->
                            <div class="flex justify-between items-center pb-2.5 border-b border-[#E9E9E9] max-sm:block">
                                <div class="flex select-none items-center">
                                    <input
                                        type="checkbox"
                                        id="select-all"
                                        class="hidden peer"
                                        v-model="allSelected"
                                        @change="selectAll"
                                    >

                                    <label
                                        class="icon-uncheck text-2xl text-navyBlue peer-checked:icon-check-box peer-checked:text-navyBlue cursor-pointer"
                                        for="select-all"
                                        role="button"
                                        tabindex="0"
                                        aria-label="@lang('shop::app.checkout.quote.index.select-all')"
                                    >
                                    </label>

                                    <span
                                        class="text-xl max-md:text-xl max-sm:text-lg ltr:ml-2.5 rtl:mr-2.5"
                                        role="heading"
                                    >
                                        @{{ "@lang('shop::app.checkout.cart.index.items-selected')".replace(':count', selectedItemsCount) }}
                                    </span>
                                </div>

                                <div
                                    class="max-sm:ltr:ml-9 max-sm:rtl:mr-9 max-sm:mt-2.5"
                                    v-if="selectedItemsCount"
                                >
                                    <span
                                        class="text-base text-[#0A49A7] cursor-pointer"
                                        role="button"
                                        tabindex="0"
                                        @click="removeSelectedItems"
                                    >
                                        @lang('shop::app.checkout.cart.index.remove')
                                    </span>

                                    @if (auth()->guard()->check())
                                        <span class="mx-2.5 border-r-[2px] border-[#E9E9E9]"></span>

                                        <span
                                            class="text-base text-[#0A49A7] cursor-pointer"
                                            role="button"
                                            tabindex="0"
                                            @click="moveToWishlistSelectedItems"
                                        >
                                            @lang('shop::app.checkout.cart.index.move-to-wishlist')
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.quote.quote_mass_actions.after') !!}

                            {!! view_render_event('bagisto.shop.checkout.quote.item.listing.before') !!}

                            <!-- Cart Item Listing Container -->
                            <div
                                class="grid gap-y-6"
                                v-for="item in quote?.items"
                            >
                                <div class="flex gap-x-2.5 justify-between flex-wrap pb-5 border-b border-[#E9E9E9]">
                                    <div class="flex gap-x-5">
                                        <div class="select-none mt-11">
                                            <input
                                                type="checkbox"
                                                :id="'item_' + item.id"
                                                class="hidden peer"
                                                v-model="item.selected"
                                                @change="updateAllSelected"
                                            >

                                            <label
                                                class="icon-uncheck text-2xl text-navyBlue peer-checked:icon-check-box peer-checked:text-navyBlue cursor-pointer"
                                                :for="'item_' + item.id"
                                                role="button"
                                                tabindex="0"
                                                aria-label="@lang('shop::app.checkout.quote.index.select-quote-item')"
                                            ></label>
                                        </div>

                                        {!! view_render_event('bagisto.shop.checkout.quote.item_image.before') !!}

                                        <!-- Cart Item Image -->
                                        <a :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`">
                                            <x-shop::media.images.lazy
                                                class="h-[110px] min-w-[110px] max-w[110px] rounded-xl"
                                                ::src="item.base_image.small_image_url"
                                                ::alt="item.name"
                                                width="110"
                                                height="110"
                                                ::key="item.id"
                                                ::index="item.id"
                                            />
                                        </a>

                                        {!! view_render_event('bagisto.shop.checkout.quote.item_image.after') !!}

                                        <!-- Cart Item Options Container -->
                                        <div class="grid place-content-start gap-y-2.5">
                                            {!! view_render_event('bagisto.shop.checkout.quote.item_name.before') !!}

                                            <a :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`">
                                                <p
                                                    class="text-base font-medium"
                                                    v-text="item.name"
                                                >
                                                </p>
                                            </a>

                                            {!! view_render_event('bagisto.shop.checkout.quote.item_name.after') !!}

                                            {!! view_render_event('bagisto.shop.checkout.quote.item_details.before') !!}

                                            <!-- Cart Item Options Container -->
                                            <div
                                                class="grid gap-x-2.5 gap-y-1.5 select-none"
                                                v-if="item.options.length"
                                            >
                                                <!-- Details Toggler -->
                                                <div class="">
                                                    <p
                                                        class="flex gap-x-1.5 text-base items-center cursor-pointer whitespace-nowrap"
                                                        @click="item.option_show = ! item.option_show"
                                                    >
                                                        @lang('shop::app.checkout.quote.index.see-details')

                                                        <span
                                                            class="text-2xl"
                                                            :class="{'icon-arrow-up': item.option_show, 'icon-arrow-down': ! item.option_show}"
                                                        ></span>
                                                    </p>
                                                </div>

                                                <!-- Option Details -->
                                                <div class="grid gap-2" v-show="item.option_show">
                                                    <div class="" v-for="option in item.options">
                                                        <p class="text-sm font-medium">
                                                            @{{ option.attribute_name + ':' }}
                                                        </p>

                                                        <p class="text-sm">
                                                            @{{ option.option_label }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            {!! view_render_event('bagisto.shop.checkout.quote.item_details.after') !!}

                                            {!! view_render_event('bagisto.shop.checkout.quote.formatted_total.before') !!}

                                            <div class="sm:hidden">
                                                <p
                                                    class="text-lg font-semibold"
                                                    v-text="item.formatted_total"
                                                >
                                                </p>

                                                <span
                                                    class="text-base text-[#0A49A7] rounded-2xl border px-2.5 cursor-pointer"
                                                    role="button"
                                                    tabindex="0"
                                                    @click="removeItem(item.id)"
                                                >
                                                    @lang('shop::app.checkout.cart.index.remove')
                                                </span>
                                            </div>

                                            {!! view_render_event('bagisto.shop.checkout.quote.formatted_total.after') !!}

                                            {!! view_render_event('bagisto.shop.checkout.quote.quantity_changer.before') !!}

                                            <x-shop::quantity-changer
                                              class="flex gap-x-2.5 border rounded-[54px] border-navyBlue py-1.5 px-2.5 max-md:px-2 max-md:py-1 max-w-32 justify-between"

                                                name="quantity"
                                                ::value="item?.quantity"
                                                @change="setItemQuantity(item.id, $event)"
                                            />

                                            {!! view_render_event('bagisto.shop.checkout.quote.quantity_changer.after') !!}
                                        </div>
                                    </div>

                                    <div class="max-sm:hidden text-right">

                                        {!! view_render_event('bagisto.shop.checkout.quote.total.before') !!}

                                        <p
                                            class="text-lg font-semibold"
                                            v-text="item.formatted_total"
                                        >
                                        </p>

                                        {!! view_render_event('bagisto.shop.checkout.quote.total.after') !!}

                                        {!! view_render_event('bagisto.shop.checkout.quote.remove_button.before') !!}

                                        <!-- Cart Item Remove Button -->
                                        <span
                                            class=" text-[#0A49A7] rounded-[54px] border px-2.5 max-w-24"
                                            role="button"
                                            tabindex="0"
                                            @click="removeItem(item.id)"
                                        >
                                            @lang('shop::app.checkout.cart.index.remove')
                                        </span>

                                        {!! view_render_event('bagisto.shop.checkout.quote.remove_button.after') !!}
                                    </div>
                                </div>
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.quote.item.listing.after') !!}

                            {!! view_render_event('bagisto.shop.checkout.quote.controls.before') !!}

                            <!-- Cart Item Actions -->
                            <div class="flex max-md:flex-wrap gap-2.5 justify-center">
                                {!! view_render_event('bagisto.shop.checkout.quote.continue_shopping.before') !!}

                                <a
                                    class="secondary-button max-md:rounded-1xl py-2.5 whitespace-nowrap max-w-[170px] max-h-[55px] rounded-2xl"
                                    href="{{ route('shop.home.index') }}"
                                >
                                    @lang('shop::app.checkout.cart.index.continue-shopping')
                                </a>

                                {!! view_render_event('bagisto.shop.checkout.quote.continue_shopping.after') !!}

                                {!! view_render_event('bagisto.shop.checkout.quote.update_cart.before') !!}

                                <x-shop::button
                                    class="secondary-button max-md:rounded-1xl py-2.5 whitespace-nowrap max-w-[170px] max-h-[55px] rounded-2xl"
                                    :title="trans('shop::app.checkout.cart.index.update-quote')"
                                    ::loading="isStoring"
                                    ::disabled="isStoring"
                                    @click="update()"
                                />

                                {!! view_render_event('bagisto.shop.checkout.quote.update_cart.after') !!}
                            </div>

                            {!! view_render_event('bagisto.shop.checkout.quote.controls.after') !!}
                        </div>

                        {!! view_render_event('bagisto.shop.checkout.quote.summary.before') !!}

                        <!-- Cart Summary -->
                        @include('shop::checkout.quote.summary')

                        {!! view_render_event('bagisto.shop.checkout.quote.summary.after') !!}
                    </div>

                    <!-- Empty Cart Section -->
                    <div
                        class="grid items-center justify-items-center w-full m-auto h-[476px] place-content-center text-center"
                        v-else
                    >
                        <img
                            src="{{ bagisto_asset('images/thank-you.png') }}"
                            alt="@lang('shop::app.checkout.quote.index.empty-product')"
                        />

                        <p
                            class="text-xl"
                            role="heading"
                        >
                            @lang('shop::app.checkout.quote.index.empty-product')
                        </p>
                    </div>
                </template>
            </div>
        </script>

        <script type="module">
            app.component("v-quote", {
                template: '#v-quote-template',

                data() {
                    return {
                        quote: [],

                        allSelected: false,

                        applied: {
                            quantity: {},
                        },

                        isLoading: true,

                        isStoring: false,
                    }
                },

                mounted() {
                    this.getCart();
                },

                computed: {
                    selectedItemsCount() {
                        return this.quote.items.filter(item => item.selected).length;
                    },
                },

                methods: {
                    getCart() {
                        this.$axios.get('{{ route('shop.api.checkout.quote.index') }}')
                            .then(response => {
                                this.quote = response.data.data;

                                this.isLoading = false;

                                if (response.data.message) {
                                    this.$emitter.emit('add-flash', {
                                        type: 'info',
                                        message: response.data.message
                                    });
                                }
                            })
                            .catch(error => {});
                    },

                    selectAll() {
                        for (let item of this.quote.items) {
                            item.selected = this.allSelected;
                        }
                    },

                    updateAllSelected() {
                        this.allSelected = this.quote.items.every(item => item.selected);
                    },

                    update() {
                        this.isStoring = true;

                        this.$axios.put('{{ route('shop.api.checkout.quote.update') }}', {
                                qty: this.applied.quantity
                            })
                            .then(response => {
                                this.quote = response.data.data;

                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data.message
                                });

                                this.isStoring = false;

                            })
                            .catch(error => {
                                this.isStoring = false;
                            });
                    },

                    setItemQuantity(itemId, quantity) {
                        this.applied.quantity[itemId] = quantity;
                    },

                    removeItem(itemId) {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                this.$axios.post('{{ route('shop.api.checkout.quote.destroy') }}', {
                                        '_method': 'DELETE',
                                        'cart_item_id': itemId,
                                    })
                                    .then(response => {
                                        this.quote = response.data.data;

                                        this.$emitter.emit('add-flash', {
                                            type: 'success',
                                            message: response.data.message
                                        });

                                    })
                                    .catch(error => {});
                            }
                        });
                    },

                    removeSelectedItems() {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                const selectedItemsIds = this.quote.items.flatMap(item => item.selected ?
                                    item.id : []);

                                this.$axios.post(
                                    '{{ route('shop.api.checkout.quote.destroy_selected') }}', {
                                        '_method': 'DELETE',
                                        'ids': selectedItemsIds,
                                    })
                                    .then(response => {
                                        this.quote = response.data.data;

                                        this.$emitter.emit('update-mini-quote', response.data.data);

                                        this.$emitter.emit('add-flash', {
                                            type: 'success',
                                            message: response.data.message
                                        });

                                    })
                                    .catch(error => {});
                            }
                        });
                    },

                    moveToWishlistSelectedItems() {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                const selectedItemsIds = this.quote.items.flatMap(item => item.selected ?
                                    item.id : []);

                                const selectedItemsQty = this.quote.items.filter(item => item.selected).map(
                                    item => this.applied.quantity[item.id] ?? item.quantity);

                                this.$axios.post(
                                    '{{ route('shop.api.checkout.quote.move_to_wishlist') }}', {
                                        'ids': selectedItemsIds,
                                        'qty': selectedItemsQty
                                    })
                                    .then(response => {
                                        this.quote = response.data.data;

                                        this.$emitter.emit('update-mini-quote', response.data.data);

                                        this.$emitter.emit('add-flash', {
                                            type: 'success',
                                            message: response.data.message
                                        });

                                    })
                                    .catch(error => {});
                            }
                        });
                    },
                }
            });
        </script>
    @endpushOnce
</x-shop::layouts>
