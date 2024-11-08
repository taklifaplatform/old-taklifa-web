<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        @lang('marketplace::app.admin.seller-categories.edit.title')
    </x-slot:title>

    {!! view_render_event('marketplace.admin.seller_category.edit.before', ['sellerCategory' => $sellerCategory]) !!}

    <!-- Category Edit Form -->
    <x-admin::form
        :action="route('admin.marketplace.seller_categories.update', $sellerCategory->id)"
        enctype="multipart/form-data"
    >
        @method('PUT')
        
        {!! view_render_event('marketplace.admin.seller_category.edit.create_form_controls.before') !!}

        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="py-2.5 text-xl font-bold text-gray-800 dark:text-white">
                @lang('marketplace::app.admin.seller-categories.edit.title')
            </p>

            <div class="flex items-center gap-x-2.5">
                <!-- Cancel Button -->
                <a
                    href="{{ route('admin.marketplace.seller_categories.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('marketplace::app.admin.seller-categories.edit.back-btn')
                </a>

                <!-- Update Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('marketplace::app.admin.seller-categories.edit.update-btn')
                </button>
            </div>
        </div>

        <!-- Full Pannel -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <div class="box-shadow w-full rounded bg-white p-4 dark:bg-gray-900">
                <div class="mb-2.5">
                    <!-- Parent category -->
                    <div class="flex flex-col gap-3">
                        <x-admin::tree.view
                            name-field="categories"
                            value-field="id"
                            selection-type="individual"
                            :items="json_encode($categories)"
                            :value="json_encode($sellerCategory->categories)"
                            :fallback-locale="config('app.fallback_locale')"
                        />
                    </div>
                </div>
            </div>
        </div>

        {!! view_render_event('marketplace.admin.seller_category.edit.create_form_controls.after') !!}
    </x-admin::form>

    {!! view_render_event('marketplace.admin.seller_category.edit.after', ['sellerCategory' => $sellerCategory]) !!}
</x-admin::layouts>