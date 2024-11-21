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

<head>
    <!-- meta tags -->
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" charset="UTF-8" />

    <style type="text/css">
        body,
        th,
        td,
        h5 {
            font-family: 'IBM Plex Sans Arabic', 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
        }

        .container {
            padding: 20px;
            display: block;
        }


        .table thead th:last-child {
            border-right: solid 1px #d3d3d3;
        }

        .table tbody td {
            padding: 5px 10px;
            color: #3A3A3A;
            vertical-align: middle;
        }

        .sale-summary {
            margin-top: 20px;
            float: left;
            background-color: #EFF7F3;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .sale-summary tr td {
            padding: 2px 12px;
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
            display: inline-block;
            vertical-align: top;
            margin: 0px 5px;
        }

        .table-header {
            color: #14532d;
            padding-right: 6pt;
            padding: 6px
        }

        .align-left {
            text-align: left;
        }

        .invoice-text {
            font-size: 35px;
            color: #14532d;
            font-weight: bold;
            position: absolute;
            width: 100%;
            left: 0;
            text-align: center;
            top: -28px;
            margin-bottom: 10px
        }

        .price {
            font-size: 12pt;
            font-weight: bold;
            color: #14532d;
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
            border: 1px solid #ddd;
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
    </style>
</head>

<body style="background-image: none; background-color: #fff;">
    <div>
        <div>
            <div style="direction: rtl;">
                <div class="col-12 header">
                    @php
                        $base64Image = null;
                        if ($channel && $channel->logo) {
                            $path = storage_path('app/public/' . $channel->logo);
                            if (file_exists($path)) {
                                $base64Image = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
                            }
                        }
                    @endphp
                    @if ($base64Image)
                        <div style="border: none; padding: 0;">
                            <img src="{{ $base64Image }}" style="width: 110px; height: auto;">
                        </div>
                    @endif
                    <div class="invoice-text">
                        <span>
                            @lang('admin::app.sales.invoices.invoice-pdf.invoice')
                        </span>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 50px;">
                <div class="col-12">
                    <div class="col-6">
                        <div>
                            <div class="row">
                                <p style="margin: 0;">#{{ $invoice->increment_id ?? $invoice->id }}
                                    <span class="price">
                                        &nbsp;&nbsp; @lang('admin::app.sales.invoices.invoice-pdf.invoice-id'):
                                    </span>
                                </p>
                            </div>

                            <div class="row">
                                <p style="margin: 0;">{{ core()->formatDate($invoice->created_at, 'd-m-Y') }}
                                    <span class="price">
                                        &nbsp;&nbsp; @lang('admin::app.sales.invoices.invoice-pdf.date'):
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6" style="padding-left: 70px">
                        <div class="row">
                            <p style="margin: 0;">#{{ $invoice->order->increment_id }}
                                <span class="price">
                                    &nbsp;&nbsp; @lang('admin::app.sales.invoices.invoice-pdf.order-id'):
                                </span>
                            </p>
                        </div>

                        <div class="row">
                            <p style="margin: 0;">{{ core()->formatDate($invoice->order->created_at, 'd-m-Y') }}
                                <span class="price">
                                    &nbsp;&nbsp; @lang('admin::app.sales.invoices.invoice-pdf.order-date'):
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <!-- Billing & Shipping Address Details -->
                <div class="table address">
                    <table style="width: 100%; margin-top: 20px">
                        <thead>
                            <tr style="background-color: #EFF7F3;">
                                <th class="table-header">
                                    @lang('admin::app.sales.invoices.invoice-pdf.invoice-to')
                                </th>

                                @if ($invoice->order->shipping_address)
                                    <th class="table-header">
                                        الشحن إلى
                                    </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                @foreach (['billing_address', 'shipping_address'] as $addressType)
                                    @if ($invoice->order->$addressType)
                                        <td>
                                            <div style="margin-top: 10px; margin-bottom: 10px;">
                                                <div style="margin-bottom: 0;">
                                                    <p style="margin: 0;">
                                                        {{ $invoice->order->$addressType->name }}
                                                        &nbsp;&nbsp; &nbsp; <b>الاستاذ(ة):</b>
                                                    </p>
                                                    <p style="margin: 0;">
                                                        {{ $invoice->order->$addressType->city }}
                                                        ,{{ $invoice->order->$addressType->country }}
                                                        &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;<b>@lang('admin::app.sales.invoices.invoice-pdf.address'):</b>
                                                    </p>
                                                    <p style="margin: 0;">
                                                        {{ $invoice->order->$addressType->phone }}
                                                        &nbsp;&nbsp; &nbsp;&nbsp; <b>@lang('admin::app.sales.invoices.invoice-pdf.communication'):</b>
                                                    </p>
                                                    <p style="margin: 0;">{{ $invoice->order->$addressType->email }}
                                                        &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;<b>@lang('admin::app.sales.invoices.invoice-pdf.email'):</b>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Payment & Shipping Methods -->
                <div class="table">
                    <table style="width: 100%;">
                        <thead>
                            <tr style="background-color: #EFF7F3;">
                                <th class="table-header">
                                    @lang('admin::app.sales.invoices.invoice-pdf.payment-method')
                                </th>

                                @if ($invoice->order->shipping_address)
                                    <th class="table-header align-left">@lang('admin::app.sales.invoices.invoice-pdf.shipping-method')</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                @if ($invoice->order->shipping_address)
                                    <td>
                                        {{ $invoice->order->shipping_title }}
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>


                <table style="width: 100%; margin-bottom: 10px; margin-top: 20px" class="table-style">
                    <thead style="background-color: #06593a;">
                        <tr style="color: #FFFFFF;">
                            <th class="th-style">@lang('admin::app.sales.invoices.invoice-pdf.total-all')</th>
                            <th class="th-style">@lang('admin::app.sales.invoices.invoice-pdf.tax-amount')</th>
                            <th class="th-style">@lang('admin::app.sales.invoices.invoice-pdf.subtotal')</th>
                            <th class="th-style">@lang('admin::app.sales.invoices.invoice-pdf.qty')</th>
                            <th class="th-style">@lang('admin::app.sales.invoices.invoice-pdf.price')</th>
                            <th class="th-style">@lang('admin::app.sales.invoices.invoice-pdf.product-name')</th>
                            <th class="th-style">SKU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $item)
                            <tr class="color td-style">
                                <td class="td-style">
                                    <span>ريال</span>
                                    {{ number_format($item->base_total + $item->tax_amount, 2) }}
                                </td>
                                <td class="td-style">
                                    <span>ريال</span>
                                    {{ number_format($item->tax_amount, 2) }}
                                </td>
                                <td class="td-style">
                                    <span>ريال</span>
                                    {{ number_format($item->base_total, 2) }}
                                </td>
                                <td class="td-style">{{ $item->qty }}</td>
                                <td class="td-style">
                                    <span>ريال</span>
                                    {{ number_format($item->base_price, 2) }}
                                </td>
                                <td class="td-style">
                                    @php
                                        $itemName = strip_tags(html_entity_decode($item['name']));
                                        $chunks = splitParagraphIntoChunks($itemName);
                                    @endphp
                                    @foreach ($chunks as $chunk)
                                        <p style="margin: 0; text-align: center">{{ $chunk }}</p>
                                    @endforeach
                                </td>
                                <td class="td-style">
                                    {{ is_object($item) ? $item->sku : '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Sale Summary -->
                <table class="sale-summary">
                    <tr>
                        <td>
                            <span>ر.س</span>
                            {{ number_format($invoice->sub_total, 2) }}
                        </td>
                        <td>-</td>
                        <td style="font-weight: bold; font-size: 13pt"> المجموع الفرعي</td>
                    </tr>

                    <tr>
                        <td>
                            <span>ر.س</span>
                            {{ number_format($invoice->shipping_amount, 2) }}
                        </td>
                        <td>-</td>
                        <td style="font-weight: bold; font-size: 13pt">الشحن والتسليم</td>
                    </tr>

                    <tr>
                        <td>
                            <span>ر.س</span>
                            {{ number_format($invoice->tax_amount, 2) }}
                        </td>
                        <td>-</td>
                        <td style="font-weight: bold; font-size: 13pt">ضريبة</td>
                    </tr>

                    <tr>
                        <td>
                            <span>ر.س</span>
                            {{ number_format($invoice->discount_amount, 2) }}
                        </td>
                        <td>-</td>
                        <td style="font-weight: bold; font-size: 13pt">خصم</td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            <hr>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span>ر.س</span>
                            {{ number_format($invoice->grand_total, 2) }}
                        </td>
                        <td>-</td>
                        <td style="font-weight: bold; font-size: 13pt">الإجمالي الكلي</td>
                    </tr>
                </table>
            </div>
        </div>
</body>

</html>
