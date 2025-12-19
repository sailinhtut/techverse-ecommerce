@php
    $site_name = getParsedTemplate('site_name');
    $site_description = getParsedTemplate('site_description');
    $site_logo = getSiteLogoURL();
    $site_address = getParsedTemplate('site_address');
    $site_phone_1 = getParsedTemplate('site_phone_1');
    $site_phone_2 = getParsedTemplate('site_phone_2');
    $site_contact_email = getParsedTemplate('site_contact_email');
    $site_primary_color = getParsedTemplate('site_primary_color');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100%;
            width: 100%;
        }

        .container {
            margin: 0 !important;
            padding: 0 !important;
            width: 100%;
        }
    </style>
</head>

<body style="margin:0; padding:0; font-family:sans-serif;">

    <table width="100%" style="overflow:hidden;">
        <tr>
            <td style="padding:30px 30px;;">
                <table width="100%">
                    <tr>
                        <td align="left" style="font-size:20px;">
                           
                            <img src="{{ $base64Image }}" alt="logo" style="height:45px; object-fit:contain;">

                            <div style="font-weight:bold;color:{{ $site_primary_color }};margin-top:10px;">{{ $site_name }}</div>
                            <div style="font-size:10px; font-weight:normal;">
                                {{ $site_description }}
                            </div>
                        </td>
                        <td align="center" style="width:40%;"></td>
                        <td align="right" style="font-size:15px;vertical-align: top;">
                            <div style="font-weight:bold">Order</div>
                            <div style="font-size:14px; font-weight:normal;">
                                ID: {{ $order->id }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding:0 30px;; font-size:14px;">
                <table style="padding:10px 0;width:100%;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;">
                    <tr>
                        <td align="left">Date: {{ $invoice->created_at->format('d/m/Y') }}</td>
                        <td align="right">Invoice No: {{ $invoice->id }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- {{ $order->shipping_address['recipient_name'] }}<br>
        {{ $order->shipping_address['street_address'] }}<br>
        {{ $order->shipping_address['city'] }},
        {{ $order->shipping_address['state'] }}<br>
        {{ $order->shipping_address['postal_code'] }},
        {{ $order->shipping_address['country'] }}<br>
        {{ $order->shipping_address['phone'] }} --}}

        <tr>
            <td style="padding:15px 30px;; font-size:14px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="left">
                            <div style="">Invoice To:</div>
                            <div style="font-size:10px;">{{ $order->shipping_address['recipient_name'] }}</div>
                            <div style="font-size:10px;">{{ $order->shipping_address['street_address'] }}</div>
                            <div style="font-size:10px;">{{ $order->shipping_address['phone'] }}</div>
                        </td>
                        <td align="center" style="width:20%;"></td>
                        <td align="right">
                            <div style="">Pay To:</div>
                            <div style="font-size:10px;">{{ $site_name }}</div>
                            <div style="font-size:10px;">{{ $site_address }}</div>
                            <div style="font-size:10px;">{{ $site_contact_email }}</div>
                            <div style="font-size:10px;">{{ $site_phone_1 }}</div>
                            <div style="font-size:10px;">{{ $site_phone_2 }}</div>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding:15px 30px;font-size:14px;">
                <table width="100%" style="border-collapse:collapse;">
                    <thead class="">
                        <tr style="border:1px solid #e5e7eb;background-color:#f9fafb;">
                            <td align="left" style="padding:10px 0; padding-left:15px;">Item</td>
                            <td align="center" style="padding:10px 0;">Price</td>
                            <td align="center" style="padding:10px 0;">Qty</td>
                            <td align="right" style="padding:10px 0; padding-right:15px;">Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->products as $item)
                            <tr style="border:1px solid #f0f0f0;">
                                <td style="padding:15px 0; padding-left:15px;">
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            {{-- <td>
                                                <img src="{{ $item->product->image ? getDownloadableLink($item->product->image) : 'https://via.placeholder.com/60' }}"
                                                    width="60" height="60"
                                                    style="border-radius:6px; object-fit:cover;">
                                            </td> --}}
                                            <td style="">
                                                {{ $item->name }}
                                                @if ($item->sku)
                                                    <div style="font-size:12px;">SKU:
                                                        {{ $item->sku }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <td align="center">
                                    {{ number_format($item->unit_price, 2) }} {{ $order->currency }}
                                </td>

                                <td align="center">
                                    {{ $item->quantity }}
                                </td>

                                <td align="right" style="color:#111; padding-right:15px;">
                                    {{ number_format($item->subtotal, 2) }} {{ $order->currency }}
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </td>
        </tr>

        <!-- SUMMARY -->
        <tr>
            <td style="padding:15px 30px;font-size:14px;" align="right">
                <table width="40%" cellpadding="0" cellspacing="0" align="right">
                    <tr>
                        <td style="padding:6px 0;">Subtotal</td>
                        <td align="right" style="padding:6px 0;">
                            {{ number_format($invoice->subtotal, 2) }} {{ $order->currency }}
                        </td>
                    </tr>

                    @if ($invoice->discount_total > 0)
                        <tr>
                            <td style="padding:6px 0;">
                                Discount ({{ $order->coupon_code }})
                            </td>
                            <td align="right" style="padding:6px 0;">
                                -{{ number_format($invoice->discount_total, 2) }} {{ $order->currency }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:6px 0;">Tax</td>
                        <td align="right">
                            {{ number_format($invoice->tax_total, 2) }} {{ $order->currency }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:6px 0; padding-bottom:10px;">Shipping</td>
                        <td align="right" style="padding-bottom:10px;">
                            {{ number_format($invoice->shipping_total, 2) }} {{ $order->currency }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="border-top:1px solid #e5e7eb;"></td>
                    </tr>

                    <tr>
                        <td style="padding-top: 10px;">Grand Total</td>
                        <td align="right" style="padding-top:10px;">
                            {{ number_format($invoice->grand_total, 2) }} {{ $order->currency }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ADDRESSES -->
        {{-- <tr>
            <td style="padding:0 30px 30px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="50%" valign="top">
                            <h4 style="margin-bottom:8px; font-size:16px; color:#111;">Billing Address</h4>
                            <p style="font-size:13px; color:#555; line-height:20px; margin:0;">
                                {{ $order->billing_address['recipient_name'] }}<br>
                                {{ $order->billing_address['street_address'] }}<br>
                                {{ $order->billing_address['city'] }},
                                {{ $order->billing_address['state'] }}<br>
                                {{ $order->billing_address['postal_code'] }},
                                {{ $order->billing_address['country'] }}<br>
                                {{ $order->billing_address['phone'] }}
                            </p>
                        </td>

                        <td width="50%" valign="top">
                            <h4 style="margin-bottom:8px; font-size:16px; color:#111;">Shipping Address</h4>
                            <p style="font-size:13px; color:#555; line-height:20px; margin:0;">
                                {{ $order->shipping_address['recipient_name'] }}<br>
                                {{ $order->shipping_address['street_address'] }}<br>
                                {{ $order->shipping_address['city'] }},
                                {{ $order->shipping_address['state'] }}<br>
                                {{ $order->shipping_address['postal_code'] }},
                                {{ $order->shipping_address['country'] }}<br>
                                {{ $order->shipping_address['phone'] }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr> --}}

        <!-- CTA BUTTON -->
        {{-- <tr>
            <td style="padding:0 30px 40px; text-align:center;">
                <a href="{{ route('order_detail.id.get', $order->id) }}"
                    style="
                        background:{{ $primaryColor }};
                        color:{{ $primaryContentColor }};
                        padding:14px 28px;
                        border-radius:6px;
                        font-size:15px;
                        font-weight:700;
                        text-decoration:none;
                        display:inline-block;
                    ">
                    View Order Online
                </a>
            </td>
        </tr> --}}

        <!-- FOOTER -->
        <tr>
            <td style="padding:20px;padding-top:100px; text-align:center; font-size:12px; color:#777;">
                © {{ date('Y') }} {{ $site_name }}. All rights reserved.
            </td>
        </tr>

    </table>

</body>

</html>
