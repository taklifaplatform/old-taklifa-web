@if (core()->getConfigData('marketplace.settings.general.enable_product_flag'))
    <p class="mt-6 text-xl font-bold leading-6 text-gray-800 dark:text-white"> 
        @lang('marketplace::app.admin.products.edit.flags')
    </p>
    
    <x-admin::datagrid :src="route('admin.marketplace.products.edit', $product->id)" />
@endif
