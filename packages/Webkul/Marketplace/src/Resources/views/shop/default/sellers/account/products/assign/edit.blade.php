<x-marketplace::shop.layouts>
    @php
        $allowInventory = ['configurable', 'bundle', 'downloadable'];

        $allowPrice = ['configurable', 'bundle'];

        $inventorySources = core()->getCurrentChannel()->inventory_sources;

        $baseProduct = $product->product;
    @endphp

    <!-- Title of the page -->
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.products.assign.title')
    </x-slot:title>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_product_edit" />
    @endSection

    {!! view_render_event('marketplace.seller.products.assign.edit.before', ['product', $product]) !!}

    <!-- Assign Product Form -->
    <x-marketplace::shop.form
        :action="route('marketplace.account.products.assign.update', $product->id)"
        enctype="multipart/form-data"
    >
        @method('PUT')

        {!! view_render_event('marketplace.seller.products.assign.edit.edit_form_controls.before', ['product', $product]) !!}
        
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <h1 class="text-2xl font-medium">
                @lang('marketplace::app.shop.sellers.account.products.assign.title')
            </h1>

            <div class="flex items-center gap-x-2.5">
                <!-- Save Button -->
                <button class="primary-button px-5 py-2.5">
                    @lang('marketplace::app.shop.sellers.account.products.assign.save-btn')
                </button>
            </div>
        </div>

        <!-- Full Pannel -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left Section -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <div class="rounded border p-5">
                    <x-marketplace::shop.form.control-group.control
                        type="hidden"
                        name="product_type"
                        value="{{$baseProduct->type}}"
                    />
                    
                    <div class="flex gap-4 max-sm:flex-wrap">
                        <!-- Condition -->
                        <x-marketplace::shop.form.control-group class="mb-2.5 w-full">
                            <x-marketplace::shop.form.control-group.label class="required">
                                @lang('marketplace::app.shop.sellers.account.products.assign.condition')
                            </x-marketplace::shop.form.control-group.label>

                            <x-marketplace::shop.form.control-group.control
                                type="select"
                                name="condition"
                                id="condition"
                                :value="old('condition') ?: $product->condition"
                                rules="required"
                            >
                                @foreach (['new', 'old'] as $type)
                                    <option value="{{ $type }}">
                                        @lang('marketplace::app.shop.sellers.account.products.assign.'.$type)
                                    </option>
                                @endforeach
                            </x-marketplace::shop.form.control-group.control>

                            <x-marketplace::shop.form.control-group.error control-name="condition" />
                        </x-marketplace::shop.form.control-group>

                        @if (! in_array($baseProduct->type, $allowPrice))
                        <!-- Price -->
                        <x-marketplace::shop.form.control-group class="mb-2.5 w-full">
                            <x-marketplace::shop.form.control-group.label class="required">
                                @lang('marketplace::app.shop.sellers.account.products.assign.price')
                            </x-marketplace::shop.form.control-group.label>

                            <x-marketplace::shop.form.control-group.control
                                type="text"
                                name="price"
                                :value="old('price') ?: $product->price"
                                rules="required"
                                :label="trans('marketplace::app.shop.sellers.account.products.assign.price')"
                                :placeholder="trans('marketplace::app.shop.sellers.account.products.assign.price')"
                            />

                            <x-marketplace::shop.form.control-group.error control-name="price" />
                        </x-marketplace::shop.form.control-group>
                        @endif
                    </div>

                    @if (! in_array($baseProduct->type, $allowInventory)
                        && $baseProduct->type != 'downloadable'
                    )
                        <p class="mb-2.5 font-semibold text-gray-800">
                            @lang('marketplace::app.shop.sellers.account.products.assign.quantities')
                        </p>

                        @foreach ($inventorySources as $inventorySource)
                            @php
                                $qty = 0;
                                
                                foreach ($baseProduct->inventories as $inventory) {
                                    if ($inventory->inventory_source_id == $inventorySource->id
                                        && $inventory->vendor_id == $product->marketplace_seller_id
                                    ) {
                                        $qty = $inventory->qty;
                                        break;
                                    }
                                }

                                $qty = old('inventories['.$inventorySource->id.']') ?: $qty;
                            @endphp

                            <x-marketplace::shop.form.control-group class="mb-2.5">
                                <x-marketplace::shop.form.control-group.label>
                                    {{$inventorySource->name}}
                                </x-marketplace::shop.form.control-group.label>

                                <x-marketplace::shop.form.control-group.control
                                    type="text"
                                    :name="'inventories['.$inventorySource->id.']'"
                                    :rules="'numeric|min:0'"
                                    :label="$inventorySource->name"
                                    :value="$qty"
                                />

                                <x-marketplace::shop.form.control-group.error :control-name="'inventories['.$inventorySource->id.']'" />
                            </x-marketplace::shop.form.control-group>
                        @endforeach
                    @endif

                    <!-- Description -->
                    <x-marketplace::shop.form.control-group class="mb-2.5">
                        <x-marketplace::shop.form.control-group.label class="required">
                            @lang('marketplace::app.shop.sellers.account.products.assign.description')
                        </x-marketplace::shop.form.control-group.label>

                        <x-marketplace::shop.form.control-group.control
                            type="textarea"
                            name="description"
                            :value="old('description') ?: $product->description"
                            id="content"
                            rules="required"
                            :label="trans('marketplace::app.shop.sellers.account.products.assign.description')"
                            :placeholder="trans('marketplace::app.shop.sellers.account.products.assign.description')"
                            :tinymce="true"
                        />

                        <x-marketplace::shop.form.control-group.error control-name="description" />
                    </x-marketplace::shop.form.control-group>

                    @include('marketplace::shop.sellers.account.products.edit.images')

                    @include('marketplace::shop.sellers.account.products.edit.videos')

                    @includeWhen($baseProduct->type == 'configurable', 'marketplace::shop.sellers.account.products.assign.types.configurable')

                    @includeWhen($baseProduct->type == 'downloadable', 'marketplace::shop.sellers.account.products.assign.types.downloadable')
                </div>
            </div>

            <!-- Right Section -->
            <div class="flex w-[360px] max-w-full flex-col gap-2">
                <!-- Profile Information -->
                <div class="rounded border p-5">
                    <p class="mb-4 text-base font-semibold text-gray-800">
                        @lang('marketplace::app.shop.sellers.account.products.assign.product-details')
                    </p>

                    <!-- Product Information -->
                    <div class="grid max-w-72 content-start gap-2.5">
                        <p class="text-base">{{$baseProduct->name}}</p>

                        <div class="flex gap-2.5 text-lg font-semibold">
                            {!! $baseProduct->getTypeInstance()->getPriceHtml() !!}
                        </div>

                        <div
                            class="w-full h-[120px] max-w-[120px] max-h-[120px] relative rounded overflow-hidden {{ empty($baseProduct?->images[0]) ? 'border border-dashed border-gray-300 rounded overflow-hidden' : '' }}"
                        >
                            @if (empty($baseProduct?->images[0]))
                                <img src="{{ bagisto_asset('images/product-placeholders/front.svg','marketplace') }}">
                            
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

        {!! view_render_event('marketplace.seller.products.assign.edit.edit_form_controls.after', ['product', $product]) !!}
    </x-marketplace::shop.form>

    {!! view_render_event('marketplace.seller.products.assign.edit.after', ['product', $product]) !!}
</x-marketplace::shop.layouts>