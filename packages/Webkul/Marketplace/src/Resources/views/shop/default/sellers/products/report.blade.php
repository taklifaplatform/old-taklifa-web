@inject ('productFlagReasonRepository', 'Webkul\Marketplace\Repositories\ProductFlagReasonRepository')
@inject ('marketplaceProductRepository', 'Webkul\Marketplace\Repositories\ProductRepository')

@php
    $userLogin = auth()->guard('customer');

    // Return, if product flag is disallow and guest user not allow.
    if ((! $userLogin->check()
        && empty(core()->getConfigData('marketplace.settings.general.guest_can_product_flag'))
        )
        || empty(core()->getConfigData('marketplace.settings.general.enable_product_flag'))
    ) {
        return;
    }

    $isSellerProduct = $marketplaceProductRepository->where('product_id', $product->id)
        ->where('is_owner', 1)
        ->exists();

    // Return for admin product.
    if (! $isSellerProduct
        && empty($seller_id)    
    ) {
        return;
    }

    $flagReasons = $productFlagReasonRepository->findWhere([
        'status' => 1
    ]);

    $user = $userLogin->user();
@endphp

<!-- Product Report Vue Component -->
<v-product-report seller-id="{{ $seller_id ?? null }}" />

@pushOnce('scripts')
    <script type="text/x-template" id="v-product-report-template">
            <div class="mt-0 flex flex-wrap gap-3 sm:mt-3 md:gap-10">
                <span
                    class="flex cursor-pointer items-center gap-2.5"
                    @click="$refs.reportModal.open()"
                >
                    <span class="mp-issue-icon text-2xl"></span>
    
                    <span class="text-lg font-medium text-navyBlue">
                        @lang('marketplace::app.shop.products.report.report-product')
                    </span>
                </span>
            </div>
    
            <!-- Report Seller Form -->
            <x-shop::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    @submit="handleSubmit($event, reportProduct)"
                    ref="reportForm"
                >
                    <!-- Report Seller Modal -->
                    <x-marketplace::shop.modal ref="reportModal">
                        <x-slot:header>
                            <!-- Modal Header -->
                            <p class="text-2xl font-medium leading-9 text-[#151515]">
                                @lang('marketplace::app.shop.products.report.report-product')
                            </p>
                        </x-slot:header>
            
                        <!-- Modal Content -->
                        <x-slot:content class="!pb-2">
                            {!! view_render_event('bagisto.shop.marketplace.seller.report.create_form_controls.before', ['product' => $product, 'sellerId' => $seller_id ?? null]) !!}
                            
                            <x-shop::form.control-group class="w-full">
                                <x-shop::form.control-group.label class="required flex">
                                    @lang('marketplace::app.shop.products.report.name')
                                </x-shop::form.control-group.label>
    
                                <x-shop::form.control-group.control
                                    type="text"
                                    name="name"
                                    class="! shadow-none"
                                    rules="required"
                                    value="{{ $user?->name }}"
                                    :label="trans('marketplace::app.shop.products.report.name')"
                                    :placeholder="trans('marketplace::app.shop.products.report.name')"
                                />
    
                                <x-shop::form.control-group.error
                                    control-name="name"
                                    class="flex"
                                />
                            </x-shop::form.control-group>
    
                            <x-shop::form.control-group class="w-full">
                                <x-shop::form.control-group.label class="required flex">
                                    @lang('marketplace::app.shop.products.report.email')
                                </x-shop::form.control-group.label>
    
                                <x-shop::form.control-group.control
                                    type="email"
                                    name="email"
                                    class="! shadow-none"
                                    rules="required|email"
                                    value="{{ $user?->email }}"
                                    :label="trans('marketplace::app.shop.products.report.email')"
                                    :placeholder="trans('marketplace::app.shop.products.report.email')"
                                />
    
                                <x-shop::form.control-group.error
                                    control-name="email"
                                    class="flex"
                                />
                            </x-shop::form.control-group>
    
                            <!-- Reason -->
                            <x-shop::form.control-group class="w-full">
                                <x-shop::form.control-group.label class="required flex">
                                    @lang('marketplace::app.shop.products.report.reason')
                                </x-shop::form.control-group.label>
    
                                <x-shop::form.control-group.control
                                    type="{{ empty(core()->getConfigData('marketplace.settings.general.custom_product_flag_reason')) ? 'select' : 'textarea' }}"
                                    name="reason"
                                    class="! shadow-none"
                                    ::rules="{required: true, {{ empty(core()->getConfigData('marketplace.settings.general.custom_product_flag_reason')) ? '' : 'max: 120' }}}"
                                    :label="trans('marketplace::app.shop.products.report.reason')"
                                    :placeholder="trans('marketplace::app.shop.products.report.reason')"
                                >
                                    @if (empty(core()->getConfigData('marketplace.settings.general.custom_product_flag_reason'))) 
                                        @foreach ($flagReasons as $reason)
                                            <option value="{{ $reason->reason }}">
                                                {{$reason->reason}}
                                            </option>
                                        @endforeach
                                    @endif
                                </x-shop::form.control-group.control>
    
                                <x-shop::form.control-group.error
                                    control-name="reason"
                                    class="flex"
                                />
                            </x-shop::form.control-group>

                            {!! view_render_event('bagisto.shop.marketplace.seller.report.create_form_controls.after', ['product' => $product, 'sellerId' => $seller_id ?? null]) !!}
                        </x-slot::content>
    
                        <x-slot:footer>
                            <div class="flex justify-end pb-4">
                                <button
                                    type="submit"
                                    class="w-1/2 rounded-2xl bg-navyBlue px-7 py-4 text-center text-base text-white"
                                >
                                    @lang('marketplace::app.shop.products.report.submit')
                                </button>
                            </div>
                        </x-slot:footer>
                    </x-marketplace::shop.modal>
                </form>
            </x-shop::form>
    </script>

    <script type="module">
        app.component('v-product-report', {
            template: '#v-product-report-template',

            props: ['sellerId'],
            
            methods: {
                reportProduct(params, { resetForm, setErrors }) {
                    let formData = new FormData(this.$refs.reportForm);

                    formData.append("product_id", "{{ $product->id }}");

                    formData.append("seller_id", this.sellerId);

                    this.$axios.post("{{route('marketplace.product.flag.store')}}", formData)
                        .then((response) => {
                            this.$refs.reportForm.reset();
                            
                            this.$refs.reportModal.close();

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                        })
                        .catch(error => {
                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            }
                            
                            if (error.response.status == 400) {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            }
                        });
                },
            }
        });
    </script>
@endPushOnce