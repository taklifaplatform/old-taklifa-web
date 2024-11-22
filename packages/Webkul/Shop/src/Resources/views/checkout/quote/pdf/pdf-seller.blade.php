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
    @php
        $base64Image = null;
        if ($seller && $seller->logo) {
            $path = storage_path('app/public/' . $seller->logo);
            if (file_exists($path)) {
                $base64Image = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
            }
        }
    @endphp

    <div style="direction: rtl;">
        <div class="col-12 header">
            @php
                $base64Image = null;
                if ($seller && $seller->logo) {
                    $path = storage_path('app/public/' . $seller->logo);
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
                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.show-invoice')
                </span>
            </div>
        </div>
    </div>

    <div class="page">
        <!-- Header -->
        <div class="page-content">
            <!-- Invoice Information -->
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        @if (core()->getConfigData('sales.invoice_settings.pdf_print_outs.invoice_id'))
                            <td style="width: 50%; padding: 2px 18px;border:none;">
                                <span>
                                    #{{ $marketplaceOrder->id }}
                                </span>
                                <b class="price">
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.invoice-id'):
                                </b>
                            </td>
                        @endif

                        @if (core()->getConfigData('sales.invoice_settings.pdf_print_outs.order_id'))
                            <td style="width: 50%; padding: 2px 18px;border:none;">
                                <span>
                                    #{{ $marketplaceOrder->order_id }}
                                </span>
                                <b class="price">
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.order-id'):
                                </b>
                            </td>
                        @endif
                    </tr>

                    <tr>
                        <td style="width: 50%; padding: 2px 18px;border:none;">
                            <span>
                                {{ core()->formatDate($marketplaceOrder->created_at, 'd-m-Y') }}
                            </span>
                            <b class="price">
                                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.created-on', ['date_time' => '']):
                            </b>
                        </td>

                        <td style="width: 50%; padding: 2px 18px;border:none;">
                            <span>
                                {{ core()->formatDate($marketplaceOrder->created_at, 'd-m-Y') }}
                            </span>
                            <b class="price">
                                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.order-date'):
                            </b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Billing & Shipping Address Details -->
            <div class="table address">
                <table style="width: 100%; margin-top: 5px">
                    <thead>
                        <tr style="background-color: #EFF7F3;">
                            <th class="table-header">
                                @lang('admin::app.sales.invoices.invoice-pdf.invoice-from')
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                {{-- @dd($seller) --}}
                                @if ($seller)
                                    <div style="margin: 10px 0;">
                                        <p style="margin: 0;">
                                            @include('admin::sales.address-seller', [
                                                'address' => $seller,
                                            ])
                                        </p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Billing & Shipping Address Details -->
            <div class="table address">
                <table style="width: 100%; margin-top: 5px">
                    <thead>
                        <tr style="background-color: #EFF7F3;">
                            <th class="table-header">
                                @lang('admin::app.sales.invoices.invoice-pdf.invoice-to')
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                @if ($marketplaceOrder->order->billing_address)
                                    <div style="margin: 10px 0;">
                                        <p style="margin: 0;">
                                            @include('admin::sales.address-customer', [
                                                'address' => $marketplaceOrder->order->billing_address,
                                            ])
                                        </p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Items -->
            <div class="table address">
                <table style="width: 100%; margin-top: 5px">
                    <thead>
                        <tr style="background-color: #EFF7F3;">
                            <th class="table-header">
                                @lang('marketplace::app.shop.sellers.account.orders.view.invoices.payment-method')
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>

            <table style="width: 100%; margin-bottom: 5px; margin-top: 5px" class="table-style">
                <thead style="background-color: #06593a;">
                    <tr style="color: #FFFFFF;">
                        <th class="th-style">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')</th>
                        <th class="th-style">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.discount')</th>
                        <th class="th-style">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.tax-amount')</th>
                        <th class="th-style">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.total')</th>
                        <th class="th-style">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.qty')</th>
                        <th class="th-style">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.price')</th>
                        <th class="th-style">@lang('marketplace::app.shop.sellers.account.orders.view.invoices.product-name')</th>
                        <th class="th-style">SKU</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($marketplaceOrder->items as $marketplaceOrderItem)
                        <tr class="color td-style">
                            <!-- Total (Base Total + Tax Amount) -->
                            <td class="td-style">
                                {{ core()->formatPrice($marketplaceOrderItem->item->total + $marketplaceOrderItem->item->tax_amount, $marketplaceOrder->order_currency_code) }}
                            </td>

                            <!-- Discount Amount -->
                            <td class="td-style">
                                {{ core()->formatPrice($marketplaceOrderItem->item->discount_amount, $marketplaceOrder->order_currency_code) }}
                            </td>

                            <!-- Tax Amount -->
                            <td class="td-style">
                                {{ core()->formatPrice($marketplaceOrderItem->item->tax_amount, $marketplaceOrder->order_currency_code) }}
                            </td>

                            <!-- Price Display Based on Configuration -->
                            <td class="td-style">
                                {{ core()->formatPrice($marketplaceOrderItem->item->total, $marketplaceOrder->order_currency_code) }}
                            </td>

                            <!-- Quantity Ordered -->
                            <td class="td-style">
                                {{ $marketplaceOrderItem->item->qty_ordered }}
                            </td>

                            <!-- Price Display Based on Configuration -->
                            <td class="td-style">
                                {{ core()->formatPrice($marketplaceOrderItem->item->price, $marketplaceOrder->order_currency_code) }}
                            </td>

                            <td class="td-style">
                                <p class="text-sm font-medium">
                                    {{ $marketplaceOrderItem->item->name }}
                                </p>
                            </td>
                            <td class="td-style">
                                <p class="text-sm font-normal">
                                    @lang('marketplace::app.shop.sellers.account.orders.view.invoices.sku', ['sku' => $marketplaceOrderItem->item->sku])
                                </p>
                                @if (isset($marketplaceOrderItem->item->additional['attributes']))
                                    <div>
                                        @foreach ($marketplaceOrderItem->item->additional['attributes'] as $attribute)
                                            <b>{{ $attribute['attribute_name'] }} :
                                            </b>{{ $attribute['option_label'] }}
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Table -->
            <div class="sale-summary">
                <table class="{{ core()->getCurrentLocale()->direction }}">
                    <tbody>
                        @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                            <tr>
                                <td>{!! core()->formatBasePrice($marketplaceOrder->base_sub_total_incl_tax, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total')</td>
                            </tr>
                        @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                            <tr>
                                <td>{!! core()->formatBasePrice($marketplaceOrder->base_sub_total_incl_tax, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total-incl-tax')
                                </td>
                            </tr>

                            <tr>
                                <td>{!! core()->formatBasePrice($marketplaceOrder->base_sub_total, true) !!}</td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.sub-total-excl-tax')
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>{!! core()->formatBasePrice($marketplaceOrder->base_sub_total, true) !!}</td>
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
                                <td>{!! core()->formatBasePrice($marketplaceOrder->base_shipping_amount, true) !!}
                                </td>
                                <td>-</td>
                                <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.shipping-handling')
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td>{!! core()->formatBasePrice($marketplaceOrder->base_tax_amount, true) !!}</td>
                            <td>-</td>
                            <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.tax')</td>
                        </tr>

                        <tr>
                            <td>{!! core()->formatBasePrice($marketplaceOrder->base_discount_amount, true) !!}</td>
                            <td>-</td>
                            <td>@lang('marketplace::app.shop.sellers.account.orders.view.invoices.discount')</td>
                        </tr>

                        <tr>
                            <td style="border-top: 1px solid #FFFFFF;">
                                <b>{!! core()->formatBasePrice($marketplaceOrder->base_grand_total - $marketplaceOrder->base_shipping_amount, true) !!}</b>
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
