<!-- Mini Cart Vue Component -->
<v-mini-quote>
    <span class="text-2xl cursor-pointer" role="button" aria-label="@lang('shop::app.checkout.quote.mini-quote.shopping-quote')">
        @include('shop::icons.notebook-pen')
    </span>
</v-mini-quote>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-mini-quote-template"
    >
        {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.before') !!}

        <x-shop::drawer>
            <!-- Drawer Toggler -->
            <x-slot:toggle>
                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.toggle.before') !!}

                <x-shop::button
                class="primary-button max-md:px-2 max-md:py-1.5 max-md:text-xs max-md:rounded-md px-8 py-2.5 whitespace-nowrap max-w-[150px] max-sm:w-full"
                :title="trans('shop::app.components.products.card.show-quote')"
            />

                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.toggle.after') !!}
            </x-slot>

            <!-- Drawer Header -->
            <x-slot:header>
                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.header.before') !!}

                <div class="flex justify-between items-center">
                    <p class="text-2xl font-medium">
                        @lang('shop::app.checkout.cart.index.view-quote')
                    </p>
                </div>

                {{-- <p class="text-base">
                    @lang('shop::app.checkout.quote.mini-quote.offer-on-orders')
                </p> --}}

                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.header.after') !!}
            </x-slot>

            <!-- Drawer Content -->
            <x-slot:content>
                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.before') !!}

                <!-- Cart Item Listing -->
                <div
                    class="grid gap-12 mt-9"
                    v-if="quote?.items?.length"
                >
                    <div
                        class="flex gap-x-5"
                        v-for="item in quote?.items"
                    >
                        <!-- Cart Item Image -->
                        {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.image.before') !!}

                        <div class="">
                            <a :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`">
                                <img
                                    :src="item.base_image.small_image_url"
                                    class="max-w-[110px] max-h-[110px] rounded-xl"
                                />
                            </a>
                        </div>

                        {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.image.after') !!}

                        <!-- Cart Item Information -->
                        <div class="grid flex-1 gap-y-2.5 place-content-start justify-stretch">
                            <div class="flex flex-wrap justify-between">

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.name.before') !!}

                                <a  class="max-w-2/5" :href="`{{ route('shop.product_or_category.index', '') }}/${item.product_url_key}`">
                                    <p
                                        class="font-medium text-wrap"
                                        v-text="item.name"
                                    >
                                    </p>
                                </a>

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.name.after') !!}

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.price.before') !!}
                                <p
                                    class="text-lg"
                                    v-text="item.formatted_price"
                                >
                                </p>

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.price.after') !!}
                            </div>

                            <!-- Cart Item Options Container -->
                            <div
                                class="grid gap-x-2.5 gap-y-1.5 select-none"
                                v-if="item.options.length"
                            >

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.product_details.before') !!}

                                <!-- Details Toggler -->
                                <div class="">
                                    <p
                                        class="flex gap-x-[15px] items-center text-base cursor-pointer"
                                        @click="item.option_show = ! item.option_show"
                                    >
                                        @lang('shop::app.checkout.quote.mini-quote.see-details')

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

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.product_details.after') !!}
                            </div>

                            <div class="flex items-center justify-between flex-wrap w-full">
                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.setItemQuantity.before') !!}

                                <!-- Cart Item Quantity Changer -->
                                <x-shop::quantity-changer
                                    class="flex gap-x-2.5 border rounded-[54px] border-navyBlue py-1.5 px-2.5 w-32 max-md:px-2 max-md:py-1 max-md:w-32 justify-between"
                                    name="quantity"
                                    ::value="item?.quantity"
                                    @change="updateItem($event, item)"
                                />

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.setItemQuantity.after') !!}

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.remove_button.before') !!}

                                <!-- Cart Item Remove Button -->
                                <button
                                    type="button"
                                    class="text-[#0A49A7] rounded-2xl border px-2.5 mt-3 max-md:mt-0 max-md:ml-2"
                                    @click="removeItem(item.id)"
                                >
                                    @lang('shop::app.checkout.cart.index.remove')
                                </button>

                                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.remove_button.after') !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty Cart Section -->
                <div
                    class="pb-8"
                    v-else
                >
                    <div class="grid gap-y-5 b-0 place-items-center">
                        <img src="{{ bagisto_asset('images/thank-you.png') }}">

                        <p class="text-xl">
                            @lang('shop::app.checkout.cart.index.empty-quote')
                        </p>
                    </div>
                </div>

                {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.content.after') !!}
            </x-slot>

            <!-- Drawer Footer -->
            <x-slot:footer>
                <div v-if="quote?.items?.length">
                    <div class="flex justify-between items-center mt-8 mb-8 px-6 pb-2 border-b border-[#E9E9E9]">
                        {!! view_render_event('bagisto.shop.checkout.mini-quote.subtotal.before') !!}

                        <p class="text-sm font-medium text-[#6E6E6E]">
                            @lang('shop::app.checkout.cart.index.subtotal')
                        </p>

                        <p
                            v-if="! isLoading"
                            class="text-3xl font-semibold"
                            v-text="quote.formatted_grand_total"
                        >
                        </p>

                        {!! view_render_event('bagisto.shop.checkout.mini-quote.subtotal.after') !!}

                        <div
                            v-else
                            class="flex justify-center items-center"
                        >
                            <!-- Spinner -->
                            <svg
                                class="absolute animate-spin  h-8 w-8  text-[5px] font-semibold text-blue"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                aria-hidden="true"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>

                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>

                            <span class="opacity-0 realative text-3xl font-semibold" v-text="quote.formatted_grand_total"></span>
                        </div>
                    </div>

                    {!! view_render_event('bagisto.shop.checkout.mini-quote.action.before') !!}

                    <!-- Cart Action Container -->
                    <div class="grid gap-2.5 mt-8 mb-8">
                        {!! view_render_event('bagisto.shop.checkout.mini-quote.continue_to_checkout.before') !!}

                        <a
                            href="{{ route('shop.checkout.quote.index') }}"
                            class="block w-11/12 mx-auto py-4 px-11 bg-navyBlue rounded-2xl text-white text-base font-medium text-center cursor-pointer max-sm:px-5"
                        >
                        @lang('shop::app.checkout.cart.index.view-quote')
                        </a>

                        {!! view_render_event('bagisto.shop.checkout.mini-quote.continue_to_checkout.after') !!}
                    </div>

                    {!! view_render_event('bagisto.shop.checkout.mini-quote.action.after') !!}
                </div>
            </x-slot>
        </x-shop::drawer>

        {!! view_render_event('bagisto.shop.checkout.mini-quote.drawer.after') !!}
    </script>

    <script type="module">
        app.component("v-mini-quote", {
            template: '#v-mini-quote-template',

            data() {
                return {
                    quote: null,

                    isLoading: false,
                    quantity: 1,
                }
            },

            mounted() {
                this.getCart();

                /**
                 * To Do: Implement this.
                 *
                 * Action.
                 */
                this.$emitter.on('update-mini-quote', (quote) => {
                    this.quote = quote;
                });
            },

            methods: {
                setItemQuantity(quantity) {
                    this.quantity = quantity;
                },

                setItemQuantity(value) {
                    this.quantity = parseInt(value, 10);
                },
                increaseQuantity() {
                    this.quantity++;
                },
                decreaseQuantity() {
                    if (this.quantity > 1) {
                        this.quantity--;
                    }
                },
                getCart() {
                    this.$axios.get('{{ route('shop.api.checkout.quote.index') }}')
                        .then(response => {
                            this.quote = response.data.data;
                        })
                        .catch(error => {});
                },

                updateItem(quantity, item) {
                    this.isLoading = true;

                    let qty = {};

                    qty[item.id] = quantity;

                    this.$axios.put('{{ route('shop.api.checkout.quote.update') }}', {
                            qty
                        })
                        .then(response => {
                            if (response.data.message) {
                                this.quote = response.data.data;
                            } else {
                                this.$emitter.emit('add-flash', {
                                    type: 'warning',
                                    message: response.data.data.message
                                });
                            }

                            this.isLoading = false;
                        }).catch(error => this.isLoading = false);
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
                                .catch(error => {
                                    this.$emitter.emit('add-flash', {
                                        type: 'error',
                                        message: response.data.message
                                    });
                                });
                        }
                    });
                },
            },
        });
    </script>
@endpushOnce
