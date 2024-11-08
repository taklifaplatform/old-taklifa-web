@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <p style="font-weight: bold;font-size: 20px;color: #121A26;line-height: 24px;margin-bottom: 24px">
            @if ($to_seller)
                @lang('marketplace::app.emails.dear', ['customer_name' => $seller->name]), 👋
            @else
                @lang('marketplace::app.emails.dear-admin'), 👋
            @endif
        </p>

        <p style="font-size: 16px;color: #384860;line-height: 24px;">
            @lang('marketplace::app.emails.report-seller.greeting')
        </p>
    </div>

    <p style="font-size: 16px;color: #384860;line-height: 24px;margin-bottom: 40px">
        @if ($to_seller)
            @lang('marketplace::app.emails.report-seller.seller-msg', ['name' => $data['name']]), 👋
        @else
            @lang('marketplace::app.emails.report-seller.admin-msg', [
                'name'        => $data['name'],
                'seller_name' => $seller->name
            ]), 👋
        @endif
    </p>

    <p style="font-size: 16px;color: #384860;line-height: 24px;margin-bottom: 40px">
        @lang('marketplace::app.emails.report-seller.reason', ['reason' => $data['reason']])
    </p>
@endcomponent