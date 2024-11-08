<!-- Panel -->
@if ($categories->count())
    <div class="box-shadow rounded-xl border bg-white p-4">
        <!-- Panel Header -->
        <p class="mb-4 flex justify-between text-base font-semibold text-gray-800">
            @lang('marketplace::app.shop.sellers.account.products.edit.categories.title')
        </p>

        <!-- Panel Content -->
        <div class="mb-5 text-sm text-gray-600">
            <x-marketplace::shop.tree.view
                name-field="categories"
                value-field="id"
                selection-type="individual"
                :items=$categories
                :value="json_encode($product->categories->pluck('id'))"
                :fallback-locale="config('app.fallback_locale')"
            >
            </x-marketplace::shop.tree.view>
        </div>
    </div>
@endif
