<v-product-variations></v-product-variations>

@pushOnce('scripts')
    <!-- Variations Template -->
    <script
        type="text/x-template"
        id="v-product-variations-template"
    >
        <div class="box-shadow relative rounded-xl border bg-white p-5 mt-2.5">
            <!-- Panel Header -->
            <div class="mb-4">
                <p class="text-base font-semibold text-gray-800">
                    @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.title')
                </p>

                <p class="text-xs font-medium text-gray-500">
                    @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.info')
                </p>
            </div>

            <!-- Panel Content -->
            <div class="grid">
                <v-product-variation-item
                    v-for='(variant, index) in variants'
                    :key="index"
                    :index="index"
                    :variant="variant"
                    :attributes="superAttributes"
                >
                </v-product-variation-item>
            </div>
        </div>
    </script>

    <!-- Variation Item Template -->
    <script
        type="text/x-template"
        id="v-product-variation-item-template"
    >
        <div
            class="flex justify-between gap-2.5 border-b border-slate-300 py-6 last:border-none"
            :class="[selected_variants[variant.id] ? '' : 'opacity-50']"
        >
            <!-- Information -->
            <div class="flex gap-2.5">
                <input
                    type="hidden"
                    :name="'variants[' + variant.id + '][price]'"
                    :value="variant.price"
                />

                <input
                    type="hidden"
                    :name="'variants[' + variant.id + '][description]'"
                    :value="variant.description"
                />

                <template v-for="inventorySource in inventorySources">
                    <input
                        type="hidden"
                        :name="'variants[' + variant.id + '][inventories][' + inventorySource.id + ']'"
                        :value="variant.inventories[inventorySource.id]"
                    />
                </template>

                <template v-for="(image, index) in variant.images">
                    <input
                        type="hidden"
                        :name="'variants[' + variant.id + '][images][files][' + image.id + ']'"
                        v-if="! image.is_new"
                    />

                    <input
                        type="file"
                        :name="'variants[' + variant.id + '][images][files][]'"
                        :id="$.uid + '_imageInput_' + index"
                        class="hidden"
                        accept="image/*"
                        :ref="$.uid + '_imageInput_' + index"
                    />
                </template>

                <!-- Selection Checkbox -->
                <div class="select-none">
                    <input
                        type="checkbox"
                        :id="'variant_' + variant.id"
                        name="selected_variants[]"
                        :value="variant.id"
                        class="peer hidden"
                        v-model="selected_variants[variant.id]"
                    >

                    <label
                        class="mp-uncheckbox-icon peer-checked:mp-checked-icon cursor-pointer text-2xl peer-checked:text-navyBlue"
                        :for="'variant_' + variant.id"
                    ></label>
                </div>

                <!-- Image -->
                <div
                    class="relative h-20 max-h-20 w-full max-w-20 overflow-hidden rounded"
                    :class="{'border border-dashed border-gray-300': ! variant.images.length}"
                >
                    <template v-if="! variant.images.length">
                        <img src="{{ bagisto_asset('images/product-placeholders/front.svg', 'marketplace') }}">
                    
                        <p class="absolute bottom-1 w-full text-center text-[6px] font-semibold text-gray-400">
                            @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.image-placeholder')
                        </p>
                    </template>

                    <template v-else>
                        <img :src="variant.images[0].url">

                        <span class="bg-pink-600 absolute bottom-px rounded-full px-[6px] text-xs font-bold text-white ltr:left-px rtl:right-px">
                            @{{ variant.images.length }}
                        </span>
                    </template>
                </div>

                <!-- Details -->
                <div class="flex flex-col gap-1.5">
                    <p class="font-semibold text-gray-800">
                        @{{ variant.name ?? 'N/A' }}
                    </p>

                    <p class="text-gray-600">
                        @{{ "@lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.sku')".replace(':sku', variant.sku) }}
                    </p>

                    <p class="text-gray-600">
                        <span
                            class="after:content-[',_'] last:after:content-['']"
                            v-for='(attribute, index) in attributes'
                        >
                            @{{ attribute.admin_name + ': ' + optionName(attribute, variant[attribute.code]) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="grid place-content-start gap-1 text-right">
                <p class="font-semibold text-gray-800">
                    @{{ $shop.formatPrice(variant.price) }}  
                </p>

                <p class="font-semibold text-gray-800">
                    @{{ "@lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.qty')".replace(':qty', totalQty) }}
                </p>

                <div class="flex gap-2.5">
                    <!-- Edit -->
                    <div>
                        <p
                            class="cursor-pointer text-emerald-600 transition-all hover:underline"
                            v-if="selected_variants[variant.id]"
                            @click="$refs.editVariantDrawer.open()"
                        >
                            @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.edit-btn')
                        </p>

                        <!-- Edit Drawer -->
                        <x-marketplace::shop.form
                            v-slot="{ meta, errors, handleSubmit }"
                            as="div"
                        >
                            <form @submit="handleSubmit($event, update)">
                                <!-- Edit Drawer -->
                                <x-marketplace::shop.drawer
                                    ref="editVariantDrawer"
                                    class="text-left"
                                >
                                    <!-- Drawer Header -->
                                    <x-slot:header>
                                        <div class="flex items-center justify-between">
                                            <p class="text-xl font-medium">
                                                @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.edit.title')
                                            </p>

                                            <button class="primary-button ltr:mr-11 rtl:ml-11">
                                                @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.edit.save-btn')
                                            </button>
                                        </div>
                                    </x-slot>

                                    <!-- Drawer Content -->
                                    <x-slot:content>
                                        <x-marketplace::shop.form.control-group.control
                                            type="hidden"
                                            name="id"
                                            ::value="variant.id"
                                        />

                                        <x-marketplace::shop.form.control-group class="flex-1">
                                            <x-marketplace::shop.form.control-group.label class="required">
                                                @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.edit.price')
                                            </x-marketplace::shop.form.control-group.label>
                
                                            <x-marketplace::shop.form.control-group.control
                                                type="text"
                                                name="price"
                                                ::rules="{required: true, decimal: true, min_value: 0}"
                                                ::value="variant.price"
                                                :label="trans('marketplace::app.shop.sellers.account.products.edit.types.configurable.edit.price')"
                                            />
                
                                            <x-marketplace::shop.form.control-group.error control-name="price" />
                                        </x-marketplace::shop.form.control-group>

                                        <!-- Inventories -->
                                        <div class="mt-5 grid">
                                            <p class="mb-2.5 font-semibold text-gray-800">
                                                @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.edit.quantities')
                                            </p>

                                            <div class="mb-2.5 grid grid-cols-3 gap-4">
                                                <x-marketplace::shop.form.control-group
                                                    class="!mb-0"
                                                    v-for='inventorySource in inventorySources'
                                                >
                                                    <x-marketplace::shop.form.control-group.label>
                                                        @{{ inventorySource.name }}
                                                    </x-marketplace::shop.form.control-group.label>

                                                    <v-field
                                                        type="text"
                                                        class="flex min-h-10 w-full rounded-md border bg-white px-3 py-1.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400"
                                                        :name="'inventories[' + inventorySource.id + ']'"
                                                        rules="numeric|min:0"
                                                        v-model="variant.inventories[inventorySource.id]"
                                                        :label="inventorySource.name"
                                                    >
                                                    </v-field>
                                                </x-marketplace::shop.form.control-group>
                                            </div>
                                        </div>

                                        <x-marketplace::shop.form.control-group class="flex-1">
                                            <x-marketplace::shop.form.control-group.label class="required">
                                                @lang('marketplace::app.shop.sellers.account.products.assign.description')
                                            </x-marketplace::shop.form.control-group.label>
                
                                            <x-marketplace::shop.form.control-group.control
                                                type="textarea"
                                                name="description"
                                                rules="required"
                                                ::value="variant.description"
                                                :label="trans('marketplace::app.shop.sellers.account.products.assign.description')"
                                            />
                
                                            <x-marketplace::shop.form.control-group.error control-name="description" />
                                        </x-marketplace::shop.form.control-group>

                                        <!-- Images -->
                                        <div class="mb-2.5">
                                            <p class="mb-2.5 font-semibold text-gray-800">
                                                @lang('marketplace::app.shop.sellers.account.products.edit.types.configurable.edit.images')
                                            </p>

                                            <v-media-images
                                                name="images"
                                                v-bind:allow-multiple="true"
                                                :uploaded-images="variant.images"
                                            >
                                            </v-media-images>
                                        </div>
                                    </x-slot>
                                </x-marketplace::shop.drawer>
                            </form>
                        </x-marketplace::shop.form>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-product-variations', {
            template: '#v-product-variations-template',

            data () {
                return {
                    variants: @json($baseProduct->variants()->get()),

                    assignedProduct: @json(isset($product) ? $product->with(['variants', 'variants.images', 'variants.product.inventories'])->first() : []),

                    superAttributes: @json($baseProduct->super_attributes()->with(['options'])->get()),
                }
            },

            created() {
                this.variants.forEach(variant => {
                    variant.price = 0.00;
                    variant.description = '';
                    variant.images = [];
                    variant.inventories = {};

                    if (! this.assignedProduct.variants?.length) {
                        return;
                    }

                    this.assignedProduct.variants.forEach(assignedVariant => {
                        if (variant.id === assignedVariant.product_id) {
                            variant.assignedVariant = true;

                            variant.price = assignedVariant.price;

                            variant.images = assignedVariant.images;

                            variant.description = assignedVariant.description;

                            variant.inventories = assignedVariant.product.inventories;
                        }
                    });
                });
            }
        });

        app.component('v-product-variation-item', {
            template: '#v-product-variation-item-template',

            props: [
                'variant',
                'attributes',
            ],

            data() {
                return {
                    vendorId: @json(auth()->guard('seller')->user()->id),

                    inventorySources: @json($inventorySources),

                    selected_variants: {},

                    inventories: {},
                }
            },

            created() {
                let inventories = {};
                
                if (Array.isArray(this.variant.inventories)) {
                    this.variant.inventories.forEach(inventory => {
                        if (inventory.vendor_id == this.vendorId) {                            
                            inventories[inventory.inventory_source_id] = inventory.qty;
                        }                        
                    });                    

                    this.variant.inventories = inventories;                    
                }

                if (this.variant.assignedVariant) {
                    this.selected_variants[this.variant.id] = true;
                }
            },
            
            computed: {
                totalQty() {
                    let totalQty = 0;

                    for (let key in this.variant.inventories) {
                        totalQty += parseInt(this.variant.inventories[key]);
                    }

                    return totalQty;
                }
            },

            watch: {
                variant: {
                    handler(newValue) {
                        setTimeout(() => this.setFiles());
                    },
                    deep: true
                }
            },

            methods: {
                optionName(attribute, optionId) {
                    return attribute.options.find((option) => {
                        return option.id == optionId;
                    })?.admin_name;
                },

                update(params) {
                    Object.assign(this.variant, params);

                    this.$refs.editVariantDrawer.close();
                },

                setFiles() {
                    this.variant.images.forEach((image, index) => {
                        if (image.file instanceof File) {
                            image.is_new = 1;

                            const dataTransfer = new DataTransfer();

                            dataTransfer.items.add(image.file);

                            this.$refs[this.$.uid + '_imageInput_' + index][0].files = dataTransfer.files;
                        } else {
                            image.is_new = 0;
                        }
                    });
                },
            }
        });
    </script>
@endPushOnce
