<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        @lang('marketplace::app.admin.sellers.edit.title')
    </x-slot:title>

    {!! view_render_event('marketplace.admin.seller.edit.before', ['seller' => $seller]) !!}
    
    <!-- Seller Profile Edit Form -->
    <v-seller></v-seller>

    {!! view_render_event('marketplace.admin.seller.edit.after', ['seller' => $seller]) !!}

    {!! view_render_event('marketplace.admin.seller.edit.seller_reviews.list.before', ['seller' => $seller]) !!}

    <x-admin::datagrid :src="route('admin.marketplace.sellers.edit', $seller->seller_id)" />

    {!! view_render_event('marketplace.admin.seller.edit.seller_reviews.list.after', ['seller' => $seller]) !!}

    @pushOnce('scripts')
        <script
            type="text/x-template" 
            id="v-seller-template"
        >
            <x-admin::form
                as="div"
                v-slot="{ meta, errors, handleSubmit }"
            >
                <form
                    @submit="handleSubmit($event, updateSeller)"
                    ref="sellerForm"
                >
                    @method('PUT')

                    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
                        <div class="flex gap-3">
                            <p class="text-xl font-bold text-gray-800 dark:text-white">
                                @lang('marketplace::app.admin.sellers.edit.title')
                            </p>

                            @if ($seller->is_suspended)
                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                >
                                    <label class="label-canceled py-1">{{ trans('marketplace::app.admin.sellers.edit.suspended') }}</label>
                                </p>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-x-2.5">
                            <!-- Cancel Button -->
                            <a
                                href="{{route('admin.marketplace.sellers.index')}}"
                                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                            >
                                @lang('marketplace::app.admin.sellers.edit.back-btn')
                            </a>

                            <!-- Update Button -->
                            <button type="submit" class="primary-button">
                                @lang('marketplace::app.admin.sellers.edit.update-btn')
                            </button>
                        </div>
                    </div>

                    <!-- Full Pannel -->
                    <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                        <!-- Left Section -->
                        <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                            <!-- Shop Information -->
                            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('marketplace::app.admin.sellers.edit.shop.title')
                                </p>

                                <div class="flex gap-4 max-sm:flex-wrap">
                                    <!-- Shop Title -->
                                    <x-admin::form.control-group class="mb-2.5 w-full">
                                        <x-admin::form.control-group.label class="required">
                                            @lang('marketplace::app.admin.sellers.edit.shop.shop-title')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="shop_title"
                                            :value="old('shop_title') ?: $seller->shop_title"
                                            rules="required"
                                            :label="trans('marketplace::app.admin.sellers.edit.shop.shop-title')"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.shop.shop-title')"
                                        />

                                        <x-admin::form.control-group.error control-name="shop_title" />
                                    </x-admin::form.control-group>

                                    <!-- Shop URL -->
                                    <x-admin::form.control-group class="mb-2.5 w-full">
                                        <x-admin::form.control-group.label class="required">
                                            @lang('marketplace::app.admin.sellers.edit.shop.url')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="url"
                                            :value="old('url') ?: $seller->shop_url"
                                            rules="required"
                                            :label="trans('marketplace::app.admin.sellers.edit.shop.url')"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.shop.url')"
                                        />

                                        <x-admin::form.control-group.error control-name="url" />
                                    </x-admin::form.control-group>
                                </div>

                                <div class="flex gap-4 max-sm:flex-wrap">
                                    <!-- Name -->
                                    <x-admin::form.control-group class="mb-2.5 w-full">
                                        <x-admin::form.control-group.label class="required">
                                            @lang('marketplace::app.admin.sellers.edit.shop.name')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="name"
                                            rules="required"
                                            :value="old('name') ?: $seller->name"
                                            :label="trans('marketplace::app.admin.sellers.edit.shop.name')"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.shop.name')"
                                        />

                                        <x-admin::form.control-group.error control-name="name" />
                                    </x-admin::form.control-group>

                                    <!-- Phone -->
                                    <x-admin::form.control-group class="mb-2.5 w-full">
                                        <x-admin::form.control-group.label class="required">
                                            @lang('marketplace::app.admin.sellers.edit.shop.phone-number')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="phone"
                                            :value="old('phone') ?: $seller->phone"
                                            rules="required|phone|min:10"
                                            :label="trans('marketplace::app.admin.sellers.edit.shop.phone-number')"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.shop.phone-number')"
                                        />

                                        <x-admin::form.control-group.error control-name="phone" />
                                    </x-admin::form.control-group>
                                </div>

                                <x-admin::form.control-group class="mb-2.5 w-full">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.edit.shop.email')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="email"
                                        rules="required"
                                        :value="old('email') ?: $seller->email"
                                        :label="trans('marketplace::app.admin.sellers.edit.shop.email')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.shop.email')"
                                    />

                                    <x-admin::form.control-group.error control-name="email" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group.label class="required">
                                    @lang('marketplace::app.admin.sellers.edit.shop.address')
                                </x-admin::form.control-group.label>

                                @php $addresses = explode(PHP_EOL, $seller->address); @endphp

                                @for ($i = 0; $i < (core()->getConfigData('marketplace.settings.general.street_lines') ?? 1); $i++)
                                    <x-admin::form.control-group class="mb-2.5">
                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="address[{{ $i }}]"
                                            :value="old('address.{{ $i }}') ?: $addresses[$i] ?? ''"
                                            rules="{{ $i ? '' : 'required' }}"
                                            :label="trans('marketplace::app.admin.sellers.edit.shop.street-address')"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.shop.street-address')"
                                        />
                                        
                                        <x-admin::form.control-group.error control-name="address[{{ $i }}]" />
                                    </x-admin::form.control-group>
                                @endfor

                                <div class="flex gap-4 max-sm:flex-wrap">
                                    <!-- Postcode -->
                                    <x-admin::form.control-group class="mb-2.5 w-full">
                                        <x-admin::form.control-group.label class="required">
                                            @lang('marketplace::app.admin.sellers.edit.shop.postcode')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="postcode"
                                            :value="old('postcode') ?: $seller->postcode"
                                            rules="required|integer"
                                            :label="trans('marketplace::app.admin.sellers.edit.shop.postcode')"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.shop.postcode')"
                                        />

                                        <x-admin::form.control-group.error control-name="postcode" />
                                    </x-admin::form.control-group>

                                    <!-- City -->
                                    <x-admin::form.control-group class="mb-2.5 w-full">
                                        <x-admin::form.control-group.label class="required">
                                            @lang('marketplace::app.admin.sellers.edit.shop.city')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="city"
                                            :value="old('city') ?: $seller->city"
                                            rules="required"
                                            :label="trans('marketplace::app.admin.sellers.edit.shop.city')"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.shop.city')"
                                        />

                                        <x-admin::form.control-group.error control-name="city" />
                                    </x-admin::form.control-group>
                                </div>

                                <div class="flex gap-4 max-sm:flex-wrap">
                                    <!-- Country -->
                                    <x-admin::form.control-group class="w-full">
                                        <x-admin::form.control-group.label>
                                            @lang('marketplace::app.admin.sellers.edit.shop.country')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="select"
                                            name="country"
                                            rules="required"
                                            ::value="country"
                                            v-model="country"
                                            :label="trans('marketplace::app.admin.sellers.edit.shop.country')"
                                        >
                                            <option value="">
                                                @lang('marketplace::app.admin.sellers.edit.shop.select')
                                            </option>

                                            @foreach (core()->countries() as $country)
                                                <option 
                                                    {{ $country->code === config('app.default_country') ? 'selected' : '' }}  
                                                    value="{{ $country->code }}"
                                                >
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error control-name="country" />
                                    </x-admin::form.control-group>

                                    <!-- State -->
                                    <x-admin::form.control-group class="w-full">
                                        <x-admin::form.control-group.label class="required">
                                            @lang('marketplace::app.admin.sellers.edit.shop.state')
                                        </x-admin::form.control-group.label>

                                        <template v-if="haveStates()">
                                            <x-admin::form.control-group.control
                                                type="select"
                                                id="state"
                                                name="state"
                                                rules="required"
                                                ::value="state"
                                                v-model="state"
                                                :label="trans('marketplace::app.admin.sellers.edit.shop.state')"
                                                :placeholder="trans('marketplace::app.admin.sellers.edit.shop.state')"
                                            >
                                                <option 
                                                    v-for='(state, index) in countryStates[country]'
                                                    :value="state.code"
                                                    v-text="state.default_name"
                                                >
                                                </option>
                                            </x-admin::form.control-group.control>
                                        </template>
    
                                        <template v-else>
                                            <x-admin::form.control-group.control
                                                type="text"
                                                name="state"
                                                v-model="state"
                                                rules="required"
                                                :label="trans('marketplace::app.admin.sellers.edit.shop.state')"
                                                :placeholder="trans('marketplace::app.admin.sellers.edit.shop.state')"
                                            />
                                        </template>

                                        <x-admin::form.control-group.error control-name="state" />
                                    </x-admin::form.control-group>
                                </div>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.edit.shop.description')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="description"
                                        :value="old('description') ?: $seller->description"
                                        id="content"
                                        rules="required"
                                        :label="trans('marketplace::app.admin.sellers.edit.shop.description')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.shop.description')"
                                        :tinymce="true"
                                    />

                                    <x-admin::form.control-group.error control-name="description" />
                                </x-admin::form.control-group>
                            </div>

                            <!-- Policy -->
                            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('marketplace::app.admin.sellers.edit.policy.title')
                                </p>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.policy.privacy')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="privacy_policy"
                                        id="privacy_policy"
                                        :value="old('privacy_policy') ?: $seller->privacy_policy"
                                        :label="trans('marketplace::app.admin.sellers.edit.policy.privacy')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.policy.privacy')"
                                        :tinymce="true"
                                    />

                                    <x-admin::form.control-group.error control-name="privacy_policy" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.policy.shipping')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="shipping_policy"
                                        id="shipping_policy"
                                        :value="old('privacy_policy') ?: $seller->privacy_policy"
                                        :label="trans('marketplace::app.admin.sellers.edit.policy.shipping')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.policy.shipping')"
                                        :tinymce="true"
                                    />

                                    <x-admin::form.control-group.error control-name="shipping_policy" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.policy.return')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="return_policy"
                                        id="return_policy"
                                        :value="old('return_policy') ?: $seller->return_policy"
                                        :label="trans('marketplace::app.admin.sellers.edit.policy.return')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.policy.return')"
                                        :tinymce="true"
                                    />

                                    <x-admin::form.control-group.error control-name="return_policy" />
                                </x-admin::form.control-group>
                            </div>
                        </div>

                        <!-- Right Section -->
                        <div class="flex w-[360px] max-w-full flex-col gap-2">
                            <!-- Profile Information -->
                            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('marketplace::app.admin.sellers.edit.profile.title')
                                </p>

                                <div class="flex gap-4 max-sm:flex-wrap">
                                    <div class="mb-2.5 w-full">
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label>
                                                @lang('marketplace::app.admin.sellers.edit.profile.logo')
                                            </x-admin::form.control-group.label>

                                            <x-admin::media.images
                                                name="logo"
                                                :uploaded-images="$seller->logo ? [['id' => 'logo', 'url' => Storage::url($seller->logo)]] : []"
                                            />
                                        </x-admin::form.control-group>

                                        <p class="text-xs text-gray-600 dark:text-gray-300">
                                            @lang('marketplace::app.admin.sellers.edit.profile.logo-size')
                                        </p>
                                    </div>

                                    <div class="mb-2.5 w-full">
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label>
                                                @lang('marketplace::app.admin.sellers.edit.profile.banner')
                                            </x-admin::form.control-group.label>

                                            <x-admin::media.images
                                                name="banner"
                                                :uploaded-images="$seller->banner ? [['id' => 'banner', 'url' => Storage::url($seller->banner)]] : []"
                                            />
                                        </x-admin::form.control-group>

                                        <p class="text-xs text-gray-600 dark:text-gray-300">
                                            @lang('marketplace::app.admin.sellers.edit.profile.banner-size')
                                        </p>
                                    </div>
                                </div>

                                @php
                                    $socialLinks = ['facebook', 'twitter', 'pinterest', 'linkedin']
                                @endphp

                                @foreach($socialLinks as $socialLink)
                                    <x-admin::form.control-group class="mb-2.5">
                                        <x-admin::form.control-group.label>
                                            @lang('marketplace::app.admin.sellers.edit.profile.social-links', ['name' => Str::title($socialLink)])
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            :name="$socialLink"
                                            :value="old($socialLink) ?: $seller->$socialLink"
                                            :label="trans('marketplace::app.admin.sellers.edit.profile.social-links', ['name' => $socialLink])"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.profile.social-links', ['name' => $socialLink])"
                                        />

                                        <x-admin::form.control-group.error :control-name="$socialLink" />
                                    </x-admin::form.control-group>
                                @endforeach
                            </div>

                            <!-- Meta Information -->
                            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('marketplace::app.admin.sellers.edit.meta-info.title')
                                </p>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.meta-info.meta-title')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="meta_title"
                                        id="meta_title"
                                        :value="old('meta_title') ?: $seller->meta_title"
                                        :label="trans('marketplace::app.admin.sellers.edit.meta-info.meta-title')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.meta-info.meta-title')"
                                    />

                                    <x-admin::form.control-group.error control-name="meta_title" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.meta-info.meta-keyword')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="meta_keywords"
                                        id="meta_keywords"
                                        :value="old('meta_keywords') ?: $seller->meta_keywords"
                                        :label="trans('marketplace::app.admin.sellers.edit.meta-info.meta-keyword')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.meta-info.meta-keyword')"
                                    />

                                    <x-admin::form.control-group.error control-name="meta_keywords" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.meta-info.meta-description')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="meta_description"
                                        id="meta_description"
                                        :value="old('meta_description') ?: $seller->meta_description"
                                        :label="trans('marketplace::app.admin.sellers.edit.meta-info.meta-description')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.meta-info.meta-description')"
                                    />

                                    <x-admin::form.control-group.error control-name="meta_description" />
                                </x-admin::form.control-group>
                            </div>

                            <!-- Allowed Products -->
                            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('marketplace::app.admin.sellers.edit.allowed-product.title')
                                </p>

                                @foreach (config('product_types') as $productType)
                                    <label
                                        class="flex w-max cursor-pointer select-none items-center gap-2.5 p-1.5"
                                        for="{{$productType['key']}}"
                                    >
                                        @php
                                           $checked = in_array($productType['key'], $seller->allowed_product_types ?? []);
                                        @endphp

                                        <x-admin::form.control-group.control
                                            type="checkbox"
                                            id="{{$productType['key']}}"
                                            for="{{$productType['key']}}"
                                            value="{{$productType['key']}}"
                                            name="allowed_product_types[]"
                                            :checked="$checked"
                                            label="@lang($productType['name'])"
                                        />

                                        <div class="cursor-pointer text-sm font-semibold text-gray-600 dark:text-gray-300">
                                            @lang($productType['name'])
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @if (core()->getConfigData('marketplace.settings.general.enable_minimum_order_amount'))
                                <!-- Minimum Order Amount -->
                                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                        @lang('marketplace::app.admin.sellers.edit.minimum-order-amount.title')
                                    </p>

                                    <x-admin::form.control-group class="mb-2.5">
                                        <x-admin::form.control-group.label>
                                            @lang('marketplace::app.admin.sellers.edit.minimum-order-amount.title')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="min_order_amount"
                                            :value="old('min_order_amount') ?: $seller->min_order_amount"
                                            :label="trans('marketplace::app.admin.sellers.edit.minimum-order-amount.title')"
                                            :placeholder="trans('marketplace::app.admin.sellers.edit.minimum-order-amount.title')"
                                        />

                                        <x-admin::form.control-group.error control-name="min_order_amount" />
                                    </x-admin::form.control-group>
                                </div>
                            @endif

                            <!-- Google Analytics Id -->
                            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('marketplace::app.admin.sellers.edit.google-analytics-id.title')
                                </p>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.google-analytics-id.title')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="google_analytics_id"
                                        :value="old('google_analytics_id') ?: $seller->google_analytics_id"
                                        :label="trans('marketplace::app.admin.sellers.edit.google-analytics-id.title')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.google-analytics-id.title')"
                                    />

                                    <x-admin::form.control-group.error control-name="google_analytics_id" />
                                </x-admin::form.control-group>                  
                            </div>
                             
                            <!-- Commission -->
                            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('marketplace::app.admin.sellers.edit.commission.title')
                                </p>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.commission.status')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="switch"
                                        name="commission_enable"
                                        :value="1"
                                        v-model="commission_enable"
                                        ::checked="commission_enable"
                                        @change="change()"
                                        :label="trans('marketplace::app.admin.sellers.edit.commission.status')"
                                    />
                                    
                                    <x-admin::form.control-group.error control-name="commission_enable" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('marketplace::app.admin.sellers.edit.commission.percentage')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="commission_percentage"
                                        ::rules="isRequired ? 'required|between:.5,99' : ''"
                                        v-model="commission_percentage"
                                        :label="trans('marketplace::app.admin.sellers.edit.commission.percentage')"
                                        :placeholder="trans('marketplace::app.admin.sellers.edit.commission.percentage')"
                                        ::disabled="isActive == false ? true : false"
                                    />

                                    <x-admin::form.control-group.error control-name="commission_percentage" />
                                </x-admin::form.control-group>
                            </div>

                            <!-- Suspended -->
                            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('marketplace::app.admin.sellers.edit.suspended')
                                </p>

                                <x-admin::form.control-group class="mb-2.5">
                                    <x-admin::form.control-group.label>
                                        @lang('marketplace::app.admin.sellers.edit.suspended')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="switch"
                                        name="is_suspended"
                                        :value="1"
                                        v-model="is_suspended"
                                        ::checked="is_suspended"
                                        :label="trans('marketplace::app.admin.sellers.edit.suspended')"
                                    />
                                    
                                    <x-admin::form.control-group.error control-name="is_suspended" />
                                </x-admin::form.control-group>
                            </div>
                        </div>
                    </div>
                </form>
            </x-admin::form>
        </script>

        <script type="module">
            app.component('v-seller', {
                template: '#v-seller-template',

                data() {
                    return {
                        sellerId: @json($seller->id),

                        country: @json(old('country') ?: $seller->country),

                        state: @json(old('state') ?: $seller->state),

                        countryStates: @json(core()->groupedStatesByCountries()),

                        commission_enable: @json($seller->commission_enable),

                        is_suspended: @json($seller->is_suspended),

                        commission_percentage: @json($seller->commission_percentage),

                        isActive: false,
                        
                        isRequired: false,
                    }
                },

                created() {
                    if (this.commission_enable) {
                        this.isActive = this.isRequired = true;
                    }
                },

                methods: {
                    change() {
                        this.isActive = ! this.isActive;
                        
                        this.isRequired = ! this.isRequired;
                    },

                    updateSeller(params, { resetForm, setErrors }) {
                        let formData = new FormData(this.$refs.sellerForm);
                        
                        this.$axios.post(`{{route('admin.marketplace.sellers.update', '')}}/${this.sellerId}`, formData)
                            .then((response) => {
                                window.location.href = response.data.redirect_url;
                            })
                            .catch(error => {
                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                    },

                    haveStates() {
                        return !!this.countryStates[this.country]?.length;
                    }
                }
            });
        </script>
    @endPushOnce
</x-admin::layouts>
