<v-product-card {{ $attributes }} :product="product">
</v-product-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-card-template"
    >
        <!-- Grid Card -->
        <div
            class='grid gap-2.5 content-start w-full relative bg-gray-100 rounded-[10px]'
            v-if="mode != 'list'"
        >
            <div class="relative overflow-hidden group max-md:min-w-[150px] max-md:max-w-[190px] max-w-[300px] max-h-[300px] rounded">

                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <a
                    :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`"
                    :aria-label="product.name + ' '"
                >
                    <x-shop::media.images.lazy
                        class="relative after:content-[' '] after:block after:pb-[calc(100%+9px)] bg-[#F5F5F5] group-hover:scale-105 transition-all duration-300"
                        ::src="product.base_image.medium_image_url"
                        ::key="product.id"
                        ::index="product.id"
                        width="100%"
                        height="300"
                        ::alt="product.name"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <div class="action-items bg-black">
                    <p
                        class="inline-block absolute top-5 ltr:left-5 rtl:right-5 px-2.5 bg-[#E51A1A] rounded-[44px] text-white text-sm"
                        v-if="product.on_sale"
                    >
                        @lang('shop::app.components.products.card.sale')
                    </p>

                    <p
                        class="inline-block absolute top-5 ltr:left-5 rtl:right-5 px-2.5 bg-navyBlue rounded-[44px] text-white text-sm"
                        v-else-if="product.is_new"
                    >
                        @lang('shop::app.components.products.card.new')
                    </p>

                    <div class="group-hover:bottom-0 opacity-0 group-hover:opacity-100 transition-all duration-300 max-sm:opacity-100 max-lg:opacity-100">


                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                        @if (core()->getConfigData('general.content.shop.wishlist_option'))
                            <span
                                class="flex justify-center items-center absolute top-5 ltr:right-5 rtl:left-5 w-[30px] h-[30px] bg-white rounded-md cursor-pointer text-2xl max-sm:text-xl"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                                tabindex="0"
                                :class="product.is_wishlist ? 'icon-heart-fill' : 'icon-heart'"
                                @click="addToWishlist()"
                            >
                            </span>

                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                        {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                        @if (core()->getConfigData('general.content.shop.compare_option'))
                            <span
                                class="icon-compare flex justify-center items-center w-[30px] h-[30px] absolute top-16 ltr:right-5 rtl:left-5 bg-white rounded-md cursor-pointer text-2xl max-sm:text-xl"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                                tabindex="0"
                                @click="addToCompare(product.id)"
                            >
                            </span>

                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}

                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                        {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.after') !!}
                    </div>
                </div>
            </div>

            <div id="app" class="p-4">
                <button v-if="product.dimensions_calculation_enabled" @click="showPopup = true" class="bg-blue-600 text-white py-2 px-4 rounded-md mt-2">
                    @lang('shop::app.components.products.card.calculate')
                </button>

                <div v-if="showPopup" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-md shadow-lg w-full max-w-[500px] md:max-w-[600px] relative border border-gray-200">
                        <div class="flex justify-center">
                            <x-shop::media.images.lazy
                                class="relative w-2/3 h-auto object-contain rounded-md mb-4"
                                ::src="product.base_image.medium_image_url"
                                ::key="product.id"
                                ::index="product.id"
                                ::alt="product.name"
                            />
                        </div>

                        <div class="pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-gray-200 pt-4">
                                <div class="text-center">
                                    <label class="block text-sm font-bold text-gray-700">
                                        @lang('shop::app.components.products.card.dimension_height')
                                    </label>
                                    <input v-model.number="defaultDimensionHeight" type="number"
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-center appearance-none px-4 py-2 no-arrows focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div class="text-center">
                                    <label class="block text-sm font-bold text-gray-700">
                                        @lang('shop::app.components.products.card.dimension_width')
                                    </label>
                                    <input v-model.number="defaultDimensionWidth" type="number"
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-center appearance-none px-4 py-2 no-arrows focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div class="text-center">
                                    <label class="block text-sm font-bold text-gray-700">
                                        @lang('shop::app.components.products.card.dimension_length')
                                    </label>
                                    <input v-model.number="defaultDimensionLength" type="number"
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-center appearance-none px-4 py-2 no-arrows focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="flex justify-center mt-4">
                                <button @click="calculateAndClose(product.id)" class="bg-blue-600 text-white py-2 px-8 rounded-md shadow-md">
                                    @lang('shop::app.components.products.card.calculate')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="grid gap-2.5 content-start max-w-[291px] p-2">

                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

                <p class="text-wrap: wrap;" v-text="product.name"></p>

                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <div
                    class="flex flex-wrap max-md:h-12 gap-2.5 items-center font-semibold text-sm"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                @if(false)
                <div class="flex max-md:flex-wrap gap-2.5">
                    <x-shop::quantity-changer
                        class="flex gap-x-2.5 border rounded-[10px] border-navyBlue py-1.5 px-2.5 max-md:px-2 max-md:py-1 max-w-max"
                        name="quantity"
                        ::value="quantity"
                        @change="setItemQuantity"
                    />
                    <x-shop::button
                        class="primary-button max-md:px-2 max-md:py-1.5 max-md:text-xs max-md:rounded-md px-8 py-2.5 whitespace-nowrap max-w-[150px] max-sm:w-full"
                        :title="trans('shop::app.components.products.card.add-to-quote')"
                        ::loading="isAddingToQuote"
                        ::disabled="! product.is_saleable || isAddingToQuote"
                        @click="addToQuote()"
                    />
                </div>
                @endif

                <div class="flex max-md:flex-wrap gap-2.5">
                    <div class="flex gap-x-2.5 border rounded-[10px] border-navyBlue py-1.5 px-2.5 max-md:px-2 max-md:py-1 max-w-[140px] max-sm:w-full bg-gray-200">
                        <button
                            type="button"
                            class="px-1 py-0.5 bg-gray-200 rounded"
                            @click="decreaseQuantity"
                        >
                            -
                        </button>

                        <input
                            type="number"
                            name="quantity"
                            class="w-14 text-center no-arrows bg-gray-200"
                            v-model="quantity"
                            min="1"
                            @input="setItemQuantity($event.target.value)"
                        />

                        <button
                            type="button"
                            class="px-1 py-0.5 bg-gray-200 rounded"
                            @click="increaseQuantity"
                        >
                            +
                        </button>
                    </div>


                    <x-shop::button
                        class="primary-button max-md:px-2 max-md:py-1.5 max-md:text-xs max-md:rounded-md px-8 py-2.5 whitespace-nowrap max-w-[140px] max-sm:w-full"
                        :title="trans('shop::app.components.products.card.add-to-quote')"
                        ::loading="isAddingToQuote"
                        ::disabled="! product.is_saleable || isAddingToQuote"
                        @click="addToQuote()"
                    />
                </div>

                <!-- Needs to implement that in future -->
                <div class="hidden flex gap-4 mt-2">
                    <span class="block w-[30px] h-[30px] bg-[#B5DCB4] rounded-full cursor-pointer"></span>

                    <span class="block w-[30px] h-[30px] bg-[#5C5C5C] rounded-full cursor-pointer"></span>
                </div>
            </div>
        </div>

        <!-- List Card -->
        <div
            class="flex gap-4 grid-cols-2 max-w-max relative max-sm:flex-wrap rounded overflow-hidden"
            v-else
        >
            <div class="relative max-w-[250px] max-h-[258px] overflow-hidden group">

                {!! view_render_event('bagisto.shop.components.products.card.image.before') !!}

                <a :href="`{{ route('shop.product_or_category.index', '') }}/${product.url_key}`">
                    <x-shop::media.images.lazy
                        class="min-w-[250px] relative after:content-[' '] after:block after:pb-[calc(100%+9px)] bg-[#F5F5F5] group-hover:scale-105 transition-all duration-300"
                        ::src="product.base_image.medium_image_url"
                        ::key="product.id"
                        ::index="product.id"
                        width="291"
                        height="300"
                        ::alt="product.name"
                    />
                </a>

                {!! view_render_event('bagisto.shop.components.products.card.image.after') !!}

                <div class="action-items bg-black">
                    <p
                        class="inline-block absolute top-5 ltr:left-5 rtl:right-5 px-2.5 bg-[#E51A1A] rounded-[44px] text-white text-sm"
                        v-if="product.on_sale"
                    >
                        @lang('shop::app.components.products.card.sale')
                    </p>

                    <p
                        class="inline-block absolute top-5 ltr:left-5 rtl:right-5 px-2.5 bg-navyBlue rounded-[44px] text-white text-sm"
                        v-else-if="product.is_new"
                    >
                        @lang('shop::app.components.products.card.new')
                    </p>

                    <div class="group-hover:bottom-0 opacity-0 transition-all duration-300 max-sm:opacity-100 group-hover:opacity-100">

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.before') !!}

                        @if (core()->getConfigData('general.content.shop.wishlist_option'))
                            <span
                                class="flex justify-center items-center absolute top-5 ltr:right-5 rtl:left-5 w-[30px] h-[30px] bg-white rounded-md text-2xl cursor-pointer"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-wishlist')"
                                tabindex="0"
                                :class="product.is_wishlist ? 'icon-heart-fill' : 'icon-heart'"
                                @click="addToWishlist()"
                            >
                            </span>
                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.wishlist_option.after') !!}

                        {!! view_render_event('bagisto.shop.components.products.card.compare_option.before') !!}

                        @if (core()->getConfigData('general.content.shop.compare_option'))
                            <span
                                class="icon-compare flex justify-center items-center absolute top-16 ltr:right-5 rtl:left-5 w-[30px] h-[30px] bg-white rounded-md text-2xl cursor-pointer"
                                role="button"
                                aria-label="@lang('shop::app.components.products.card.add-to-compare')"
                                tabindex="0"
                                @click="addToCompare(product.id)"
                            >
                            </span>
                        @endif

                        {!! view_render_event('bagisto.shop.components.products.card.compare_option.after') !!}
                    </div>
                </div>
            </div>

            <div class="grid gap-4 content-start">

                {!! view_render_event('bagisto.shop.components.products.card.name.before') !!}

                <p
                    class="text-base"
                    v-text="product.name"
                >
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.name.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.price.before') !!}

                <div
                    class="flex gap-2.5 text-sm font-semibold"
                    v-html="product.price_html"
                >
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                <!-- Needs to implement that in future -->
                <div class="hidden flex gap-4">
                    <span class="block w-[30px] h-[30px] rounded-full bg-[#B5DCB4]">
                    </span>

                    <span class="block w-[30px] h-[30px] rounded-full bg-[#5C5C5C]">
                    </span>
                </div>

                {!! view_render_event('bagisto.shop.components.products.card.price.after') !!}

                <p class="text-sm text-[#6E6E6E]" v-if="! product.avg_ratings">
                    @lang('shop::app.components.products.card.review-description')
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.before') !!}

                <p v-else class="text-sm text-[#6E6E6E]">
                    <x-shop::products.star-rating
                        ::value="product && product.avg_ratings ? product.avg_ratings : 0"
                        :is-editable=false
                    />
                </p>

                {!! view_render_event('bagisto.shop.components.products.card.average_ratings.after') !!}

                {!! view_render_event('bagisto.shop.components.products.card.add_to_cart.before') !!}

                <div class="flex gap-2.5 items-center">
                    <x-shop::quantity-changer
                        class="flex gap-x-2.5 border rounded-[10px] border-navyBlue py-2.5 px-3.5 items-center max-w-max"
                        name="quantity"
                        ::value="quantity"
                        @change="setItemQuantity"
                    />
                    <x-shop::button
                        class="primary-button px-8 py-2.5 whitespace-nowrap max-w-[150px] max-sm:w-full"
                        :title="trans('shop::app.components.products.card.add-to-quote')"
                        ::loading="isAddingToQuote"
                        ::disabled="! product.is_saleable || isAddingToQuote"
                        @click="addToQuote()"
                    />
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

                    showPopup: false,
                    defaultDimensionLength: 0,
                    defaultDimensionWidth: 0,
                    defaultDimensionHeight: 0,
                    pricePerUnit: 10,

                    calculatedPrice: this.product.price,
                }
            },

            methods: {
                calculateAndClose(productId) {
                    this.calculate(productId);
                    this.showPopup = false;
                },
                calculate(productId) {
                    const height = this.defaultDimensionHeight;
                    const width = this.defaultDimensionWidth;
                    const length = this.defaultDimensionLength;
                    const quantity = this.quantity;

                    // Basic validation
                    if (height <= 0 || width <= 0 || length <= 0 || quantity <= 0) {
                        alert('Please enter valid dimensions and quantity.');
                        return;
                    }

                    // Calculate the total price considering the quantity
                    const volume = height * width * length; // Calculate the volume based on dimensions
                    const calculatedPrice = volume * quantity * this
                        .pricePerUnit; // Calculate the price based on volume and quantity

                    // Update the product price with the calculated price
                    this.calculatedPrice = calculatedPrice;

                    // Optionally, you can also display the calculated price in an alert
                    alert(`The calculated price is: ${calculatedPrice} price units`);

                    // Update the UI to reflect the new price
                    this.updateProductPrice();
                },
                updateProductPrice() {
                    // Update the product price in the UI
                    this.product.price = this.calculatedPrice;
                },

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

                    this.$axios.post('{{ route('shop.api.checkout.quote.store') }}', {
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
