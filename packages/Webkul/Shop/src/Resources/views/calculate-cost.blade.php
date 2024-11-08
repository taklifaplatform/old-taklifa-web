<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="@lang('shop::app.checkout.onepage.index.checkout')" />

    <meta name="keywords" content="@lang('shop::app.checkout.onepage.index.checkout')" />
@endPush

<x-shop::layouts>
    <!-- Page Title -->
    <x-slot name="title">
        {{ $channel->home_seo['meta_title'] ?? 'حاسبة السعر' }}
    </x-slot>

    <v-calculator>
        <x-shop::shimmer.checkout.onepage />
    </v-calculator>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-calculator-template">


            <div v-if="!showResults">
                <!-- Input Card -->
                <h2 class="text-xl mt-4 text-center">
                    @lang('shop::app.checkout.cart.index.construction-calculator')
                </h2>
                <div class="flex justify-center items-center p-4">
                    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md">
                        <!-- Quantity Input -->
                        <div class="mb-4">
                            <label for="quantity" class="block text-sm font-medium text-gray-700">
                                @lang('shop::app.checkout.cart.index.land-area')
                            </label>
                            <input class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm p-2 no-arrows"
                                   v-model="quantity" @blur="validateQuantity"
                                   type="number" id="quantity" min="1"
                                   placeholder="@lang('shop::app.checkout.cart.index.land-area')"
                                   :class="{'border-red-500': errorMessage}" />
                            <p v-if="errorMessage" class="text-red-500 text-sm mt-1">
                                @lang('shop::app.checkout.cart.index.please-enter')
                            </p>
                        </div>

                        <!-- Number of Floors -->
                        <div class="mt-2">
                            <label for="floors" class="block text-sm font-medium text-gray-700">
                                @lang('shop::app.checkout.cart.index.number-of-floors')
                            </label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <button :class="[
                                        'button-option',
                                        selectedFloor === 1 ? 'bg-[#14532d] text-white' : 'bg-white text-gray-700',
                                        'border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-100 flex-1',
                                    ]"
                                    @click="selectFloor(1)">
                                    @lang('shop::app.checkout.cart.index.one-floor')
                                </button>
                                <button :class="[
                                        'button-option',
                                        selectedFloor === 2 ? 'bg-[#14532d] text-white' : 'bg-white text-gray-700',
                                        'border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-100 flex-1',
                                    ]"
                                    @click="selectFloor(2)">
                                    @lang('shop::app.checkout.cart.index.two-floors')
                                </button>
                                <button :class="[
                                        'button-option',
                                        selectedFloor === 3 ? 'bg-[#14532d] text-white' : 'bg-white text-gray-700',
                                        'border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-100 flex-1',
                                    ]"
                                    @click="selectFloor(3)">
                                    @lang('shop::app.checkout.cart.index.two-floors-with-an-annex')
                                </button>
                            </div>
                            <p v-if="errorFloorMessage" class="text-red-500 text-sm mt-1">
                                @lang('shop::app.checkout.cart.index.select-number-floors')
                            </p>
                        </div>

                        <!-- Basement Toggle and Input -->
                        <div id="app" class="mt-8">
                            <!-- Toggle for Basement -->
                            <div class="mb-4 flex items-center">
                                <input v-model="hasBasement" type="checkbox" id="basement-toggle" class="toggle-switch" />
                                <label for="basement-toggle" class="block text-sm font-medium text-gray-700 mr-4">
                                    @lang('shop::app.checkout.cart.index.basement')
                                </label>
                            </div>

                            <!-- Conditional Input for Basement -->
                            <div v-if="hasBasement" class="mb-4">
                                <input v-model="basementNumber"
                                       placeholder="@lang('shop::app.checkout.cart.index.basement-area')"
                                       type="number" id="basement-number" min="1"
                                       :class="{'border-red-500': basementError, 'border-gray-300': !basementError}"
                                       class="mt-1 block w-full border rounded-md shadow-sm p-2 no-arrows"
                                       @blur="validateBasement" />
                                <p v-if="basementError" class="text-red-500 mt-1">
                                  @lang('shop::app.checkout.cart.index.basement-error')
                                </p>
                            </div>
                        </div>

                        <!-- Calculate Cost Button -->
                        <div class="mt-8">
                            <button @click="calculateTotalPrice"
                                    class="w-full text-white font-bold py-2 px-4 rounded-md hover:bg-[#1a4d3f] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                                    style="background-color: #14532d;">
                                @lang('shop::app.checkout.cart.index.calculate-the-cost')
                            </button>
                            <p v-if="generalErrorMessage" class="text-red-500 text-sm mt-1">
                                @{{ generalErrorMessage }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Card -->
            <div v-if="showResults">
                <h2 class="text-xl mt-4 text-center">
                    @lang('shop::app.checkout.cart.index.construction-cost')
                </h2>
                <div class="flex justify-center items-center p-4">
                    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md">
                        <div class="flex items-center mb-4">
                            <div class="flex-1 border p-2 text-center font-bold">
                                @lang('shop::app.checkout.cart.index.land-area')
                                <br />
                                <span class="text-[#14532d]">@{{ quantity }}</span>
                            </div>

                            <div class="flex-1 border p-2 text-center font-bold">
                                @lang('shop::app.checkout.cart.index.number-of-floors')
                                <br />
                                <span class="text-[#14532d]">@{{ selectedFloor }}</span>
                            </div>

                            <div class="flex-1 border p-2 text-center font-bold">
                                @lang('shop::app.checkout.cart.index.basement-area')
                                <br />
                                <span class="text-[#14532d]"> @{{ basementNumber }}</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h2 class="font-bold">
                                @lang('shop::app.checkout.cart.index.construction-areas')
                            </h2>
                            <div class="flex items-center mt-2">
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    <span>@lang('shop::app.checkout.cart.index.ground-floor')</span>
                                    <span class="text-[#14532d]">11</span>
                                </div>
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    <span>@lang('shop::app.checkout.cart.index.the-first-floor')</span>
                                    <span class="text-[#14532d]">11</span>
                                </div>
                            </div>

                            <div class="flex items-center mt-2">
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    @lang('shop::app.checkout.cart.index.appendix')
                                     <span class="text-[#14532d]">11</span>
                                </div>
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    @lang('shop::app.checkout.cart.index.the-wall')
                                    <span class="text-[#14532d]">11</span>
                                </div>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    @lang('shop::app.checkout.cart.index.water-tank')
                                     <span class="text-[#14532d]">11</span>
                                </div>
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    @lang('shop::app.checkout.cart.index.septic-tank')
                                    <span class="text-[#14532d]">11</span>
                                </div>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    @lang('shop::app.checkout.cart.index.the-pool')
                                     <span class="text-[#14532d]">11</span>
                                </div>
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    @lang('shop::app.checkout.cart.index.total-cons')
                                     <span class="text-[#14532d]">11</span>
                                </div>
                            </div>
                        </div>


                        <div class="mt-4">
                            <h2 class="font-bold">
                                @lang('shop::app.checkout.cart.index.costs')
                            </h2>

                            <div class="flex items-center mt-4">
                                <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                    @lang('shop::app.checkout.cart.index.skeleton-construction')
                                     <span class="text-[#14532d]">11</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center mt-4">
                            <div class="flex-1 border p-2 text-center text-[#14532d] font-bold">
                                @lang('shop::app.checkout.cart.index.finishing')
                             </div>

                            <div class="flex-1 border p-2 text-center text-[#14532d] font-bold">
                                @lang('shop::app.checkout.cart.index.turnkey')
                            </div>
                        </div>

                        <div class="flex items-center mt-2">
                            <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                @lang('shop::app.checkout.cart.index.normal')
                                 <span class="text-[#14532d]">11 ريال</span>
                            </div>
                            <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                @lang('shop::app.checkout.cart.index.normal')
                                 <span class="text-[#14532d]">11 ريال</span>
                            </div>
                        </div>

                        <div class="flex items-center mt-2">
                            <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                @lang('shop::app.checkout.cart.index.excellent')
                                 <span class="text-[#14532d]">11 ريال</span>
                            </div>
                            <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                @lang('shop::app.checkout.cart.index.excellent')
                                 <span class="text-[#14532d]">11 ريال</span>
                            </div>
                        </div>

                        <div class="flex items-center mt-2">
                            <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                @lang('shop::app.checkout.cart.index.luxury')
                                 <span class="text-[#14532d]">11 ريال</span>
                            </div>
                            <div class="flex-1 border p-2 text-center flex justify-between items-center">
                                @lang('shop::app.checkout.cart.index.luxury')
                                 <span class="text-[#14532d]">11 ريال</span>
                            </div>
                        </div>

                <div class="mt-6">
                    <button @click="resetCalculator"
                        class="w-full text-white font-bold py-2 px-4 rounded-md hover:bg-[#1a4d3f] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                        style="background-color: #14532d;">
                        @lang('shop::app.checkout.cart.index.calculate-again')
                    </button>
                </div>
            </div>

    </script>

        <script type="module">
            app.component('v-calculator', {
                template: '#v-calculator-template',
                data() {
                    return {
                        quantity: null,
                        hasBasement: false,
                        basementNumber: '',
                        basementError: false,
                        selectedFloor: null,
                        errorMessage: null,
                        errorFloorMessage: null,
                        generalErrorMessage: null,
                        result: null,
                        showResults: false
                    };
                },
                methods: {
                    selectFloor(floor) {
                        this.selectedFloor = floor;
                        this.errorFloorMessage = null;
                    },
                    validateQuantity() {
                        if (this.quantity < 200 || this.quantity > 2500) {
                            this.errorMessage = 'Land area must be between 200 and 2500 sqm';
                        } else {
                            this.errorMessage = null;
                        }
                    },
                    validateBasement() {
                        if (this.hasBasement && (!this.basementNumber || this.basementNumber < 1)) {
                            this.basementError = true;
                        } else {
                            this.basementError = false;
                        }
                    },
                    calculateTotalPrice() {
                        this.validateQuantity();
                        this.validateBasement();

                        if (!this.quantity) {
                            this.errorMessage = 'Land area is required';
                        }
                        if (!this.selectedFloor) {
                            this.errorFloorMessage = 'Please select the number of floors';
                        }

                        if (this.errorMessage || this.basementError || this.errorFloorMessage) {
                            this.generalErrorMessage = 'Please correct the errors above before calculating.';
                            this.result = null;
                            this.showResults = false;
                            return;
                        }

                        this.generalErrorMessage = null;

                        // Dummy calculation logic (replace with actual calculation)
                        const basePricePerSqm = 50; // Example base price per square meter
                        const floorPrice = this.selectedFloor * 1000; // Example price per floor
                        const basementPrice = this.hasBasement ? this.basementNumber * 200 :
                            0; // Example price for basement

                        this.result = (this.quantity * basePricePerSqm) + floorPrice + basementPrice;
                        this.showResults = true;
                    },
                    resetCalculator() {
                        this.quantity = null;
                        this.hasBasement = false;
                        this.basementNumber = '';
                        this.selectedFloor = null;
                        this.result = null;
                        this.showResults = false;
                        this.errorMessage = null;
                        this.errorFloorMessage = null;
                        this.generalErrorMessage = null;
                    }
                },
                watch: {
                    basementNumber() {
                        this.validateBasement();
                    },
                    hasBasement() {
                        this.validateBasement();
                    },
                }
            });
        </script>
    @endPushOnce


</x-shop::layouts>

<style>
    .toggle-switch {
        appearance: none;
        width: 40px;
        height: 20px;
        background-color: #e5e7eb;
        border-radius: 9999px;
        position: relative;
        cursor: pointer;
        outline: none;
        transition: background-color 0.2s;
    }

    .toggle-switch:checked {
        background-color: #14532d;
    }

    .toggle-switch:before {
        content: "";
        position: absolute;
        top: 2px;
        left: 2px;
        width: 16px;
        height: 16px;
        background-color: white;
        border-radius: 9999px;
        transition: transform 0.2s;
    }

    .toggle-switch:checked:before {
        transform: translateX(20px);
    }

    input[type="number"].no-arrows::-webkit-outer-spin-button,
    input[type="number"].no-arrows::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"].no-arrows {
        -moz-appearance: textfield;
    }

    .gap-x-2.5 {
        gap: 10px;
    }
</style>
