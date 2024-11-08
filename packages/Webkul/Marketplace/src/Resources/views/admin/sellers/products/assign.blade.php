<x-admin::layouts>
    @php
        $allowInventory = ['configurable', 'bundle', 'downloadable', 'booking'];

        $allowPrice = ['configurable', 'bundle'];

        $inventorySources = core()->getCurrentChannel()->inventory_sources;

        $sellerId = request()->route()->seller_id;

        $productId = request()->route()->product_id;
    @endphp

    <!-- Title of the page -->
    <x-slot:title>
        @lang('marketplace::app.admin.sellers.assign-product.title')
    </x-slot:title>

    <!-- Assign Product Form -->
    <x-admin::form
        :action="route('admin.marketplace.sellers.products.save_assign', [
            'seller_id'  => $sellerId,
            'product_id' => $productId,            
        ])"
        enctype="multipart/form-data"
    >
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="py-2.5 text-xl font-bold text-gray-800 dark:text-white">
                @lang('marketplace::app.admin.sellers.assign-product.title')
            </p>

            <div class="flex items-center gap-x-2.5">
                <!-- Cancel Button -->
                <a
                    href="{{ route('admin.marketplace.sellers.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('marketplace::app.admin.sellers.assign-product.back-btn')
                </a>

                <!-- Save Button -->
                <button class="primary-button">
                    @lang('marketplace::app.admin.sellers.assign-product.save-btn')
                </button>
            </div>
        </div>

        <!-- Full Pannel -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left Section -->
            <div class="flex flex-col flex-1 gap-2 max-xl:flex-auto">
                <div class="p-4 bg-white rounded box-shadow dark:bg-gray-900">
                    <x-admin::form.control-group.control
                        type="hidden"
                        name="product_type"
                        value="{{$baseProduct->type}}"
                    />

                    <div class="flex gap-4 max-sm:flex-wrap">
                        <!-- Condition -->
                        <x-admin::form.control-group class="mb-2.5 w-full">
                            <x-admin::form.control-group.label class="required">
                                @lang('marketplace::app.admin.sellers.assign-product.condition')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                name="condition"
                                id="condition"
                                :value="old('condition')"
                                rules="required"
                            >
                                <option value="">
                                    @lang('marketplace::app.admin.sellers.assign-product.select-condition')
                                </option>

                                @foreach (['new', 'old'] as $type)
                                    <option value="{{ $type }}">
                                        @lang('marketplace::app.admin.sellers.assign-product.'.$type)
                                    </option>
                                @endforeach
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="condition" />
                        </x-admin::form.control-group>

                        @if (! in_array($baseProduct->type, $allowPrice))
                            <!-- Price -->
                            <x-admin::form.control-group class="mb-2.5 w-full">
                                <x-admin::form.control-group.label class="required">
                                    @lang('marketplace::app.admin.sellers.assign-product.price')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="price"
                                    :value="old('price')"
                                    rules="required"
                                    :label="trans('marketplace::app.admin.sellers.assign-product.price')"
                                    :placeholder="trans('marketplace::app.admin.sellers.assign-product.price')"
                                />

                                <x-admin::form.control-group.error control-name="price" />
                            </x-admin::form.control-group>
                        @endif
                    </div>

                    @if (! in_array($baseProduct->type, $allowInventory)
                        && $baseProduct->type != 'downloadable'
                    )
                        <p class="mb-2.5 font-semibold text-gray-800 dark:text-white">
                            @lang('marketplace::app.admin.sellers.assign-product.quantities')
                        </p>

                        @foreach ($inventorySources as $inventorySource)
                            <x-admin::form.control-group class="mb-2.5">
                                <x-admin::form.control-group.label>
                                    {{$inventorySource->name}}
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="inventories[{{ $inventorySource->id }}]"
                                    :value="old('inventories[{{ $inventorySource->id }}]')"
                                    rules="numeric|min:0"
                                    :placeholder="$inventorySource->name"
                                />
                                <x-admin::form.control-group.error control-name="inventories[{{ $inventorySource->id }}]" />
                            </x-admin::form.control-group>
                        @endforeach
                    @endif

                    <!-- Description -->
                    <x-admin::form.control-group class="mb-2.5">
                        <x-admin::form.control-group.label class="required">
                            @lang('marketplace::app.admin.sellers.assign-product.description')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            name="description"
                            :value="old('description')"
                            id="content"
                            rules="required"
                            :label="trans('marketplace::app.admin.sellers.assign-product.description')"
                            :placeholder="trans('marketplace::app.admin.sellers.assign-product.description')"
                        />
                        <x-admin::form.control-group.error control-name="description" />
                    </x-admin::form.control-group>

                    <div class="flex justify-between gap-5 mb-4">
                        <div class="flex flex-col gap-2">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                @lang('marketplace::app.admin.sellers.assign-product.images.title')
                            </p>
                
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-300">
                                @lang('marketplace::app.admin.sellers.assign-product.images.info')
                            </p>
                        </div>
                    </div>
                
                    <!-- Image Blade Component -->
                    <x-admin::media.images
                        name="images[files]"
                        allow-multiple="true"
                        show-placeholders="true"
                    />

                    <div class="flex justify-between gap-5 py-4">
                        <div class="flex flex-col gap-2">
                            <p class="text-base font-semibold text-gray-800 dark:text-white">
                                @lang('marketplace::app.admin.sellers.assign-product.videos.title')
                            </p>
                
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-300">
                                @lang('marketplace::app.admin.sellers.assign-product.videos.info', ['size' => core()->getMaxUploadSize()])
                            </p>
                        </div>
                    </div>

                    <!-- Video Blade Component -->
                    <x-admin::media.videos
                        name="videos[files]"
                        allow-multiple="true"
                        show-placeholders="true"
                    />

                    <x-admin::form.control-group.error control-name='videos.files[0]' />

                    @includeWhen($baseProduct->type == 'configurable', 'marketplace::admin.sellers.products.assign.configurable')

                    @includeWhen($baseProduct->type == 'downloadable', 'marketplace::admin.sellers.products.assign.downloadable', ['product' => $baseProduct])
                </div>
            </div>

            <!-- Right Section -->
            <div class="flex w-[360px] max-w-full flex-col gap-2">
                <!-- Profile Information -->
                <div class="p-4 bg-white rounded box-shadow dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('marketplace::app.admin.sellers.assign-product.product-details')
                    </p>

                    <!-- Product Information -->
                    <div class="grid max-w-72 content-start gap-2.5">
                        <p class="text-base dark:text-white">{{$baseProduct->name}}</p>

                        <div class="flex gap-2.5 font-semibold text-lg [&>*]:dark:text-white">
                            {!! $baseProduct->getTypeInstance()->getPriceHtml() !!}
                        </div>

                        <div
                            class="w-full h-[120px] max-w-[120px] max-h-[120px] relative rounded overflow-hidden {{ empty($baseProduct?->images[0]) ? 'border border-dashed border-gray-300 dark:border-gray-800 rounded dark:invert dark:mix-blend-exclusion overflow-hidden' : '' }}"
                        >
                            @if (empty($baseProduct?->images[0]))
                                <img src="{{ bagisto_asset('images/product-placeholders/front.svg') }}">
                            
                                <p class="absolute bottom-1 w-full text-center text-[6px] font-semibold text-gray-400">
                                    @lang('admin::app.catalog.products.edit.types.grouped.image-placeholder')
                                </p>
                            @else
                                <img src={{ Storage::url($baseProduct?->images[0]->path) }}>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
