<div class="flex items-start gap-10 max-lg:gap-5 md:mt-9">
    <!-- Desktop Filter Shimmer Effect -->
    <div class="max-md:hidden">
        <x-shop::shimmer.categories.filters />
    </div>

    <div class="flex-1">
        <!-- Desktop Toolbar Shimmer Effect -->
        <div class="max-md:hidden">
            <x-marketplace::shop.shimmer.toolbar />
        </div>

        <!-- Product Card Container -->
        @if (request()->query('mode') =='list')
            <div class="mt-8 grid grid-cols-1 gap-6">
                <x-shop::shimmer.products.cards.list count="9" />
            </div>
        @else
            <div class="mt-8 grid grid-cols-3 gap-8 max-1060:grid-cols-2 max-sm:mt-5 max-sm:justify-items-center max-sm:gap-4">
                <!-- Product Card Shimmer Effect -->
                <x-shop::shimmer.products.cards.grid count="9" />
            </div> 
        @endif

        <button class="shimmer mx-auto mt-14 block h-12 w-[171.516px] rounded-2xl py-3"></button>
    </div>
</div>