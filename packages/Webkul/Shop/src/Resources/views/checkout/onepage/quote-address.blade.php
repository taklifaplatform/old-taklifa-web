{!! view_render_event('bagisto.shop.checkout.onepage.address.before') !!}

<!-- Accordian Blade Component -->
<x-shop::accordion class="mt-8 mb-7 !border-b-0">
    <!-- Accordian Header Component Slot -->
    <x-slot:header class="!p-0">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-medium max-sm:text-xl">
                @lang('shop::app.checkout.onepage.address.title')
            </h2>
        </div>
    </x-slot>

    <!-- Accordian Content Component Slot -->
    <x-slot:content class="!p-0 mt-8">
        @include('shop::checkout.onepage.address.quote-guest')
    </x-slot:content>
</x-shop::accordion>

{!! view_render_event('bagisto.shop.checkout.onepage.address.after') !!}
