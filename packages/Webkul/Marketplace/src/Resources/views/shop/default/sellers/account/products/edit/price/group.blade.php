<!-- Seperator -->
<span class="absolute my-1.5 block w-full border border-gray-200 ltr:left-0 rtl:right-0"></span>

<v-product-customer-group-price>
    <x-admin::shimmer.catalog.products.edit.group-price />
</v-product-customer-group-price>

@inject('customerGroupRepository', 'Webkul\Customer\Repositories\CustomerGroupRepository')

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-customer-group-price-template"
    >
        <div>
            <!-- Header -->
            <div class="mt-1.5 flex items-center justify-between py-4">
                <p class="py-2.5 text-base font-semibold text-gray-800 dark:text-white">
                    @lang('marketplace::app.shop.sellers.account.products.edit.price.group.title')
                </p>

                <p
                    class="cursor-pointer text-blue-600 transition-all hover:underline"
                    @click="resetForm(); $refs.groupPriceCreateModal.open()"
                >
                    @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create-btn')
                </p>
            </div>

            <!-- Content -->
            <div class="grid">
                <!-- Card -->
                <div
                    class="flex flex-col gap-2 py-2.5"
                    v-for="(item, index) in prices"
                >
                    <!-- Hidden Inputs -->
                    <input
                        type="hidden"
                        :name="'customer_group_prices[' + item.id + '][customer_group_id]'"
                        :value="item.customer_group_id"
                    />

                    <input
                        type="hidden"
                        :name="'customer_group_prices[' + item.id + '][qty]'"
                        :value="item.qty"
                    />

                    <input
                        type="hidden"
                        :name="'customer_group_prices[' + item.id + '][value_type]'"
                        :value="item.value_type"
                    />

                    <input
                        type="hidden"
                        :name="'customer_group_prices[' + item.id + '][value]'"
                        :value="item.value"
                    />

                    <div class="flex justify-between">
                        <p class="font-semibold text-gray-600 dark:text-gray-300">
                            @{{ getGroupNameById(item.customer_group_id) }}
                        </p>

                        <p
                            class="cursor-pointer text-blue-600 transition-all hover:underline"
                            @click="selectedPrice = item; $refs.groupPriceCreateModal.open()"
                        >
                            @lang('marketplace::app.shop.sellers.account.products.edit.price.group.edit-btn')
                        </p>
                    </div>

                    <p
                        class="text-gray-600 dark:text-gray-300"
                        v-if="item.value_type == 'fixed'"
                    >
                        @{{ "@lang('marketplace::app.shop.sellers.account.products.edit.price.group.fixed-group-price-info')".replace(':qty', item.qty).replace(':price', item.value) }}
                    </p>

                    <p
                        class="text-gray-600 dark:text-gray-300"
                        v-else
                    >
                        @{{ "@lang('marketplace::app.shop.sellers.account.products.edit.price.group.discount-group-price-info')".replace(':qty', item.qty).replace(':price', item.value) }}
                    </p>
                </div>

                <!-- Empty Container -->
                <div
                    class="flex items-center gap-5 py-2.5"
                    v-if="! prices.length"
                >
                    <img
                        src="{{ bagisto_asset('images/icon-discount.svg', 'marketplace') }}"
                        class="h-20 w-20 rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert"
                    />

                    <div class="flex flex-col gap-1.5">
                        <p class="text-base font-semibold text-gray-400">
                            @lang('marketplace::app.shop.sellers.account.products.edit.price.group.add-group-price')
                        </p>

                        <p class="text-gray-400">
                            @lang('marketplace::app.shop.sellers.account.products.edit.price.group.empty-info')
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Modal -->
            <x-marketplace::shop.form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
                ref="groupPriceForm"
            >
                <form @submit="handleSubmit($event, create)">
                    <!-- Customer Create Modal -->
                    <x-marketplace::shop.modal ref="groupPriceCreateModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p
                                class="text-lg font-bold text-gray-800 dark:text-white"
                                v-if="! selectedPrice.id"
                            >
                                @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.create-title')
                            </p>

                            <p
                                class="text-lg font-bold text-gray-800 dark:text-white"
                                v-else
                            >
                                @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.update-title')
                            </p>    
                        </x-slot>
        
                        <!-- Modal Content -->
                        <x-slot:content>
                            <x-marketplace::shop.form.control-group>
                                <x-marketplace::shop.form.control-group.label>
                                    @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.customer-group')
                                </x-marketplace::shop.form.control-group.label>
    
                                <x-marketplace::shop.form.control-group.control
                                    type="select"
                                    name="customer_group_id"
                                    v-model="selectedPrice.customer_group_id"
                                    :label="trans('marketplace::app.shop.sellers.account.products.edit.price.group.create.customer-group')"
                                >
                                    <option value="">
                                        @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.all-groups')
                                    </option>

                                    <option
                                        v-for="group in groups"
                                        :value="group.id"
                                    >
                                        @{{ group.name }}
                                    </option>
                                </x-marketplace::shop.form.control-group.control>
                            </x-marketplace::shop.form.control-group>

                            <div class="flex gap-4">
                                <x-marketplace::shop.form.control-group class="flex-1">
                                    <x-marketplace::shop.form.control-group.label class="required">
                                        @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.qty')
                                    </x-marketplace::shop.form.control-group.label>
        
                                    <x-marketplace::shop.form.control-group.control
                                        type="text"
                                        name="qty"
                                        rules="required|numeric|min_value:1"
                                        v-model="selectedPrice.qty"
                                        :label="trans('marketplace::app.shop.sellers.account.products.edit.price.group.create.qty')"
                                    />
        
                                    <x-marketplace::shop.form.control-group.error control-name="qty" />
                                </x-marketplace::shop.form.control-group>

                                <x-marketplace::shop.form.control-group class="flex-1">
                                    <x-marketplace::shop.form.control-group.label class="required">
                                        @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.price-type')
                                    </x-marketplace::shop.form.control-group.label>
        
                                    <x-marketplace::shop.form.control-group.control
                                        type="select"
                                        name="value_type"
                                        rules="required"
                                        v-model="selectedPrice.value_type"
                                        :label="trans('marketplace::app.shop.sellers.account.products.edit.price.group.create.price-type')"
                                    >
                                        <option value="fixed">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.fixed')
                                        </option>

                                        <option value="discount">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.discount')
                                        </option>
                                    </x-marketplace::shop.form.control-group.control>
        
                                    <x-marketplace::shop.form.control-group.error control-name="value_type" />
                                </x-marketplace::shop.form.control-group>

                                <x-marketplace::shop.form.control-group class="flex-1">
                                    <x-marketplace::shop.form.control-group.label class="required">
                                        @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.price')
                                    </x-marketplace::shop.form.control-group.label>
        
                                    <x-marketplace::shop.form.control-group.control
                                        type="text"
                                        name="value"
                                        ::rules="{required: true, decimal: true, min_value: 0, ...(selectedPrice.value_type === 'discount' ? {max_value: 100} : {})}"
                                        v-model="selectedPrice.value"
                                        :label="trans('marketplace::app.shop.sellers.account.products.edit.price.group.create.price')"
                                    />
        
                                    <x-marketplace::shop.form.control-group.error control-name="value" />
                                </x-marketplace::shop.form.control-group>
                            </div>
                        </x-slot>
        
                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Modal Submission -->
                            <div class="flex items-center justify-end py-4">
                                <button
                                    type="button"
                                    class="cursor-pointer whitespace-nowrap rounded-md border-2 border-transparent px-3 py-1.5 font-semibold text-red-600 transition-all hover:bg-gray-100"
                                    @click="remove"
                                    v-if="selectedPrice.id"
                                >
                                    @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.delete-btn')
                                </button>

                                <button 
                                    type="submit"
                                    class="primary-button"
                                >
                                    @lang('marketplace::app.shop.sellers.account.products.edit.price.group.create.save-btn')
                                </button>
                            </div>
                        </x-slot>
                    </x-marketplace::shop.modal>
                </form>
            </x-marketplace::shop.form>
        </div>
    </script>

    <script type="module">
        app.component('v-product-customer-group-price', {
            template: '#v-product-customer-group-price-template',

            data: function() {
                return {
                    groups: @json($customerGroupRepository->all()),

                    prices: @json($product->customer_group_prices),

                    selectedPrice: {
                        customer_group_id: null,
                        qty: 1,
                        value_type: 'fixed',
                        value: 0,
                    }
                }
            },

            methods: {
                getGroupNameById(id) {
                    let group = this.groups.find(group => group.id == id);

                    return group ? group.name : "@lang('marketplace::app.shop.sellers.account.products.edit.price.group.all-groups')";
                },

                create(params) {
                    if (this.selectedPrice.id == undefined) {
                        params.id = 'price_' + this.prices.length;

                        this.prices.push(params);
                    } else {
                        const indexToUpdate = this.prices.findIndex(price => price.id === this.selectedPrice.id);

                        this.prices[indexToUpdate] = this.selectedPrice;
                    }

                    this.resetForm();

                    this.$refs.groupPriceCreateModal.close();
                },

                resetForm() {
                    this.selectedPrice = {
                        customer_group_id: null,
                        qty: 1,
                        value_type: 'fixed',
                        value: 0,
                    };
                },

                remove() {
                    this.$refs.groupPriceCreateModal.close();

                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            let index = this.prices.indexOf(this.selectedPrice);

                            this.prices.splice(index, 1);

                            this.resetForm();
                        }
                    });
                }
            }
        });
    </script>
@endPushOnce
