@php
    $options = $product->bundle_options()->with(['product','bundle_option_products','bundle_option_products.product.inventory_indices','bundle_option_products.product.images'])->get();
@endphp

<v-bundle-options :errors="errors"></v-bundle-options>

@pushOnce('scripts')
    <script type="text/x-template" id="v-bundle-options-template">
        <div class="box-shadow relative rounded-xl border bg-white p-5">
            <!-- Panel Header -->
            <div class="mb-2.5 grid grid-cols-3 justify-items-end gap-5">
                <div class="col-span-2 grid gap-2">
                    <p class="text-base font-semibold text-gray-800">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.title')
                    </p>

                    <p class="text-xs font-medium text-gray-500">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.info')
                    </p>
                </div>
                
                <!-- Add Button -->
                <div
                    class="secondary-button absolute"
                    @click="resetForm(); $refs.updateCreateOptionModal.open()"
                >
                    @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.add-btn')
                </div>
            </div>

            <!-- Panel Content -->
            <div
                class="grid"
                v-if="options.length"
            >
                <!-- Bundle Option Component -->
                <v-bundle-option-item
                    v-for='(option, index) in options'
                    :key="index"
                    :index="index"
                    :option="option"
                    :errors="errors"
                    @onEdit="selectedOption = $event; $refs.updateCreateOptionModal.open()"
                    @onRemove="removeOption($event)"
                ></v-bundle-option-item>
            </div>

            <!-- For Empty Option -->
            <div
                class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10"
                v-else
            >
                <!-- Placeholder Image -->
                <img
                    src="{{ bagisto_asset('images/icon-options.svg', 'marketplace') }}"
                    class="h-20 w-20 rounded border border-dashed"
                />

                <!-- Add Variants Information -->
                <div class="flex flex-col items-center">
                    <p class="text-base font-semibold text-gray-400">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.empty-title')
                    </p>

                    <p class="text-gray-400">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.empty-info')
                    </p>
                </div>

                <div
                    class="secondary-button text-sm"
                    @click="resetForm(); $refs.updateCreateOptionModal.open()"
                >
                    @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.add-btn')
                </div>
            </div>

            <!-- Add Option Form Modal -->
            <x-marketplace::shop.form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form @submit="handleSubmit($event, updateOrCreate)">
                    <!-- Customer Create Modal -->
                    <x-marketplace::shop.modal ref="updateCreateOptionModal">
                        <x-slot:header>
                            <!-- Modal Header -->
                            <p class="text-2xl font-medium leading-10 text-[#151515]">
                                @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.title')
                            </p>
                        </x-slot:header>
        
                        <x-slot:content>
                            <!-- Modal Content -->
                            <x-marketplace::shop.form.control-group>
                                <x-marketplace::shop.form.control-group.label class="required flex">
                                    @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.name')
                                </x-marketplace::shop.form.control-group.label>

                                <x-marketplace::shop.form.control-group.control
                                    type="text"
                                    name="label"
                                    rules="required"
                                    v-model="selectedOption.label"
                                    :label="trans('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.name')"
                                />
        
                                <x-marketplace::shop.form.control-group.error
                                    class="flex"
                                    control-name="label"
                                />
                            </x-marketplace::shop.form.control-group>

                            <div class="flex gap-4">
                                <x-marketplace::shop.form.control-group class="flex-1">
                                    <x-marketplace::shop.form.control-group.label class="required flex">
                                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.type')
                                    </x-marketplace::shop.form.control-group.label>

                                    <x-marketplace::shop.form.control-group.control
                                        type="select"
                                        name="type"
                                        rules="required"
                                        v-model="selectedOption.type"
                                        :label="trans('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.type')"
                                    >
                                        <option value="select">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.select')
                                        </option>

                                        <option value="radio">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.radio')
                                        </option>

                                        <option value="checkbox">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.checkbox')
                                        </option>

                                        <option value="multiselect">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.multiselect')
                                        </option>
                                    </x-marketplace::shop.form.control-group.control>
        
                                    <x-marketplace::shop.form.control-group.error
                                        class="flex"
                                        control-name="type"
                                    />
                                </x-marketplace::shop.form.control-group>

                                <x-marketplace::shop.form.control-group class="flex-1">
                                    <x-marketplace::shop.form.control-group.label class="required flex">
                                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.is-required')
                                    </x-marketplace::shop.form.control-group.label>

                                    <x-marketplace::shop.form.control-group.control
                                        type="select"
                                        name="is_required"
                                        rules="required"
                                        v-model="selectedOption.is_required"
                                        :label="trans('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.is-required')"
                                    >
                                        <option value="1">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.yes')
                                        </option>

                                        <option value="0">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.no')
                                        </option>
                                    </x-marketplace::shop.form.control-group.control>
        
                                    <x-marketplace::shop.form.control-group.error
                                        class="flex"
                                        control-name="is_required"
                                    />
                                </x-marketplace::shop.form.control-group>
                            </div>
                        </x-slot:content>

                        <x-slot:footer>
                            <!-- Modal Submission -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="w-28 rounded-2xl bg-navyBlue px-7 py-3 text-center text-base text-white"
                                >
                                    @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.update-create.save-btn')
                                </button>
                            </div>
                        </x-slot:footer>
                    </x-marketplace::shop.modal>
                </form>
            </x-marketplace::shop.form>
        </div>
    </script>

    <script type="text/x-template" id="v-bundle-option-item-template">
        <!-- Panel -->
        <div>
            <!-- Hidden Inputs -->
            <input type="hidden" :name="'bundle_options[' + option.id + '][{{$currentLocale->code}}][label]'" :value="option.label"/>

            <input type="hidden" :name="'bundle_options[' + option.id + '][type]'" :value="option.type"/>

            <input type="hidden" :name="'bundle_options[' + option.id + '][is_required]'" :value="option.is_required"/>

            <input type="hidden" :name="'bundle_options[' + option.id + '][sort_order]'" :value="index"/>

            <!-- Panel Header -->
            <div class="mb-2.5 flex justify-between gap-5 p-4">
                <div class="flex flex-col gap-2">
                    <p
                        class="text-base text-gray-800"
                        :class="{'required': option.is_required}"
                    >
                        @{{ (index + 1) + '. ' + option.label + ' - ' + types[option.type].title }}
                    </p>

                    <p class="text-xs text-gray-500">
                        @{{ types[option.type].info }}
                    </p>
                </div>
                
                <!-- Add Button -->
                <div class="flex items-center gap-x-5">
                    <p
                        class="cursor-pointer font-semibold text-navyBlue transition-all hover:underline"
                        @click="$refs['productSearch' + option.id].openDrawer()"
                    >
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.add-btn')
                    </p>

                    <p
                        class="cursor-pointer font-semibold text-navyBlue transition-all hover:underline"
                        @click="edit"
                    >
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.edit-btn')
                    </p>

                    <p
                        class="cursor-pointer font-semibold text-red-600 transition-all hover:underline"
                        @click="remove"
                    >
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.delete-btn')
                    </p>
                </div>
            </div>

            <!-- Panel Content -->
            <div
                class="grid"
                v-if="option.bundle_option_products.length"
            >
                <!-- Draggable Products -->
                <draggable
                    ghost-class="draggable-ghost"
                    v-bind="{animation: 200}"
                    :list="option.bundle_option_products"
                    item-key="id"
                >
                    <template #item="{ element, index }">
                        <div class="flex justify-between gap-2.5 border-b border-slate-300 py-4">
                            <!-- Information -->
                            <div class="flex gap-2.5">
                                <!-- Drag Icon -->
                                <i class="mp-drag-icon pointer-events-none text-xl text-gray-600 transition-all"></i>

                                <!-- Is Default Option -->
                                <div>
                                    <input
                                        :type="[option.type == 'checkbox' || option.type == 'multiselect' ? 'checkbox' : 'radio']"
                                        :name="'bundle_options[' + option.id + '][products][' + element.id + '][is_default]'"
                                        class="peer sr-only"
                                        :id="'bundle_options[' + option.id + '][products][' + element.id + '][is_default]'"
                                        :value="element.is_default"
                                        :checked="element.is_default"
                                        @change="updateIsDefault(element)"
                                    />

                                    <label
                                        class="cursor-pointer text-2xl peer-checked:text-navyBlue"
                                        :class="[option.type == 'checkbox' || option.type == 'multiselect' ? 'mp-uncheckbox-icon  peer-checked:mp-checked-icon' : 'mp-radio-unselect-icon peer-checked:mp-radio-select-icon']"
                                        :for="'bundle_options[' + option.id + '][products][' + element.id + '][is_default]'"
                                    >
                                    </label>
                                </div>
                                
                                <!-- Image -->
                                <div
                                    class="relative h-15 max-h-15 w-full max-w-15 overflow-hidden rounded"
                                    :class="{'border border-dashed border-gray-300 rounded': ! element.product.images.length}"
                                >
                                    <template v-if="! element.product.images.length">
                                        <img src="{{ bagisto_asset('images/product-placeholders/front.svg', 'marketplace') }}">
                                    
                                        <p class="absolute bottom-1 w-full text-center text-[6px] font-semibold text-gray-400">
                                            @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.image-placeholder')
                                        </p>
                                    </template>
                
                                    <template v-else>
                                        <img :src="element.product.images[0].url">
                                    </template>
                                </div>

                                <!-- Details -->
                                <div class="grid content-start gap-1.5">
                                    <p class="font-semibold text-[16x] text-gray-800">
                                        @{{ element.product.name }}
                                    </p>

                                    <p class="text-gray-600">
                                        @{{ "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.sku')".replace(':sku', element.product.sku) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="grid gap-1 text-right">
                                <p class="font-semibold text-gray-800">
                                    @{{ $shop.formatPrice(element.product.price) }}
                                </p>
                                
                                <!-- Hidden Input -->
                                <input
                                    type="hidden"
                                    :name="'bundle_options[' + option.id + '][products][' + element.id + '][product_id]'"
                                    :value="element.product.id"
                                />
                                
                                <input
                                    type="hidden"
                                    :name="'bundle_options[' + option.id + '][products][' + element.id + '][sort_order]'"
                                    :value="index"
                                />

                                <x-marketplace::shop.form.control-group class="!mb-0 grid justify-items-end">
                                    <x-marketplace::shop.form.control-group.label class="required">
                                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.default-qty')
                                    </x-marketplace::shop.form.control-group.label>

                                    <v-field
                                        type="text"
                                        :name="'bundle_options[' + option.id + '][products][' + element.id + '][qty]'"
                                        v-model="element.qty"
                                        class="flex min-h-10 w-[86px] rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400"
                                        :class="[errors['bundle_options[' + option.id + '][products][' + element.id + '][qty]'] ? 'border border-red-600 hover:border-red-600' : '']"
                                        rules="required|numeric|min_value:1"
                                        label="@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.default-qty')"
                                    ></v-field>

                                    <v-error-message
                                        :name="'bundle_options[' + option.id + '][products][' + element.id + '][qty]'"
                                        v-slot="{ message }"
                                    >
                                        <p class="mt-1 text-xs italic text-red-600">
                                            @{{ message }}
                                        </p>
                                    </v-error-message>
                                </x-marketplace::shop.form.control-group>

                                <p
                                    class="cursor-pointer text-red-600 transition-all hover:underline"
                                    @click="removeProduct(element)"
                                >
                                    @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.delete-btn')
                                </p>
                            </div>
                        </div>
                    </template>
                </draggable>
            </div>

            <!-- For Empty Option -->
            <div
                class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10"
                v-else
            >
                <!-- Placeholder Image -->
                <img
                    src="{{ bagisto_asset('images/icon-add-product.svg', 'marketplace') }}"
                    class="h-20 w-20"
                />

                <!-- Add Variants Information -->
                <div class="flex flex-col items-center">
                    <p class="text-base font-semibold text-gray-400">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.empty-title')
                    </p>

                    <p class="text-gray-400">
                        @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.empty-info')
                    </p>
                </div>

                <div
                    class="secondary-button text-sm"
                    @click="$refs['productSearch' + option.id].openDrawer()"
                >
                    @lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.add-btn')
                </div>
            </div>

            <!-- Product Search Blade Component -->
            <x-marketplace::shop.products.search
                ::ref="'productSearch' + option.id"
                ::added-product-ids="addedProductIds"
                ::query-params="{type: 'simple'}"
                @onProductAdded="addSelected($event)"
            >
            </x-marketplace::shop.products.search>
        </div>
    </script>

    <script type="module">
        app.component('v-bundle-options', {
            template: '#v-bundle-options-template',

            props: ['errors'],

            data() {
                return {
                    options: @json($options),

                    selectedOption: {
                        label: '',
                        type: 'select',
                        is_required: 1,
                        bundle_option_products: []
                    }
                }
            },

            methods: {
                updateOrCreate(params) {
                    if (this.selectedOption.id == undefined) {
                        params.id = 'option_' + this.options.length;

                        params.bundle_option_products = [];

                        this.options.push(params);
                    } else {
                        const indexToUpdate = this.options.findIndex(option => option.id === this.selectedOption.id);

                        this.options[indexToUpdate] = this.selectedOption;
                    }

                    this.resetForm();

                    this.$refs.updateCreateOptionModal.close();
                },

                removeOption(option) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            let index = this.options.indexOf(option);

                            this.options.splice(index, 1);
                        }
                    });
                },

                resetForm() {
                    this.selectedOption = {
                        label: '',
                        type: 'select',
                        is_required: 1,
                        bundle_option_products: []
                    };
                },
            }
        });

        app.component('v-bundle-option-item', {
            template: '#v-bundle-option-item-template',

            props: ['index', 'option', 'errors'],

            data() {
                return {
                    types: {
                        select: {
                            key: 'select',
                            title: "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.types.select.title')",
                            info: "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.types.select.info')"
                        },

                        radio: {
                            key: 'radio',
                            title: "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.types.radio.title')",
                            info: "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.types.radio.info')"
                        },

                        multiselect: {
                            key: 'multiselect',
                            title: "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.types.multiselect.title')",
                            info: "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.types.multiselect.info')"
                        },

                        checkbox: {
                            key: 'checkbox',
                            title: "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.types.checkbox.title')",
                            info: "@lang('marketplace::app.shop.sellers.account.products.edit.types.bundle.option.types.checkbox.info')"
                        }
                    },
                }
            },

            computed: {
                addedProductIds() {
                    return this.option.bundle_option_products.map(productOption => productOption.product.id);
                }
            },

            methods: {
                edit() {
                    this.$emit('onEdit', this.option);
                },

                remove() {
                    this.$emit('onRemove', this.option);
                },

                addSelected(selectedProducts) {
                    let self = this;

                    selectedProducts.forEach(function (product) {
                        self.option.bundle_option_products.push({
                            id: 'product_' + self.option.bundle_option_products.length,
                            qty: 1,
                            is_default: 0,
                            product: product,
                        });
                    });
                },

                removeProduct(product) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            let index = this.option.bundle_option_products.indexOf(product);

                            this.option.bundle_option_products.splice(index, 1);
                        }
                    });
                },

                updateIsDefault: function(updatedProductOption) {
                    let self = this;

                    this.option.bundle_option_products.forEach(function(productOption) {
                        if (self.option.type == 'radio'
                            || self.option.type == 'select'
                        ) {
                            productOption.is_default = productOption.product.id == updatedProductOption.product.id ? 1 : 0;
                        } else {
                            if (productOption.product.id == updatedProductOption.product.id) {
                                productOption.is_default = productOption.is_default ? 0 : 1;
                            }
                        }
                    });
                }
            }
        });
    </script>
@endPushOnce