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
    <title>Order Update</title>
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
                            <h2 style="margin:0; font-size:22px; font-weight:600;">Order Update</h2>

                            <p style="margin:5px 0 0; font-size:14px;">
                                Order #{{ $order->order_number }}
                            </p>

                            <p style="margin:0; font-size:13px; opacity:.8;">
                                Current Status: <strong>{{ ucfirst($order->status) }}</strong>
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
                                We wanted to let you know that your order has been updated.
                                Please review the latest details below.
                            </p>

                            @if (!empty($order->status_message))
                                <div
                                    style="background:#f3f4f6; padding:15px; border-radius:6px; font-size:14px; color:#333; margin-bottom:20px;">
                                    <strong>Update Message:</strong><br>
                                    {{ $order->status_message }}
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- ITEMS -->
                    <tr>
                        <td style="padding:0 30px 30px;">
                            <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
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

                                                    <div style="font-size:14px; color:#333; font-weight:600; margin-left:10px;">
                                                        {{ $item->name }}
                                                    </div>
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

                    <!-- SUMMARY -->
                    <tr>
                        <td style="padding:0 30px 30px;">
                            <table width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
                                <tr>
                                    <td style="padding:6px 0; color:#555;">Subtotal</td>
                                    <td align="right" style="padding:6px 0; color:#111;">
                                        {{ number_format($order->subtotal, 2) }} {{ $order->currency }}
                                    </td>
                                </tr>

                                @if ($order->discount_total > 0)
                                    <tr>
                                        <td style="padding:6px 0; color:#555;">Discount ({{ $order->coupon_code }})
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
