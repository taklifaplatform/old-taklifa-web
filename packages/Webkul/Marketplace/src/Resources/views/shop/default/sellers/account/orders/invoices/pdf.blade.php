<!DOCTYPE html>
<html lang="ar">
@php
    if (!function_exists('splitParagraphIntoChunks')) {
        function splitParagraphIntoChunks($paragraph, $wordsPerChunk = 3)
        {
            $words = preg_split('/\s+/', trim($paragraph));

            $chunks = [];

            for ($i = 0; $i < count($words); $i += $wordsPerChunk) {
                $chunk = array_slice($words, $i, $wordsPerChunk);
                $chunks[] = implode(' ', $chunk);
            }

            return $chunks;
        }
    }
@endphp
<!-- meta tags -->
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
<meta http-equiv="Cache-control" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" charset="UTF-8" />

<style type="text/css">
    body,
    th,
    td,
    h5 {
        font-family: ' IBM Plex Sans Arabic', 'DejaVu Sans', sans-serif;
        direction: rtl;
        text-align: right;
    }

    .container {
        padding: 20px;
        display: block;
    }

    .table thead th:last-child {
        border-right: solid 1px #E9E9E9;
    }

    .table tbody td {
        padding: 5px 10px;
        color: #3A3A3A;
        vertical-align: middle;
    }

    .label {
        color: #000;
        font-weight: bold;
    }

    .text-center {
        text-align: center;
    }

    .col-6 {
        width: 42%;
        display:
            inline-block;
        vertical-align: top;
        margin: 0px 5px;
    }

    .align-left {
        text-align: left;
    }

    .table-style {
        border-collapse: collapse;
        font-size: 11pt;
    }

    .th-style {
        border: 1px solid #ddd;
        word-wrap: break-word;
        padding: 6px;
        text-align: center;
        height: 40px;
    }

    .td-style {
        border:
            1px solid #ddd;
        text-align: right;
        word-wrap: break-word;
        padding: 6px;
        text-align: center;
    }

    .color:nth-child(even) {
        background-color: #EFF7F3;
    }

    .table-style {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        overflow: hidden;
        border: 1px solid #ddd;
    }

    .page-header {
        border-bottom: 1px solid #E9EFFC;
        text-align: center;
        font-size: 24px;
        text-transform: uppercase;
        /* color: #000DBB; */
        padding: 24px 0;
        margin: 0;
    }
</style>
</head>

