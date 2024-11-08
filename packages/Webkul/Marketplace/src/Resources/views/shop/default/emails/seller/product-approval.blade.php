@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <p style="font-weight: bold;font-size: 20px;color: #121A26;line-height: 24px;margin-bottom: 24px">
            @lang('marketplace::app.emails.dear', ['customer_name' => $product->seller->name]), 👋
        </p>

        <p style="font-size: 16px;color: #384860;line-height: 24px;">
            @lang('marketplace::app.emails.seller.product-approval.greeting')
        </p>
    </div>

    <p style="font-size: 16px;color: #384860;line-height: 24px;margin-bottom: 40px">
        @if ($product->is_approved)
            @lang('marketplace::app.emails.seller.product-approval.approved', ['name' => $product->product->name])
        @else
            @lang('marketplace::app.emails.seller.product-approval.disapproved', ['name' => $product->product->name])
        @endif
    </p>
@endcomponent