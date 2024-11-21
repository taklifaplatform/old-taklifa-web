<!DOCTYPE html>
<html lang="ar">
    <div style="margin-top: 10px; margin-bottom: 10px;">
        <div style="margin-bottom: 0;">
            <p style="margin: 0;">
                {{ $address->name }}
                &nbsp;&nbsp; &nbsp; <b>الاستاذ(ة):</b>
            </p>
            <p style="margin: 0;">
                {{ $address->city }}
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
