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
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title direction="rtl">عرض الأسعار</title>
    <style>
        body {
            font-family: 'IBM Plex Sans Arabic', 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
        }

        .rtl-text {
            text-align: right;
            direction: rtl;
            font-size: 12pt;
            margin: 0;
        }

        .rounded-table {
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
        }

        .table-style {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            overflow: hidden;
        }

        .price {
            font-size: 12pt;
            font-weight: bold;
            color: #14532d;
        }

        .footer-section {
            margin-top: 20px;
            background-color: #EFF7F3;
            border-radius: 10px;
            overflow: hidden;
            font-weight: bold;
            padding: 5px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }

        th,
        td {
            border: 1px solid #ddd;
            text-align: right;
            word-wrap: break-word;
        }

        .color:nth-child(even) {
            background-color: #EFF7F3;
        }

        /* Fixed width for table columns */
        th:nth-child(1),
        td:nth-child(1) {
            width: 20%;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 15%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 20%;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 35%;
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 40%;
        }

        img.product-image {
            width: 50px;
            height: 40px;
        }
    </style>
</head>

<body>
    <div>
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
                        <h1 style="color: #06593A; font-size: 24pt; margin: 0;">عرض الأسعار</h1>
                    </td>
                    <td colspan="7" style="border: none; padding: 0;">
                        @if ($base64Image)
                            <img src="{{ $base64Image }}" style="width: 110px; height: auto;">
                        @endif
                    </td>
                </tr>

                <tr>
                    <td colspan="3" style="border: none; padding-bottom: 32pt;">
                        <p style="margin: 0;">#{{ $order->id }}
                            <span class="price">
                                &nbsp;&nbsp; رقم عـــرض الاســـعار:
                            </span>
                        </p>
                        <p style="margin: 0;">
                            {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d/m/Y') }}
                            <span class="price">
                                &nbsp;&nbsp; تاريخ عرض الاســـعار:
                            </span>
                        </p>
                        <div>
                            <br />
                            <br />
                            <br />
                            <br />
                        </div>
                    </td>
                    <td colspan="7" style="border: none; text-align: right; padding: 0;">
                        <div style="padding-top: 12pt">
                            <h4 class="rtl-text" style="color: #14532d;">مؤسسة حكيم التميز للمقاولات العامة</h4>
                            <p class="rtl-text"> 1010890528 &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;<b> س - ت:</b></p>
                            <p class="rtl-text"> 311619590800003 <b>الرقم الضريبي:</b></p>
                            <p class="rtl-text">الرياض حي قرطبة، الرياض، المملكة العربية السعودية
                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; <b> العنوان:</b>
                            </p>
                            <p class="rtl-text"> taklifa@taklifa.com
                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; <b> الأيميل:</b>
                            </p>
                            <p class="rtl-text"> 0531199908
                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; <b> التواصل:</b>
                            </p>
                            <br>
                            <div style="border-bottom: 1px dashed #000; margin-top: 10px;"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <table style="width: 100%; padding-top: 16pt; margin-bottom: 40px">
        <tr>
            <td colspan="2" style="border: none;">
            </td>
            <td colspan="10" style="border: none;">
                <div style="margin-bottom: 10px;">
                    <span style="font-weight: bold; margin-bottom: 0; color: #14532d;">إلى </span>
                    @foreach ($order->addresses as $address)
                        <div class="rtl-text" style="margin-bottom: 0;">
                            <p style="margin: 0;"> {{ $address->name }}
                                &nbsp;&nbsp; &nbsp;&nbsp; <b> الاستاذ(ة):</b>
                            </p>
                            <p style="margin: 0;"> {{ $address->country }} , {{ $address->city }}
                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; <b> العنوان:</b>
                            </p>
                            <p style="margin: 0;">{{ $address->phone }}
                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; <b> التواصل:</b>
                            </p>
                            <p style="margin: 0;">{{ $address->email }}
                                &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; <b> الأيميل:</b>
                            </p>
                        </div>
                    @endforeach
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%" class="table-style">
        <thead style="background-color: #06593a;">
            <tr style="color: #FFFFFF;">
                <th style="padding: 3px; text-align: center; height: 40px">المجموع</th>
                <th style="padding: 3px; text-align: center">الكمية</th>
                <th style="padding: 3px; text-align: center">سعر الوحدة</th>
                <th style="padding: 3px; text-align: center">الوصف</th>
                <th style="padding: 3px; text-align: center">البند</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                @php
                    $base64Image = null;
                    $image = $item->product->images->first();
                    if ($image) {
                        $path = storage_path('app/public/' . $image->path);
                        if (file_exists($path)) {
                            $base64Image = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
                        }
                    }
                @endphp
                <tr class="color">
                    <td style="padding: 3px; text-align: center">{{ number_format($item->total, 2) }}</td>
                    <td style="padding: 3px; text-align: center">{{ $item->qty_ordered }}</td>
                    <td style="padding: 3px; text-align: center">{{ number_format($item->price, 2) }}</td>
                    <td style="padding: 3px;">
                        @php
                            $descriptions = strip_tags(html_entity_decode($item->product->short_description));
                        @endphp

                        @foreach (splitParagraphIntoChunks($descriptions) as $desc)
                            <p style="margin: 0; text-align: center">{{ $desc }}</p>
                        @endforeach
                    </td>

                    <td style="padding: 3px;">
                        <table>
                            <tr>
                                @php
                                    $items = strip_tags(html_entity_decode($item->name));
                                @endphp
                                @foreach (splitParagraphIntoChunks($items) as $item)
                                    <p style="margin: 0;  text-align: center;">{{ $item }}</p>
                                @endforeach
                                <td style="border: none">
                                    @if ($base64Image)
                                        <img class="product-image" src="{{ $base64Image }}" alt="Product Image">
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <table style="width: 102%" class="rounded-table">
        <tbody style="font-size: 12pt; font-weight: bold">
            <tr>
                <td colspan="9" style="padding: 6px; text-align: center">
                    <span>ر.س</span>
                    {{ number_format($order->sub_total, 2) }}
                </td>
                <td colspan="5" style="padding: 6px; text-align: center">
                    المجموع
                </td>
                <td style="border: none"></td>
                <td style="border: none"></td>
            </tr>
            <tr>
                <td colspan="9" style="padding: 6px; text-align: center">
                    <span>ر.س</span>
                    {{ number_format($order->tax_amount, 2) }}
                </td>
                <td colspan="5" style="padding: 6px; text-align: center">القيمة المضافة <br>
                    <span style="text-align: center">(15%)</span>
                </td>
                <td style="border: none"></td>
                <td style="border: none"></td>
            </tr>
            <tr style="font-size: 16pt;">
                <td colspan="9" style="padding: 6px; text-align: center; color:#14532d; background-color: #DCEFE7; ">
                    <span>ر.س</span>
                    {{ number_format($order->grand_total, 2) }}
                </td>
                <td colspan="5" style="padding: 6px; text-align: center; background-color: #DCEFE7; ">
                    الإجمالي
                </td>
                <td style="border: none"></td>
                <td style="border: none"></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer section -->
    {{-- <table class="footer-section">
        <tbody>
            <tr>
                <td style="border: none; padding-right: 10pt;">
                    <span>يسرنا أن نقدم لكم عرض السعر آملين أن ينال عرضنا القبول.</span>
                    <br />
                    <span>تم إنشاء عرض السعر من منصة تكلفة التابعة لمؤسسة حكيم التميز للمقاولات</span>
                </td>
            </tr>
        </tbody>
    </table> --}}

</body>

</html>
