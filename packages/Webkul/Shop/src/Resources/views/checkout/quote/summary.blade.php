<!-- clean this -->
<div class="w-[418px] max-w-full mb-12">
    {!! view_render_event('bagisto.shop.checkout.quote.summary.title.before') !!}

    <p class="text-2xl font-medium" role="heading">
        dfgf
        @lang('shop::app.checkout.cart.index.quote-summary')
    </p>

    {!! view_render_event('bagisto.shop.checkout.quote.summary.title.after') !!}

    <!-- Cart Totals -->
    <div class="grid gap-4 mt-12">
        <!-- Sub Total -->
        {!! view_render_event('bagisto.shop.checkout.quote.summary.sub_total.before') !!}

        <div class="flex justify-between text-right">
            <p class="text-base">
                @lang('shop::app.checkout.cart.index.subtotal')
            </p>

            <p class="text-base font-medium">
                @{{ quote.formatted_sub_total }}
            </p>
        </div>

        {!! view_render_event('bagisto.shop.checkout.quote.summary.sub_total.after') !!}

        <!-- Taxes -->
        {!! view_render_event('bagisto.shop.checkout.quote.summary.tax.before') !!}

        <div class="flex justify-between text-right" v-for="(amount, index) in quote.base_tax_amounts"
            v-if="parseFloat(quote.base_tax_total)">
            <p class="text-base max-sm:text-sm max-sm:font-normal">
                @lang('shop::app.checkout.quote.summary.tax') (@{{ index }})%
            </p>

            <p class="text-base font-medium max-sm:text-sm max-sm:font-medium">
                @{{ amount }}
            </p>
        </div>

        {!! view_render_event('bagisto.shop.checkout.quote.summary.tax.after') !!}

        <!-- Discount -->
        {!! view_render_event('bagisto.shop.checkout.quote.summary.discount_amount.before') !!}

        <div class="flex justify-between text-right"
            v-if="quote.base_discount_amount && parseFloat(quote.base_discount_amount) > 0">
            <p class="text-base">
                @lang('shop::app.checkout.quote.summary.discount-amount')
            </p>

            <p class="text-base font-medium">
                @{{ quote.formatted_base_discount_amount }}
            </p>
        </div>

        {!! view_render_event('bagisto.shop.checkout.quote.summary.discount_amount.after') !!}

        <!-- Shipping Rates -->
        {!! view_render_event('bagisto.shop.checkout.onepage.summary.delivery_charges.before') !!}

        <div class="flex text-right justify-between" v-if="quote.selected_shipping_rate">
            <p class="text-base">
                @lang('shop::app.checkout.onepage.summary.delivery-charges')
            </p>

            <p class="text-base font-medium">
                @{{ quote.selected_shipping_rate }}
            </p>
        </div>

        {!! view_render_event('bagisto.shop.checkout.onepage.summary.delivery_charges.after') !!}

        <!-- Apply Coupon -->
        {!! view_render_event('bagisto.shop.checkout.quote.summary.coupon.before') !!}

        @include('shop::checkout.quote.coupon')

        {!! view_render_event('bagisto.shop.checkout.quote.summary.coupon.after') !!}

        <!-- Cart Grand Total -->
        {!! view_render_event('bagisto.shop.checkout.quote.summary.grand_total.before') !!}

        <div class="flex justify-between text-right">
            <p class="text-lg font-semibold">
                @lang('shop::app.checkout.cart.index.grand-total')
            </p>

            <p class="text-lg font-semibold">
                @{{ quote.formatted_grand_total }}
            </p>
        </div>

        {!! view_render_event('bagisto.shop.checkout.quote.summary.grand_total.after') !!}

        {!! view_render_event('bagisto.shop.checkout.quote.summary.proceed_to_checkout.before') !!}

        <div class="flex justify-center space-x-4 mt-4">

            <a href="{{ route('shop.checkout.quote.address') }}" class="primary-button py-3 px-11 rounded-2xl">
                @lang('shop::app.checkout.cart.index.proceed-to-checkout')
            </a>
        </div>

        {!! view_render_event('bagisto.shop.checkout.quote.summary.proceed_to_checkout.after') !!}
    </div>
</div>
