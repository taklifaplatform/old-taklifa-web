<div class="box-shadow relative mb-2.5 rounded-xl border bg-white p-4">
    <!-- Panel Header -->
    <div class="mb-4 flex justify-between gap-5">
        <div class="flex flex-col gap-2">
            <p class="text-base font-semibold text-gray-800">
                @lang('marketplace::app.shop.sellers.account.products.edit.images.title')
            </p>

            <p class="text-xs font-medium text-gray-500">
                @lang('marketplace::app.shop.sellers.account.products.edit.images.info')
            </p>
        </div>
    </div>

    <!-- Image Blade Component -->
    <x-marketplace::shop.media.images
        name="images[files]"
        allow-multiple="true"
        show-placeholders="true"
        :uploaded-images="$product?->images ?? null"
    />
</div>
