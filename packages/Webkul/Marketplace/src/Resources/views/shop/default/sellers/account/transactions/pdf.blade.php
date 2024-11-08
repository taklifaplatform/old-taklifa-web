<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}"
>
    <head>
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        @php
            $fontPath = [];
            
            $fontFamily = [
                'regular' => 'Arial, sans-serif',
                'bold'    => 'Arial, sans-serif',
            ];

            if (in_array(app()->getLocale(), ['ar', 'he', 'fa', 'tr', 'ru', 'uk'])) {
                $fontFamily = [
                    'regular' => 'DejaVu Sans',
                    'bold'    => 'DejaVu Sans',
                ];
            } elseif (app()->getLocale() == 'zh_CN') {
                $fontPath = [
                    'regular' => asset('fonts/NotoSansSC-Regular.ttf'),
                    'bold'    => asset('fonts/NotoSansSC-Bold.ttf'),
                ];
                
                $fontFamily = [
                    'regular' => 'Noto Sans SC',
                    'bold'    => 'Noto Sans SC Bold',
                ];
            } elseif (app()->getLocale() == 'ja') {
                $fontPath = [
                    'regular' => asset('fonts/NotoSansJP-Regular.ttf'),
                    'bold'    => asset('fonts/NotoSansJP-Bold.ttf'),
                ];
                
                $fontFamily = [
                    'regular' => 'Noto Sans JP',
                    'bold'    => 'Noto Sans JP Bold',
                ];
            } elseif (app()->getLocale() == 'hi_IN') {
                $fontPath = [
                    'regular' => asset('fonts/Hind-Regular.ttf'),
                    'bold'    => asset('fonts/Hind-Bold.ttf'),
                ];
                
                $fontFamily = [
                    'regular' => 'Hind',
                    'bold'    => 'Hind Bold',
                ];
            } elseif (app()->getLocale() == 'bn') {
                $fontPath = [
                    'regular' => asset('fonts/NotoSansBengali-Regular.ttf'),
                    'bold'    => asset('fonts/NotoSansBengali-Bold.ttf'),
                ];
                
                $fontFamily = [
                    'regular' => 'Noto Sans Bengali',
                    'bold'    => 'Noto Sans Bengali Bold',
                ];
            } elseif (app()->getLocale() == 'sin') {
                $fontPath = [
                    'regular' => asset('fonts/NotoSansSinhala-Regular.ttf'),
                    'bold'    => asset('fonts/NotoSansSinhala-Bold.ttf'),
                ];
                
                $fontFamily = [
                    'regular' => 'Noto Sans Sinhala',
                    'bold'    => 'Noto Sans Sinhala Bold',
                ];
            }
        @endphp

        <style type="text/css">
            @if (! empty($fontPath['regular']))
                @font-face {
                    src: url({{ $fontPath['regular'] }}) format('truetype');
                    font-family: {{ $fontFamily['regular'] }};
                }
            @endif
            
            @if (! empty($fontPath['bold']))
                @font-face {
                    src: url({{ $fontPath['bold'] }}) format('truetype');
                    font-family: {{ $fontFamily['bold'] }};
                    font-style: bold;
                }
            @endif
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: {{ $fontFamily['regular'] }};
            }

            body {
                font-size: 10px;
                color: #091341;
                font-family: "{{ $fontFamily['regular'] }}";
            }

            b, th {
                font-family: "{{ $fontFamily['bold'] }}";
            }

            .container {
                padding: 20px;
                display: block;
            }

            .invoice-summary {
                margin-bottom: 20px;
            }

            .table {
                margin-top: 20px;
            }

            .table table {
                width: 100%;
                border-collapse: collapse;
                text-align: center;
            }

            .table thead th {
                font-weight: 700;
                border-top: solid 1px #d3d3d3;
                border-bottom: solid 1px #d3d3d3;
                border-left: solid 1px #d3d3d3;
                padding: 5px 10px;
                background: #F4F4F4;
            }

            .table thead th:last-child {
                border-right: solid 1px #d3d3d3;
            }

            .table tbody td {
                padding: 5px 10px;
                border-bottom: solid 1px #d3d3d3;
                border-left: solid 1px #d3d3d3;
                color: #3A3A3A;
                vertical-align: middle;
            }

            .table tbody td p {
                margin: 0;
            }

            .table tbody td:last-child {
                border-right: solid 1px #d3d3d3;
            }

           .sale-summary {
                margin-top: 40px;
                float: right;
            }

            .sale-summary tr td {
                padding: 3px 5px;
            }

            .sale-summary tr.bold {
                font-weight: 600;
            }

            .label {
                color: #000;
                font-weight: bold;
            }
        </style>
    </head>

    <body style="background-image: none;background-color: #fff;">
        <div class="container">

            <div class="invoice-summary">

                <div class="row">
                    <span class="label">@lang('marketplace::app.shop.sellers.account.transactions.view.id') -</span>
                    <span class="value">#{{ $transaction->transaction_id }}</span>
                </div>

                <div class="row">
                    <span class="label">@lang('marketplace::app.shop.sellers.account.transactions.view.method') -</span>
                    <span class="value">{{ $transaction->method }}</span>
                </div>

                <div class="row">
                    <span class="label">@lang('marketplace::app.shop.sellers.account.transactions.view.date') -</span>
                    <span class="value">{{ core()->formatDate($transaction->created_at, 'd M Y') }}</span>
                </div>

                <div class="items table">
                    <table>
                        <thead>
                            <tr>
                                <th>@lang('marketplace::app.shop.sellers.account.transactions.view.name')</th>
                                <th>@lang('marketplace::app.shop.sellers.account.transactions.view.price')</th>
                                <th>@lang('marketplace::app.shop.sellers.account.transactions.view.qty')</th>
                                <th>@lang('marketplace::app.shop.sellers.account.transactions.view.total')</th>
                                <th>@lang('marketplace::app.shop.sellers.account.transactions.view.commission')</th>
                                <th>@lang('marketplace::app.shop.sellers.account.transactions.view.seller-total')</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($transaction->order->items as $item)
                                <tr>
                                    <td>{{ $item->item->name }}</td>

                                    <td>
                                        {!! core()->formatPrice($item->item->price, $transaction->order->order->order_currency_code) !!}
                                    </td>

                                    <td>{{$item->item->qty_shipped}}</td>

                                    <td>
                                        {{ core()->formatPrice($item->item->total, $transaction->order->order->order_currency_code) }}
                                    </td>

                                    <td>
                                        {{ core()->formatPrice($item->commission, $transaction->order->order->order_currency_code) }}
                                    </td>

                                    <td>
                                        {{ core()->formatPrice($item->seller_total, $transaction->order->order->order_currency_code) }}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <table class="sale-summary">
                    <tr>
                        <td>@lang('marketplace::app.shop.sellers.account.transactions.view.subtotal')</td>
                        <td>-</td>
                        <td>{{ core()->formatPrice($transaction->order->sub_total, $transaction->order->order->order_currency_code) }}</td>
                    </tr>

                    @if ($transaction->order->order->haveStockableItems())
                    <tr>
                        <td>@lang('marketplace::app.shop.sellers.account.transactions.view.shipping-handling')</td>
                        <td>-</td>
                        <td>{{ core()->formatPrice(0, $transaction->order->order->order_currency_code) }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td>@lang('marketplace::app.shop.sellers.account.transactions.view.tax')</td>
                        <td>-</td>
                        <td>{{ core()->formatPrice($transaction->order->tax_amount, $transaction->order->order->order_currency_code) }}</td>
                    </tr>

                    <tr>
                        <td>@lang('marketplace::app.shop.sellers.account.transactions.view.commission')</td>
                        <td>-</td>
                        <td>{{ core()->formatPrice($transaction->order->base_commission, $transaction->order->order->order_currency_code) }}</td>
                    </tr>

                    <tr class="">
                        <td>
                            <b> @lang('marketplace::app.shop.sellers.account.transactions.view.seller-total') </b>
                        </td>
                        <td>-</td>
                        <td>
                            <b> {{ core()->formatPrice($transaction->order->base_seller_total, $transaction->order->order->order_currency_code) }} </b>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </body>
</html>