<body>
    @php
        $base64Image = null;
        if ($seller && $seller->logo) {
            $path = storage_path('app/public/' . $seller->logo);
            if (file_exists($path)) {
                $base64Image = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
            }
        }
    @endphp

    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr>
                <td colspan="3" style="border: none;">
                    <h1 style=" font-size: 24pt; margin: 0;"> @lang('marketplace::app.shop.sellers.account.orders.view.invoices.show-title')</h1>
                </td>
                <td colspan="7" style="border: none; padding: 0;">
                    @if ($base64Image)
                        <img src="{{ $base64Image }}" style="width: 110px; height: auto;">
                    @else
                        <img
                            src="data:image/png;base64,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" />
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page">
        <!-- Header -->
        {{-- <div class="page-header">
            <b>{{ Str::upper(trans('marketplace::app.shop.sellers.account.orders.view.invoices.title')) }}</b>
        </div> --}}

        <div class="page-content">
            <!-- Invoice Information -->
            <table style="width: 100%; padding-bottom: 22pt; padding-top: 23px">
                <tbody>
                    <tr>
                        @if (core()->getConfigData('sales.invoice_settings.pdf_print_outs.invoice_id'))
                            <td style="width: 50%; padding: 2px 18px;border:none;">
                                <span>
                                    #{{ $invoice->id }}
                                </span>
                                <b>
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.invoice-id'):
                                </b>
                            </td>
                        @endif

                        @if (core()->getConfigData('sales.invoice_settings.pdf_print_outs.order_id'))
                            <td style="width: 50%; padding: 2px 18px;border:none;">
                                <span>
                                    #{{ $invoice->order->order->increment_id }}
                                </span>
                                <b>
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.order-id'):
                                </b>
                            </td>
                        @endif
                    </tr>

                    <tr>
                        <td style="width: 50%; padding: 2px 18px;border:none;">
                            <span>
                                {{ core()->formatDate($invoice->created_at, 'd-m-Y') }}
                            </span>
                            <b>
                                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.created-on', ['date_time' => '']):
                            </b>
                        </td>

                        <td style="width: 50%; padding: 2px 18px;border:none;">
                            <span>
                                {{ core()->formatDate($invoice->order->created_at, 'd-m-Y') }}
                            </span>
                            <b>
                                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.order-date'):
                            </b>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Items -->
            <table style="width: 100%;" class="table-style">
                <thead style="background-color: #F5F5F5;">
                    <tr style="color: #0d0c0c;">
                        <th style="padding: 12px; ">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')</th>
                        <th style="padding: 3px; ">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.discount')</th>
                        <th style="padding: 3px;" >@lang('marketplace::app.shop.sellers.account.orders.view.invoices.tax-amount')</th>
                        <th style="padding: 3px;">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.total')</th>
                        <th style="padding: 3px;">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.qty')</th>
                        <th style="padding: 12px;">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.price')</th>
                        <th style="padding: 12px; text-align: center">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.name')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->invoice->items as $item)
                        <tr class="color">
                            <td style=" ">
                                {!! core()->formatBasePrice($item->base_total + $item->base_tax_amount, true) !!}
                            </td>
                            <td style="">
                                {!! core()->formatBasePrice($invoice->base_discount_amount, true) !!}
                            </td>
                            <td style="">
                                {!! core()->formatBasePrice($item->base_tax_amount, true) !!}
                            </td>
                            <td style="">
                                @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                    {!! core()->formatBasePrice($item->base_total_incl_tax, true) !!}
                                @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                    {!! core()->formatBasePrice($item->base_total_incl_tax, true) !!}

                                    <div class="small-text">
                                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.excl-tax')

                                        <span>
                                            {{ core()->formatBasePrice($item->base_total) }}
                                        </span>
                                    </div>
                                @else
                                    {!! core()->formatBasePrice($item->base_total, true) !!}
                                @endif
                            </td>
                            <td style="">
                                {{ $item->qty }}
                            </td>
                            <td style="">
                                @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                    {!! core()->formatBasePrice($item->base_price_incl_tax, true) !!}
                                @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                                    {!! core()->formatBasePrice($item->base_price_incl_tax, true) !!}

                                    <div class="small-text">
                                        @lang('marketplace::app.shop.sellers.account.orders.view.invoices.excl-tax')

                                        <span>
                                            {{ core()->formatBasePrice($item->base_price) }}
                                        </span>
                                    </div>
                                @else
                                    {!! core()->formatBasePrice($item->base_price, true) !!}
                                @endif

                            </td>
                            <td style="padding: 12px; padding-right: 18px">
                                {{-- @php
                                $descriptions = strip_tags(html_entity_decode($item->product->short_description));
                                @endphp

                                @foreach (splitParagraphIntoChunks($descriptions) as $desc)
                                <p style="margin: 0; text-align: center">{{ $desc }}</p>
                                @endforeach --}}
                                {{ $item->name }}
                                @if (isset($item->additional['attributes']))
                                    <div class="item-options">
                                        @foreach ($item->additional['attributes'] as $attribute)
                                            <b>{{ $attribute['attribute_name'] }} :
                                            </b>{{ $attribute['option_label'] }}</br>
                                        @endforeach
                                    </div>
                                @endif
                                <br>
                                SKU - {{ $item->getTypeInstance()->getOrderedItem($item)->sku }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Table -->
            <div style="padding-top: 22px">
                <table class="{{ core()->getCurrentLocale()->direction }}">
                    <tbody>
                        @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                            <tr>
                                <td>{!! core()->formatBasePrice($invoice->base_sub_total_incl_tax, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')</td>
                            </tr>
                        @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                            <tr>
                                <td>{!! core()->formatBasePrice($invoice->base_sub_total_incl_tax, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total-incl-tax')
                                </td>
                            </tr>

                            <tr>
                                <td>{!! core()->formatBasePrice($invoice->base_sub_total, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total-excl-tax')
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>{!! core()->formatBasePrice($invoice->base_sub_total, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')</td>
                            </tr>
                        @endif

                        @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'including_tax')
                            <tr>
                                <td>{!! core()->formatBasePrice(0, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.shipping-handling-incl-tax')
                                </td>
                            </tr>
                        @elseif (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                            <tr>
                                <td>{!! core()->formatBasePrice(0, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.shipping-handling-incl-tax')
                                </td>
                            </tr>

                            <tr>
                                <td>{!! core()->formatBasePrice(0, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.shipping-handling-excl-tax')
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>{!! core()->formatBasePrice($invoice->order->order->base_shipping_amount, true) !!}
                                </td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.shipping-handling')
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td>{!! core()->formatBasePrice($invoice->base_tax_amount, true) !!}</td>
                            <td>-</td>
                            <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.tax')</td>
                        </tr>

                        <tr>
                            <td>{!! core()->formatBasePrice($invoice->base_discount_amount, true) !!}</td>
                            <td>-</td>
                            <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.discount')</td>
                        </tr>

                        <tr>
                            <td style="border-top: 1px solid #FFFFFF;">
                                <b>{!! core()->formatBasePrice($invoice->base_grand_total - $invoice->base_shipping_amount, true) !!}</b>
                            </td>
                            <td style="border-top: 1px solid #FFFFFF;">-</td>
                            <td style="border-top: 1px solid #FFFFFF;">
                                <b>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.grand-total')</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
