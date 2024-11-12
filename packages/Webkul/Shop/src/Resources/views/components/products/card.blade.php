<v-product-card {{ $attributes }} :product="product">
</v-product-card>

@pushOnce('scripts')
    <script type="text/x-template" id="v-product-card-template">
    <!-- Grid Card -->
    <div class='grid gap-2.5 content-start w-full relative bg-gray-100 rounded-[10px]' v-if="mode != 'list'">
        <div
            class="relative overflow-hidden group max-md:min-w-[150px] max-md:max-w-[190px] max-w-[300px] max-h-[300px] rounded">
            {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

            <!-- Product Image -->
            <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`"
                :aria-label="product.name + ' '">
                <x-shop::media.images.lazy
                    class="relative after:content-[' '] after:block after:pb-[calc(100%+9px)] bg-[#F5F5F5] group-hover:scale-105 transition-all duration-300"
                    ::src="product.base_image.medium_image_url" ::key="product.id" ::index="product.id" width="100%"
                    height="300" ::alt="product.name" />
            </a>

            {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

            {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

                @if (core()->getConfigData('catalog.products.review.summary') == 'star_counts')
                    <x-shop::products.ratings
                        class="absolute bottom-1.5 items-center !border-white bg-white/80 !px-2 !py-1 text-xs max-sm:!px-1.5 max-sm:!py-0.5 ltr:left-1.5 rtl:right-1.5"
                        ::average="product.ratings.average"
                        ::total="product.ratings.total"
                        ::rating="false"
                        v-if="product.ratings.total"
                    />
                @else
                    <x-shop::products.ratings
                        class="absolute bottom-1.5 items-center !border-white bg-white/80 !px-2 !py-1 text-xs max-sm:!px-1.5 max-sm:!py-0.5 ltr:left-1.5 rtl:right-1.5"
                        ::average="product.ratings.average"
                        ::total="product.reviews.total"
                        ::rating="false"
                        v-if="product.reviews.total"
                    />
                @endif

            {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

            <!-- Product Ratings -->
            <div class="action-items bg-black">
                <!-- Product Sale Badge -->
                <p class="inline-block absolute top-5 ltr:left-5 rtl:right-5 px-2.5 bg-[#E51A1A] rounded-[44px] text-white text-sm"
                    v-if="product.on_sale">
                    @lang('shop::app.components.products.card.sale')
                </p>

                <!-- Product New Badge -->
                <p class="inline-block absolute top-5 ltr:left-5 rtl:right-5 px-2.5 bg-navyBlue rounded-[44px] text-white text-sm"
                    v-else-if="product.is_new">
                    @lang('shop::app.components.products.card.new')
                </p>

                <div
                    class="group-hover:bottom-0 opacity-0 group-hover:opacity-100 transition-all duration-300 max-sm:opacity-100 max-lg:opacity-100">

                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                    <span
                        class="flex justify-center items-center absolute top-5 ltr:right-5 rtl:left-5 w-[30px] h-[30px] bg-white rounded-md cursor-pointer text-2xl max-sm:text-xl"
                        role="button" aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                        tabindex="0" :class="product.is_wishlist ? 'icon-heart-fill' : 'icon-heart'"
                        @click="addToWishlist()">
                    </span>
                    @endif

                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                    {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                    @if (core()->getConfigData('catalog.products.settings.compare_option'))
                    <span
                        class="icon-compare flex justify-center items-center w-[30px] h-[30px] absolute top-16 ltr:right-5 rtl:left-5 bg-white rounded-md cursor-pointer text-2xl max-sm:text-xl"
                        role="button" aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                        tabindex="0" @click="addToCompare(product.id)">
                    </span>
                    @endif

                    {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}

                </div>
            </div>
        </div>

        <!-- Product Information Section -->
        <div class="grid gap-2.5 content-start max-w-[291px] p-2">

            {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

            <p class="text-wrap: wrap;" v-text="product.name"></p>

            {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

            <!-- Pricing -->
            {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

            <div class="flex flex-wrap max-md:h-12 gap-2.5 items-center font-semibold text-sm"
                v-html="product.price_html">
            </div>

            {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

            @if(false)
            <div class="flex max-md:flex-wrap gap-2.5">
                <x-shop::quantity-changer
                    class="flex gap-x-2.5 border rounded-[10px] border-navyBlue py-1.5 px-2.5 max-md:px-2 max-md:py-1 max-w-max"
                    name="quantity" ::value="quantity" @change="setItemQuantity" />
                <x-shop::button
                    class="primary-button max-md:px-2 max-md:py-1.5 max-md:text-xs max-md:rounded-md px-8 py-2.5 whitespace-nowrap max-w-[150px] max-sm:w-full"
                    :title="trans('shop::app.components.products.card.add-to-quote')" ::loading="isAddingToQuote"
                    ::disabled="! product.is_saleable || isAddingToQuote" @click="addToQuote()" />
            </div>
            @endif

            <div class="flex max-md:flex-wrap gap-2.5">
                <div
                    class="flex gap-x-2.5 border rounded-[10px] border-navyBlue py-1.5 px-2.5 max-md:px-2 max-md:py-1 max-w-[140px] max-sm:w-full bg-gray-200">
                    <button type="button" class="px-1 py-0.5 bg-gray-200 rounded" @click="decreaseQuantity">
                        -
                    </button>

                    <input type="number" name="quantity" class="w-14 text-center no-arrows bg-gray-200"
                        v-model="quantity" min="1" @input="setItemQuantity($event.target.value)" />

                    <button type="button" class="px-1 py-0.5 bg-gray-200 rounded" @click="increaseQuantity">
                        +
                    </button>
                </div>

                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                        <x-shop::button
                        class="primary-button max-md:px-2 max-md:py-1.5 max-md:text-xs max-md:rounded-md px-8 py-2.5 whitespace-nowrap max-w-[140px] max-sm:w-full"
                        :title="trans('shop::app.components.products.card.add-to-quote')" ::loading="isAddingToQuote"
                        ::disabled="! product.is_saleable || isAddingToQuote" @click="addToQuote()" />

                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                @endif
            </div>

            <!-- Needs to implement that in future -->
            <div class="hidden flex gap-4 mt-2">
                <span class="block w-[30px] h-[30px] bg-[#B5DCB4] rounded-full cursor-pointer"></span>

                <span class="block w-[30px] h-[30px] bg-[#5C5C5C] rounded-full cursor-pointer"></span>
            </div>
        </div>
    </div>

    <!-- List Card -->
    <div class="flex gap-4 grid-cols-2 max-w-max relative max-sm:flex-wrap rounded overflow-hidden" v-else>
        <div class="group relative max-h-[258px] max-w-[250px] overflow-hidden">

            {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

            <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
                <x-shop::media.images.lazy
                    class="after:content-[' '] relative min-w-[250px] bg-zinc-100 transition-all duration-300 after:block after:pb-[calc(100%+9px)] group-hover:scale-105"
                    ::src="product.base_image.medium_image_url" ::key="product.id" ::index="product.id" width="291"
                    height="300" ::alt="product.name" />
            </a>

            {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

            <div class="action-items bg-black">
                <p class="inline-block absolute top-5 ltr:left-5 rtl:right-5 px-2.5 bg-[#E51A1A] rounded-[44px] text-white text-sm"
                    v-if="product.on_sale">
                    @lang('shop::app.components.products.card.sale')
                </p>

                <p class="inline-block absolute top-5 ltr:left-5 rtl:right-5 px-2.5 bg-navyBlue rounded-[44px] text-white text-sm"
                    v-else-if="product.is_new">
                    @lang('shop::app.components.products.card.new')
                </p>

                <div
                    class="group-hover:bottom-0 opacity-0 transition-all duration-300 max-sm:opacity-100 group-hover:opacity-100">

                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}


                    @if (core()->getConfigData('general.content.shop.wishlist_option'))
                    <span
                        class="flex justify-center items-center absolute top-5 ltr:right-5 rtl:left-5 w-[30px] h-[30px] bg-white rounded-md text-2xl cursor-pointer"
                        role="button" aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                        tabindex="0" :class="product.is_wishlist ? 'icon-heart-fill' : 'icon-heart'"
                        @click="addToWishlist()">
                    </span>
                    @endif

                    {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                    {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                    @if (core()->getConfigData('general.content.shop.compare_option'))
                    <span
                        class="icon-compare flex justify-center items-center absolute top-16 ltr:right-5 rtl:left-5 w-[30px] h-[30px] bg-white rounded-md text-2xl cursor-pointer"
                        role="button" aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                        tabindex="0" @click="addToCompare(product.id)">
                    </span>
                    @endif

                    {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}
                </div>
            </div>
        </div>

        <div class="grid gap-4 content-start">

            {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

            <p class="text-base" v-text="product.name">
            </p>

            {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

            {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

            <div class="flex gap-2.5 text-lg font-semibold" v-html="product.price_html">
            </div>

            {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

            <!-- Needs to implement that in future -->
            <div class="flex hidden gap-4">
                <span class="block h-[30px] w-[30px] rounded-full bg-[#B5DCB4]">
                </span>

                <span class="block h-[30px] w-[30px] rounded-full bg-zinc-500">
                </span>
            </div>

            {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

            {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

            <p v-else class="text-sm text-[#6E6E6E]">
                <x-shop::products.ratings ::value="product && product.avg_ratings ? product.avg_ratings : 0"
                    :is-editable=false />
            </p>

            {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

            {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

            <div class="flex gap-2.5 items-center">
                <x-shop::quantity-changer
                    class="flex gap-x-2.5 border rounded-[10px] border-navyBlue py-2.5 px-3.5 items-center max-w-max"
                    name="quantity" ::value="quantity" @change="setItemQuantity" />
                <x-shop::button class="primary-button px-8 py-2.5 whitespace-nowrap max-w-[150px] max-sm:w-full"
                    :title="trans('shop::app.components.products.card.add-to-quote')" ::loading="isAddingToQuote"
                    ::disabled="! product.is_saleable || isAddingToQuote" @click="addToQuote()" />
            </div>

            {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
        </div>
    </div>
</script>

    <script type="module">
        app.component('v-product-card', {
            template: '#v-product-card-template',

            props: ['mode', 'product'],

            data() {
                return {
                    isCustomer: '{{ auth()->guard('customer')->check() }}',

                    isAddingToCart: false,
                    isAddingToQuote: false,
                    quantity: 1,
                }
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
                addToWishlist() {
                    if (this.isCustomer) {
                        this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                                product_id: this.product.id
                            })
                            .then(response => {
                                this.product.is_wishlist = !this.product.is_wishlist;
                                this.quantity = 1;

                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data.data.message
                                });
                            })
                            .catch(error => {});
                    } else {
                        window.location.href = "{{ route('shop.customer.session.index') }}";
                    }
                },

                addToCompare(productId) {
                    /**
                     * This will handle for customers.
                     */
                    if (this.isCustomer) {
                        this.$axios.post('{{ route('shop.api.compare.store') }}', {
                                'product_id': productId
                            })
                            .then(response => {
                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data.data.message
                                });
                            })
                            .catch(error => {
                                if ([400, 422].includes(error.response.status)) {
                                    this.$emitter.emit('add-flash', {
                                        type: 'warning',
                                        message: error.response.data.data.message
                                    });

                                    return;
                                }

                                this.$emitter.emit('add-flash', {
                                    type: 'error',
                                    message: error.response.data.message
                                });
                            });

                        return;
                    }

                    /**
                     * This will handle for guests.
                     */
                    let items = this.getStorageValue() ?? [];

                    if (items.length) {
                        if (!items.includes(productId)) {
                            items.push(productId);

                            localStorage.setItem('compare_items', JSON.stringify(items));

                            this.$emitter.emit('add-flash', {
                                type: 'success',
                                message: "@lang('shop::app.components.products.card.add-to-compare-success')"
                            });
                        } else {
                            this.$emitter.emit('add-flash', {
                                type: 'warning',
                                message: "@lang('shop::app.components.products.card.already-in-compare')"
                            });
                        }
                    } else {
                        localStorage.setItem('compare_items', JSON.stringify([productId]));

                        this.$emitter.emit('add-flash', {
                            type: 'success',
                            message: "@lang('shop::app.components.products.card.add-to-compare-success')"
                        });

                    }
                },

                getStorageValue(key) {
                    let value = localStorage.getItem('compare_items');

                    if (!value) {
                        return [];
                    }

                    return JSON.parse(value);
                },

                addToCart() {

                    this.isAddingToCart = true;

                    this.$axios.post('{{ route('shop.api.checkout.cart.store') }}', {
                            'quantity': this.quantity,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            if (response.data.data.redirect_uri) {
                                window.location.href = response.data.data.redirect_uri;
                            }

                            if (response.data.message) {
                                this.$emitter.emit('update-mini-cart', response.data.data);

                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data.message
                                });
                            } else {
                                this.$emitter.emit('add-flash', {
                                    type: 'warning',
                                    message: response.data.data.message
                                });
                            }

                            this.isAddingToCart = false;
                        })
                        .catch(error => {
                            this.isAddingToCart = false;

                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: response.data.message
                            });
                        });
                },

                addToQuote() {

                    this.isAddingToQuote = true;

                    this.$axios.post('{{ route('shop.api.checkout.cart.store') }}', {
                            'quantity': this.quantity,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            if (response.data.data.redirect_uri) {
                                window.location.href = response.data.data.redirect_uri;
                            }

                            if (response.data.message) {
                                this.$emitter.emit('update-mini-quote', response.data.data);

                                this.$emitter.emit('add-flash', {
                                    type: 'success',
                                    message: response.data.message
                                });
                            } else {
                                this.$emitter.emit('add-flash', {
                                    type: 'warning',
                                    message: response.data.data.message
                                });
                            }

                            this.isAddingToQuote = false;
                        })
                        .catch(error => {
                            this.isAddingToQuote = false;

                            this.$emitter.emit('add-flash', {
                                type: 'error',
                                message: response.data.message
                            });
                        });
                },
            },
        });
    </script>

    <style>
        input[type="number"].no-arrows::-webkit-outer-spin-button,
        input[type="number"].no-arrows::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"].no-arrows {
            -moz-appearance: textfield;
        }

        /* Adjust the distance between the input and buttons */
        .gap-x-2.5 {
            gap: 10px;
            /* Adjust this value as needed */
        }
    </style>
@endpushOnce
