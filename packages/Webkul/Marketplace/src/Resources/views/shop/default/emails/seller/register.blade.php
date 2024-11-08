@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <p style="font-weight: bold;font-size: 20px;color: #121A26;line-height: 24px;margin-bottom: 24px">
            @lang('marketplace::app.emails.dear-admin'), 👋
        </p>

        <p style="font-size: 16px;color: #384860;line-height: 24px;">
            @lang('marketplace::app.emails.seller.registration.greeting')
        </p>
    </div>

    <p style="font-size: 16px;color: #384860;line-height: 24px;margin-bottom: 40px">
        @lang('marketplace::app.emails.seller.registration.description')
    </p>

    <p style="font-size: 16px;color: #384860;line-height: 24px;margin-bottom: 40px">
        @lang('marketplace::app.emails.seller.registration.details')
    </p>

    <p style="font-size: 16px;color: #020202;line-height: 20px;">
        @lang('marketplace::app.emails.seller.registration.name')

        <span style="font-size: 16px;color: #384860;line-height: 20px;">
            {{ $seller->name }}
        </span>
    </p>

    <p style="font-size: 16px;color: #020202;line-height: 20px;">
        @lang('marketplace::app.emails.seller.registration.shop')

        <span style="font-size: 16px;color: #384860;line-height: 20px;">
            {{ $seller->shop_url }}
        </span>
    </p>

    <p style="font-size: 16px;color: #020202;line-height: 20px;">
        @lang('marketplace::app.emails.seller.registration.email')

        <span style="font-size: 16px;color: #384860;line-height: 20px;">
            {{ $seller->email }}
        </span>
    </p>

    <p style="font-size: 16px;color: #020202;line-height: 20px;margin-bottom: 40px">
        @lang('marketplace::app.emails.seller.registration.date')

        <span style="font-size: 16px;color: #384860;line-height: 20px;">
            {{ $seller->created_at }}
        </span>
    </p>
@endcomponent