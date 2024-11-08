@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <span style="font-size: 22px;font-weight: 600;color: #121A26">
            @lang('marketplace::app.emails.seller.orders.created.title')
        </span> <br>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('marketplace::app.emails.dear-seller'),👋
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            {!! trans('marketplace::app.emails.seller.orders.created.greeting', [
                'order_id'   => '<a href="'.route('shop.customers.account.orders.view', $sellerOrder->order->id).'" style="color: #2969FF;">#'.$sellerOrder->order->increment_id.'</a>',
                'created_at' => core()->formatDate($sellerOrder->order->created_at, 'Y-m-d H:i:s')
                ])
            !!}
        </p>
    </div>

    <div style="font-size: 20px;font-weight: 600;color: #121A26">
        @lang('marketplace::app.emails.seller.orders.created.summary')
    </div>

    <div style="display: flex;flex-direction: row;margin-top: 20px;justify-content: space-between;margin-bottom: 40px;">
        @if ($sellerOrder->order->shipping_address)
            <div style="line-height: 25px;">
                <div style="font-size: 16px;font-weight: 600;color: #121A26;">
                    @lang('marketplace::app.emails.seller.orders.shipping-address')
                </div>

                <div style="font-size: 16px;font-weight: 400;color: #384860;margin-bottom: 40px;">
                    {{ $sellerOrder->order->shipping_address->company_name ?? '' }}<br/>

                    {{ $sellerOrder->order->shipping_address->name }}<br/>
                    
                    {{ $sellerOrder->order->shipping_address->address1 }}<br/>
                    
                    {{ $sellerOrder->order->shipping_address->postcode." ".$sellerOrder->order->shipping_address->city }}<br/>
                    
                    {{ $sellerOrder->order->shipping_address->state }}<br/>

                    ---<br/>

                    @lang('marketplace::app.emails.seller.orders.contact') : {{ $sellerOrder->order->billing_address->phone }}
                </div>

                <div style="font-size: 16px;font-weight: 600;color: #121A26;">
                    @lang('marketplace::app.emails.seller.orders.shipping')
                </div>

                <div style="font-size: 16px;font-weight: 400;color: #384860;">
                    {{ $sellerOrder->order->shipping_title }}
                </div>
            </div>
        @endif

        @if ($sellerOrder->order->billing_address)
            <div style="line-height: 25px;">
                <div style="font-size: 16px;font-weight: 600;color: #121A26;">
                    @lang('marketplace::app.emails.seller.orders.billing-address')
                </div>

                <div style="font-size: 16px;font-weight: 400;color: #384860;margin-bottom: 40px;">
                    {{ $sellerOrder->order->billing_address->company_name ?? '' }}<br/>

                    {{ $sellerOrder->order->billing_address->name }}<br/>
                    
                    {{ $sellerOrder->order->billing_address->address1 }}<br/>
                    
                    {{ $sellerOrder->order->billing_address->postcode." ".$sellerOrder->order->billing_address->city }}<br/>
                    
                    {{ $sellerOrder->order->billing_address->state }}<br/>

                    ---<br/>

                    @lang('marketplace::app.emails.seller.orders.contact') : {{ $sellerOrder->order->billing_address->phone }}
                </div>

                <div style="font-size: 16px;font-weight: 600;color: #121A26;">
                    @lang('marketplace::app.emails.seller.orders.payment')
                </div>

                <div style="font-size: 16px;font-weight: 400;color: #384860;">
                    {{ core()->getConfigData('sales.payment_methods.'.$sellerOrder->order->payment->method.'.title') }}
                </div>

                @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($sellerOrder->order->payment->method); @endphp

                @if (! empty($additionalDetails))
                    <div style="font-size: 16px; color: #384860;">
                        <div>{{ $additionalDetails['title'] }}</div>
                        <div>{{ $additionalDetails['value'] }}</div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div style="padding-bottom: 40px;border-bottom: 1px solid #CBD5E1;">
        <table style="overflow-x: auto; border-collapse: collapse;
        border-spacing: 0;width: 100%">
            <thead>
                <tr style="color: #121A26;border-top: 1px solid #CBD5E1;border-bottom: 1px solid #CBD5E1;">
                    @foreach (['sku', 'name', 'price', 'qty'] as $item)
                        <th style="text-align: left;padding: 15px">
                            @lang('marketplace::app.emails.seller.orders.'.$item)
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody style="font-size: 16px;font-weight: 400;color: #384860;">
                @foreach ($sellerOrder->items as $orderItem)
                    <tr>
                        <td style="text-align: left;padding: 15px">{{ $orderItem->item->getTypeInstance()->getOrderedItem($orderItem->item)->sku }}</td>

                        <td style="text-align: left;padding: 15px">
                            {{ $orderItem->item->name }}

                            @if (isset($orderItem->item->additional['attributes']))
                                <div>

                                    @foreach ($orderItem->item->additional['attributes'] as $attribute)
                                        <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                    @endforeach

                                </div>
                            @endif
                        </td>

                        <td style="text-align: left;padding: 15px">{{ core()->formatPrice($orderItem->item->price, $sellerOrder->order->order_currency_code) }}
                        </td>

                        <td style="text-align: left;padding: 15px">{{ $orderItem->item->qty_ordered }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display: grid;justify-content: end;font-size: 16px;color: #384860;line-height: 30px;padding-top: 20px;padding-bottom: 20px;">
        <div style="display: grid;gap: 100px;grid-template-columns: repeat(2, minmax(0, 1fr));">
            <span>
                @lang('marketplace::app.emails.seller.orders.subtotal')
            </span>

            <span style="text-align: right;">
                {{ core()->formatPrice($sellerOrder->order->sub_total, $sellerOrder->order->order_currency_code) }}
            </span>
        </div>

        @if ($sellerOrder->order->shipping_address)
            <div style="display: grid;gap: 100px;grid-template-columns: repeat(2, minmax(0, 1fr));">
                <span>
                    @lang('marketplace::app.emails.seller.orders.shipping-handling')
                </span>

                <span style="text-align: right;">
                    {{ core()->formatPrice($sellerOrder->order->shipping_amount, $sellerOrder->order->order_currency_code) }}
                </span>
            </div>
        @endif

        @foreach (\Webkul\Tax\Facades\Tax::getTaxRatesWithAmount($sellerOrder->order, false) as $taxRate => $taxAmount )
            <div style="display: grid;gap: 100px;grid-template-columns: repeat(2, minmax(0, 1fr));">
                <span>
                    @lang('marketplace::app.emails.seller.orders.tax') {{ $taxRate }} %
                </span>

                <span style="text-align: right;">
                    {{ core()->formatPrice($taxAmount, $sellerOrder->order->order_currency_code) }}
                </span>
            </div>
        @endforeach

        @if ($sellerOrder->order->discount_amount > 0)
            <div style="display: grid;gap: 100px;grid-template-columns: repeat(2, minmax(0, 1fr));">
                <span>
                    @lang('marketplace::app.emails.seller.orders.discount')
                </span>

                <span style="text-align: right;">
                    {{ core()->formatPrice($sellerOrder->order->discount_amount, $sellerOrder->order->order_currency_code) }}
                </span>
            </div>
        @endif

        <div style="display: grid;gap: 100px;grid-template-columns: repeat(2, minmax(0, 1fr));font-weight: bold">
            <span>
                @lang('marketplace::app.emails.seller.orders.grand-total')
            </span>

            <span style="text-align: right;">
                {{ core()->formatPrice($sellerOrder->order->grand_total, $sellerOrder->order->order_currency_code) }}
            </span>
        </div>
    </div>
@endcomponent
