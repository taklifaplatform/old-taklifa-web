@switch($attribute->type)
    @case('text')
        <v-field
            type="text"
            name="{{ $attribute->code }}"
            :rules="{{ $attribute->validations }}"
            label="{{ $attribute->admin_name }}"
            value="{{ old($attribute->code) ?: $product[$attribute->code] }}"
            v-slot="{ field }"
        >
            <input
                type="text"
                name="{{ $attribute->code }}"
                id="{{ $attribute->code }}"
                v-bind="field"
                :class="[errors['{{ $attribute->code }}'] ? 'border border-red-600 hover:border-red-600' : '']"
                class="mb-3 flex min-h-10 w-full rounded-md border-2 px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400"
                @if ($attribute->code == 'url_key') v-slugify @endif
                @if ($attribute->code == 'name') v-slugify-target:url_key="setValues" @endif
            >
        </v-field>

        @break

    @case('price')
        <div class="relative">
            <x-marketplace::shop.form.control-group.control
                type="price"
                :name="$attribute->code"
                :id="$attribute->code"
                ::rules="{{ $attribute->validations }}"
                :label="$attribute->admin_name"
                value="{{ old($attribute->code) ?: $product[$attribute->code] }}"
                :class="($attribute->code == 'price' ? 'py-2.5 bg-gray-50 text-xl font-bold' : '')"
            >
                <x-slot:currency :class="'' . ($attribute->code == 'price' ? 'bg-gray-50 text-xl' : '')">
                    {{ core()->currencySymbol(core()->getBaseCurrencyCode()) }}
                </x-slot>
            </x-marketplace::shop.form.control-group.control>
        </div>
    @break

    @case('textarea')
        <x-marketplace::shop.form.control-group.control
            type="textarea"
            :name="$attribute->code"
            :id="$attribute->code"
            ::rules="{{ $attribute->validations }}"
            :label="$attribute->admin_name"
            value="{{ old($attribute->code) ?: $product[$attribute->code] }}"
            :tinymce="(bool) $attribute->enable_wysiwyg"
        />

        @break

    @case('date')
        <x-marketplace::shop.form.control-group.control
            type="date"
            :name="$attribute->code"
            :id="$attribute->code"
            ::rules="{{ $attribute->validations }}"
            :label="$attribute->admin_name"
            value="{{ old($attribute->code) ?: $product[$attribute->code] }}"
        />

        @break

    @case('datetime')
        <x-marketplace::shop.form.control-group.control
            type="datetime"
            :name="$attribute->code"
            ::rules="{{ $attribute->validations }}"
            :label="$attribute->admin_name"
            value="{{ old($attribute->code) ?: $product[$attribute->code] }}"
        />

        @break

    @case('select')
        <x-marketplace::shop.form.control-group.control
            type="select"
            :name="$attribute->code"
            :id="$attribute->code"
            ::rules="{{ $attribute->validations }}"
            :label="$attribute->admin_name"
            :value="old($attribute->code) ?: $product[$attribute->code]"
        >
            @php
                $selectedOption = old($attribute->code) ?: $product[$attribute->code];

                if ($attribute->code != 'tax_category_id') {
                    $options = $attribute->options()->orderBy('sort_order')->get();
                } else {
                    $options = app('Webkul\Tax\Repositories\TaxCategoryRepository')->all();
                }
            @endphp

            @foreach ($options as $option)
                <option
                    value="{{ $option->id }}"
                    {{ $selectedOption == $option->id ? 'selected' : '' }}
                >
                    {{ $option->admin_name ?? $option->name }}
                </option>
            @endforeach
        </x-marketplace::shop.form.control-group.control>

        @break

    @case('multiselect')
        <x-marketplace::shop.form.control-group.control
            type="multiselect"
            :name="$attribute->code.'[]'"
            :id="$attribute->code.'[]'"
            ::rules="{{ $attribute->validations }}"
            :label="$attribute->admin_name"
        >
            @php
                $selectedOption = old($attribute->code) ?: explode(',', $product[$attribute->code]);
            @endphp

            @foreach ($attribute->options()->orderBy('sort_order')->get() as $option)
                <option
                    value="{{ $option->id }}"
                    {{ in_array($option->id, $selectedOption) ? 'selected' : ''}}
                >
                    {{ $option->admin_name }}
                </option>
            @endforeach
        </x-marketplace::shop.form.control-group.control>

        @break

    @case('checkbox')
        @php
            $selectedOption = old($attribute->code) ?: explode(',', $product[$attribute->code]);
        @endphp

        @foreach ($attribute->options as $option)
            <div class="flex items-center gap-2.5 py-1.5">
                <x-marketplace::shop.form.control-group.control
                    type="checkbox"
                    :name="$attribute->code.'[]'"
                    :value="$option->id"
                    :id="$attribute->code.'_'.$option->id"
                    :for="$attribute->code.'_'.$option->id"
                    ::rules="{{ $attribute->validations }}"
                    :label="$attribute->admin_name"
                    :checked="in_array($option->id, $selectedOption)"
                >
                </x-marketplace::shop.form.control-group.control>

                <p class="font-semibold text-gray-600">
                    {{ $option->admin_name }}
                </p>
            </div>
        @endforeach

        @break

    @case('boolean')
        @php $selectedValue = old($attribute->code) ?: $product[$attribute->code] @endphp

        <x-marketplace::shop.form.control-group.control
            type="switch"
            :name="$attribute->code"
            :id="$attribute->code"
            :value="1"
            :label="$attribute->admin_name"
            :checked="(boolean) $selectedValue"
        />

        @break

    @case('image')
    @case('file')
        <div class="flex gap-2.5">
            @if ($product[$attribute->code])
                <a
                    href="{{ route('admin.catalog.products.file.download', [$product->id, $attribute->id] )}}"
                    class="flex"
                >
                    @if ($attribute->type == 'image')
                        <img
                            src="{{ Storage::url($product[$attribute->code]) }}"
                            class="h-[45px] w-[45px] overflow-hidden rounded border hover:border-gray-400"
                        />
                    @else
                        <div class="inline-flex w-full max-w-max cursor-pointer appearance-none items-center justify-between gap-x-1 rounded-md border border-transparent p-1.5 text-center text-gray-600 transition-all marker:shadow hover:bg-gray-200 active:border-gray-300">
                            <i class="icon-down-stat text-2xl"></i>
                        </div>
                    @endif
                </a>

                <input type="hidden" name="{{ $attribute->code }}" value="{{ $product[$attribute->code] }}"/>
            @endif

            <v-field
                type="text"
                class="w-full"
                name="{{ $attribute->code }}"
                :rules="{{ $attribute->validations }}"
                label="{{ $attribute->admin_name }}"
                v-slot="{ handleChange, handleBlur }"
                value="{{ old($attribute->code) ?: $product[$attribute->code] }}"
            >
                <input
                    type="file"
                    name="{{ $attribute->code }}"
                    id="{{ $attribute->code }}"
                    :class="[errors['{{ $attribute->code }}'] ? 'border border-red-600 hover:border-red-600' : '']"
                    class="flex min-h-10 w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400"
                    @change="handleChange"
                    @blur="handleBlur"
                >
            </v-field>
        </div>

        @if ($product[$attribute->code])
            <div class="flex items-center gap-2.5">
                <x-marketplace::shop.form.control-group.control
                    type="checkbox"
                    :name="$attribute->code.'[delete]'"
                    value="1"
                    :id="$attribute->code.'_delete'"
                    :for="$attribute->code.'_delete'"
                />

                <p class="text-sm text-gray-600">
                    @lang('admin::app.catalog.products.edit.remove')
                </p>
            </div>
        @endif

        @break

@endswitch
