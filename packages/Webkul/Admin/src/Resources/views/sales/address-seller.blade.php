<!DOCTYPE html>
<html lang="ar">
    <div>
        <div style="margin-bottom: 0;">
            <p style="margin: 0;">
                {{ $address->name }}
                &nbsp;&nbsp; &nbsp; <b>@lang('admin::app.sales.invoices.invoice-pdf.Mr'):</b>
            </p>
            <p style="margin: 0;">
                {{ $address->url }}
                &nbsp;&nbsp; &nbsp; <b>@lang('admin::app.sales.invoices.invoice-pdf.shop-name'):</b>
            </p>
            <p style="margin: 0;">
                {{ $address->shop_title }},  {{ $address->city }},  {{ $address->state }}
                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;<b>@lang('admin::app.sales.invoices.invoice-pdf.address'):</b>
            </p>
            <p style="margin: 0;">
                {{ $address->phone }}
                &nbsp;&nbsp; &nbsp;&nbsp; <b>@lang('admin::app.sales.invoices.invoice-pdf.communication'):</b>
            </p>
            <p style="margin: 0;">{{ $address->email }}
                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;<b>@lang('admin::app.sales.invoices.invoice-pdf.email'):</b>
            </p>
        </div>
    </div>
</html>
