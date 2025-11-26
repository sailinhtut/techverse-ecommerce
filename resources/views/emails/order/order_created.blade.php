@php
    $site_name = getParsedTemplate('site_name');
    $primaryColor = getParsedTemplate('site_primary_color');
    $primaryContentColor = getParsedTemplate('site_primary_content_color');
    $site_logo = getSiteLogoURL();
@endphp



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>

<body style="margin:0; padding:0; background:#f5f5f5; font-family:Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5; padding:20px 0;">
        <tr>
            <td align="center">


                <!-- MAIN CONTAINER -->
                <table width="650" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:10px; overflow:hidden;">

                    <!-- HEADER -->
                    <tr>
                        <td
                            style="background:{{ $primaryColor }}; color:{{ $primaryContentColor }}; padding:25px; text-align:center;">
                            <h2 style="margin:0; font-size:22px; font-weight:600;">Order Confirmation</h2>
                            <p style="margin:5px 0 0; font-size:14px; color:{{ $primaryContentColor }};">
                                Order {{ $order->order_number }}
                            </p>
                        </td>
                    </tr>

                    <!-- GREETING -->
                    <tr>
                        <td style="padding:30px;">
                            <p style="font-size:16px; margin:0 0 10px;">
                                Hi <strong>{{ $user->name }}</strong>,
                            </p>
                            <p style="font-size:14px; color:#555; margin:0 0 20px;">
                                Thank you for shopping with us! Your order has been successfully created.
                                Below is a summary of your purchase.
                            </p>
                        </td>
                    </tr>

                    <!-- ITEMS TABLE -->
                    <tr>
                        <td style="padding:0 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th align="left"
                                            style="padding:10px 0; font-size:14px; border-bottom:2px solid #eee;">Item
                                        </th>
                                        <th align="center"
                                            style="padding:10px 0; font-size:14px; border-bottom:2px solid #eee;">Price
                                        </th>
                                        <th align="center"
                                            style="padding:10px 0; font-size:14px; border-bottom:2px solid #eee;">Qty
                                        </th>
                                        <th align="right"
                                            style="padding:10px 0; font-size:14px; border-bottom:2px solid #eee;">Total
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($order->products as $item)
                                        <tr>
                                            <td style="padding:15px 0; border-bottom:1px solid #f0f0f0;">
                                                <div style="display:flex; gap:10px; align-items:center;">
                                                    <img src="{{ $item->product->image ? getDownloadableLink($item->product->image) : 'https://via.placeholder.com/60' }}"
                                                        width="60" height="60"
                                                        style="border-radius:6px; object-fit:cover;" alt="product">
                                                    <span style="font-size:14px; color:#333; font-weight:600; margin-left:10px;">
                                                        {{ $item->name }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td align="center" style="font-size:14px; color:#555;">
                                                {{ number_format($item->unit_price, 2) }} {{ $order->currency }}
                                            </td>

                                            <td align="center" style="font-size:14px; color:#555;">
                                                {{ $item->quantity }}
                                            </td>

                                            <td align="right" style="font-size:14px; color:#111; font-weight:600;">
                                                {{ number_format($item->subtotal, 2) }} {{ $order->currency }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <!-- ORDER SUMMARY -->
                    <tr>
                        <td style="padding:0 30px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                <tr>
                                    <td align="left" style="padding:6px 0; color:#555;">Subtotal</td>
                                    <td align="right" style="padding:6px 0; color:#111;">
                                        {{ number_format($order->subtotal, 2) }} {{ $order->currency }}
                                    </td>
                                </tr>

                                @if ($order->discount_total > 0)
                                    <tr>
                                        <td align="left" style="padding:6px 0; color:#555;">
                                            Discount ({{ $order->coupon_code }})
                                        </td>
                                        <td align="right" style="padding:6px 0; color:#16a34a; font-weight:bold;">
                                            -{{ number_format($order->discount_total, 2) }} {{ $order->currency }}
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td style="padding:6px 0; color:#555;">Tax</td>
                                    <td align="right" style="padding:6px 0; color:#111;">
                                        {{ number_format($order->tax_total, 2) }} {{ $order->currency }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:6px 0; color:#555;">Shipping</td>
                                    <td align="right" style="padding:6px 0; color:#111;">
                                        {{ number_format($order->shipping_total, 2) }} {{ $order->currency }}
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2" style="border-top:2px solid #eee; padding:10px 0;"></td>
                                </tr>

                                <tr>
                                    <td style="font-size:18px; font-weight:700; color:#111;">Grand Total</td>
                                    <td align="right" style="font-size:18px; font-weight:700; color:#111;">
                                        {{ number_format($order->grand_total, 2) }} {{ $order->currency }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 30px 40px; text-align: left;">
                            <a href="{{ route('order_detail.id.get', $order->id) }}"
                                style="
                                    display:inline-block;
                                    background: {{ $primaryColor }};
                                    color: {{ $primaryContentColor }};
                                    padding: 12px 24px;
                                    border-radius: 6px;
                                    text-decoration:none;
                                    font-size:14px;
                                    font-weight:600;
                               ">
                                View Order Details
                            </a>
                        </td>
                    </tr>


                    <!-- ADDRESSES -->
                    {{-- <tr>
                        <td style="padding:0 30px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0">

                                <tr>
                                    <td width="50%" valign="top">
                                        <h4 style="margin:0 0 10px; font-size:16px; color:#111;">Shipping Address</h4>
                                        <p style="margin:0; font-size:14px; color:#555; line-height:20px;">
                                            {{ $order->shipping_address['recipient_name'] }}<br>
                                            {{ $order->shipping_address['street_address'] }}<br>
                                            {{ $order->shipping_address['city'] }},
                                            {{ $order->shipping_address['state'] }}<br>
                                            {{ $order->shipping_address['postal_code'] }},
                                            {{ $order->shipping_address['country'] }}<br>
                                            Phone: {{ $order->shipping_address['phone'] }}
                                        </p>
                                    </td>

                                    <td width="50%" valign="top">
                                        <h4 style="margin:0 0 10px; font-size:16px; color:#111;">Billing Address</h4>
                                        <p style="margin:0; font-size:14px; color:#555; line-height:20px;">
                                            {{ $order->billing_address['recipient_name'] }}<br>
                                            {{ $order->billing_address['street_address'] }}<br>
                                            {{ $order->billing_address['city'] }},
                                            {{ $order->billing_address['state'] }}<br>
                                            {{ $order->billing_address['postal_code'] }},
                                            {{ $order->billing_address['country'] }}<br>
                                            Phone: {{ $order->billing_address['phone'] }}
                                        </p>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr> --}}

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#f3f4f6; padding:20px; text-align:center; font-size:12px; color:#6b7280;">
                            © {{ date('Y') }} {{ $site_name }} — All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>

</html>
