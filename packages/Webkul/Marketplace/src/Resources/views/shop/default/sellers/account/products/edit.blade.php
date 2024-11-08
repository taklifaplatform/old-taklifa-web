<x-marketplace::shop.layouts>
    <x-slot:title>
        @lang('marketplace::app.shop.sellers.account.products.edit.title')
    </x-slot:title>

    <!-- Breadcrumbs -->
    @section('breadcrumbs')
        <x-marketplace::shop.breadcrumbs name="seller_product_edit" />
    @endSection

    {!! view_render_event('marketplace.seller.products.edit.before', ['product', $product]) !!}

    @php
        $channels = core()->getAllChannels();

        $currentChannel = core()->getRequestedChannel();

        $currentLocale = core()->getRequestedLocale();
    @endphp

    <x-marketplace::shop.form
        action="{{route('marketplace.account.products.update', $product->id)}}"
        enctype="multipart/form-data"
    >
        @method('PUT')

        {!! view_render_event('marketplace.seller.products.edit.edit_form_controls.before', ['product', $product]) !!}

        <!-- Page Header -->
        <div class="grid gap-2.5">
            <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
                <div class="grid gap-1.5">
                    <p class="text-2xl font-medium leading-6">
                        @lang('marketplace::app.shop.sellers.account.products.edit.title')
                    </p>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <!-- Back Button -->
                    <a href="{{ route('shop.marketplace.seller.account.products.index') }}">
                        <button class="transparent-button rounded-xl px-5 py-2.5 font-semibold hover:bg-gray-200">
                            @lang('marketplace::app.shop.sellers.account.products.edit.back-btn')
                        </button>
                    </a>

                    <!-- Preview Button -->
                    @if (
                        $product->status
                        && $product->visible_individually
                        && $product->url_key
                    )
                        <a
                            href="{{ route('shop.product_or_category.index', $product->url_key) }}"
                            class="secondary-button font-semibold"
                            target="_blank"
                        >
                            @lang('marketplace::app.shop.sellers.account.products.edit.preview')
                        </a>
                    @endif
                    
                    <!-- Save Button -->
                    <button class="primary-button px-5 py-2.5">
                        @lang('marketplace::app.shop.sellers.account.products.edit.save-btn')
                    </button>
                </div>
            </div>
        </div>

        <!-- Channel and Locale Switcher -->
        <div class="flex items-center justify-between gap-4 max-md:flex-wrap mt-3.5">
            <div class="flex items-center gap-x-5">
                <!-- Channel Switcher -->
                <x-shop::dropdown
                    @class([
                        '[&>*]:!rounded-xl',
                        'hidden' => $channels->count() <= 1,
                    ])
                >
                    <!-- Dropdown Toggler -->
                    <x-slot:toggle>
                        <button
                            type="button"
                            class="flex items-center gap-x-2 p-2 hover:bg-gray-100 focus:bg-gray-100 rounded-xl"
                        >
                            <span class="mp-home-icon text-2xl"></span>
                            
                            {{ $currentChannel->name }}

                            <input
                                type="hidden"
                                name="channel"
                                value="{{ $currentChannel->code }}"
                            />

                            <span class="mp-sort-by-icon text-2xl text-gray-700"></span>
                        </button>
                    </x-slot>

                    <!-- Dropdown Content -->
                    <x-slot:content class="!p-1">
                        @foreach ($channels as $channel)
                            <a
                                href="?{{ Arr::query(['channel' => $channel->code, 'locale' => $currentLocale->code]) }}"
                                class="flex cursor-pointer gap-2.5 px-5 py-2 text-base hover:bg-gray-100 rounded-xl"
                            >
                                {{ $channel->name }}
                            </a>
                        @endforeach
                    </x-slot>
                </x-shop::dropdown>

                <!-- Locale Switcher -->
                <x-shop::dropdown
                    @class([
                        '[&>*]:!rounded-xl',
                        'hidden' => $currentChannel->locales->count() <= 1,
                    ])
                >
                    <!-- Dropdown Toggler -->
                    <x-slot:toggle>
                        <button
                            type="button"
                            class="flex items-center gap-x-2 p-2 hover:bg-gray-100 focus:bg-gray-100 rounded-xl"
                        >
                            <span class="mp-language-icon text-2xl"></span>

                            {{ $currentLocale->name }}
                            
                            <input
                                type="hidden"
                                name="locale"
                                value="{{ $currentLocale->code }}"
                            />

                            <span class="mp-sort-by-icon text-2xl"></span>
                        </button>
                    </x-slot>

                    <!-- Dropdown Content -->
                    <x-slot:content class="!p-1">
                        @foreach ($currentChannel->locales->sortBy('name') as $locale)
                            <a
                                href="?{{ Arr::query(['channel' => $currentChannel->code, 'locale' => $locale->code]) }}"
                                class="flex gap-2.5 px-5 py-2 text-base cursor-pointer hover:bg-gray-100 rounded-xl"
                            >
                                {{ $locale->name }}
                            </a>
                        @endforeach
                    </x-slot>
                </x-shop::dropdown>
            </div>
        </div>

        <div class="mt-3.5 flex gap-8 max-xl:flex-wrap">
            @foreach ($product->attribute_family->attribute_groups->groupBy('column') as $column => $groups)
                <div
                    @if ($column == 1) class="flex flex-1 flex-col gap-8 max-xl:flex-auto" @endif
                    @if ($column == 2) class="flex w-[360px] max-w-full flex-col gap-8 max-sm:w-full" @endif
                >
                    @foreach ($groups as $group)
                        @php
                            $customAttributes = $product->getEditableAttributes($group);
                        @endphp

                        @if (count($customAttributes))
                            <div class="box-shadow relative rounded-xl border bg-white p-5">
                                <p class="mb-4 text-base font-semibold text-gray-800">
                                    {{ $group->name }}
                                </p>

                                @foreach ($customAttributes as $attribute)
                                    @php
                                        if (
                                            ! $sellerProduct->is_approved
                                            && $attribute->code == 'status'
                                        ) {
                                            continue;
                                        }
                                    @endphp
                                    
                                    <x-marketplace::shop.form.control-group>
                                        <x-marketplace::shop.form.control-group.label class="!mt-5">
                                            {{ $attribute->admin_name.($attribute->is_required ? '*' : '') }}
                                        </x-marketplace::shop.form.control-group.label>

                                        @include ('marketplace::shop.sellers.account.products.edit.controls', [
                                            'attribute' => $attribute,
                                            'product'   => $product,
                                        ])
                                         
                                        <x-marketplace::shop.form.control-group.error :control-name="$attribute->code" />
                                    </x-marketplace::shop.form.control-group>
                                @endforeach

                                @includeWhen($group->code == 'price', 'marketplace::shop.sellers.account.products.edit.price.group')

                                @includeWhen(
                                    $group->code == 'inventories' && ! $product->getTypeInstance()->isComposite(),
                                    'marketplace::shop.sellers.account.products.edit.inventories'
                                )
                            </div>
                        @endif
                    @endforeach

                    @if ($column == 1)
                        <!-- Images View Blade File -->
                        @include('marketplace::shop.sellers.account.products.edit.images')

                        <!-- Videos View Blade File -->
                        @include('marketplace::shop.sellers.account.products.edit.videos')

                        <!-- Product Type View Blade File -->
                        @includeIf('marketplace::shop.sellers.account.products.edit.types.'.$product->type)

                        <!-- Include Product Type Additional Blade Files If Any -->
                        @foreach ($product->getTypeInstance()->getAdditionalViews() as $view)
                            @includeIf($view)
                        @endforeach
                    @else
                        <!-- Categories View Blade File -->
                        @include('marketplace::shop.sellers.account.products.edit.categories')

                        @include('marketplace::shop.sellers.account.products.edit.channels')
                    @endif
                </div>
            @endforeach
        </div>

        {!! view_render_event('marketplace.seller.products.edit.edit_form_controls.after', ['product', $product]) !!}
    </x-marketplace::shop.form>   
    
    {!! view_render_event('marketplace.seller.products.edit.after', ['product', $product]) !!}
</x-marketplace::shop.layouts>