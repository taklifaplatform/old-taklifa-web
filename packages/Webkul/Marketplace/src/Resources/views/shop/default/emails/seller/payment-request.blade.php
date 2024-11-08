@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <p style="font-weight: bold;font-size: 20px;color: #121A26;line-height: 24px;margin-bottom: 24px">
            @if ($is_requested)
                @lang('marketplace::app.emails.dear-admin'), 👋
            @else
                @lang('marketplace::app.emails.dear-seller'), 👋
            @endif
        </p>

        <p style="font-size: 16px;color: #384860;line-height: 24px;">
            @lang('marketplace::app.emails.seller.payment-request.greeting')
        </p>
    </div>

    <p style="font-size: 16px;color: #384860;line-height: 24px;margin-bottom: 40px">
        @if ($is_requested)
            @lang('marketplace::app.emails.seller.payment-request.requested', [
                'seller_name' => $sellerOrder->seller->first_name." ".$sellerOrder->seller->last_name,
                'order_id'    => '<a href="'.route('admin.sales.orders.view', $sellerOrder->order->id).'" style="color: #2969FF;">#'.$sellerOrder->order->increment_id.'</a>'
            ])
        @else
            @lang('marketplace::app.emails.seller.payment-request.paid', [
                'order_id' => '<a href="'.route('shop.marketplace.seller.account.orders.view', $sellerOrder->order_id).'" style="color: #2969FF;">#'.$sellerOrder->order->increment_id.'</a>'
            ])
        @endif
    </p>
@endcomponent