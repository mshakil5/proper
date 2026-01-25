<!doctype html>
<html lang="en" style="margin:0;padding:0;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Order Confirmation</title>
    <style>
        body, table, td, a { margin:0; padding:0; border-collapse: collapse; }
        img { border:0; height:auto; line-height:100%; outline:none; text-decoration:none; }
        a { text-decoration:none; }
        body { height:100% !important; width:100% !important; font-family:Arial, Helvetica, sans-serif; background:#f8f8f8; }

        @media (max-width: 600px) {
            .container { width:100% !important; }
            .p-32 { padding:20px !important; }
        }
    </style>
</head>
<body style="background-color:#f8f8f8; margin:0; padding:0;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8f8f8;">
    <tr>
        <td align="center" style="padding:20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="container" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;border:none;">
                
                <!-- Header -->
                <tr>
                    <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:40px 24px; text-align:center;">
                        <h1 style="margin:0; color:#ffffff; font-size:28px;">✓ Order Confirmed!</h1>
                        <p style="margin:8px 0 0 0; color:#ffffff; font-size:16px;">Thank you for your order</p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:32px 24px 16px 24px;" class="p-32">
                        <p style="margin:0 0 16px 0; font-size:15px; line-height:1.6; color:#444;">
                            Hi {{ $order->first_name }},
                        </p>
                        
                        <p style="margin:0 0 24px 0; font-size:15px; line-height:1.6; color:#444;">
                            We've received your order and it's being prepared!
                        </p>

                        <!-- Order Details Box -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f9f9f9; border: 1px solid #e8e8e8; border-radius:8px; margin:24px 0;">
                            <tr>
                                <td style="padding:16px;">
                                    <p style="margin:0 0 12px 0; font-size:13px; font-weight:bold; color:#333;">Order Details</p>
                                    <table width="100%" style="font-size:12px; color:#555;">
                                        <tr>
                                            <td style="padding:4px 0;"><strong>Order Number:</strong></td>
                                            <td style="text-align:right; font-weight:bold; color:#ff8a00;">{{ $order->order_number }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0;"><strong>Order Date:</strong></td>
                                            <td style="text-align:right;">{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0;"><strong>Delivery Type:</strong></td>
                                            <td style="text-align:right;">{{ ucfirst($order->delivery_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0;"><strong>Estimated Time:</strong></td>
                                            <td style="text-align:right;">{{ $order->time }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Items -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:24px 0;">
                            <tr>
                                <td style="padding:12px 0; border-bottom:1px solid #e8e8e8; font-weight:bold; font-size:13px;">Items</td>
                            </tr>
                            @foreach($order->items as $item)
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #f5f5f5; font-size:13px;">
                                        <div style="margin-bottom:4px;">
                                            <strong>{{ $item->product_name }}</strong>
                                            <span style="color:#999;"> x{{ $item->quantity }}</span>
                                        </div>
                                        <div style="color:#666; font-size:12px;">£{{ number_format($item->price, 2) }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                        <!-- Totals -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:24px 0;">
                            <tr>
                                <td style="padding:8px 0; text-align:right; font-size:13px;">
                                    <strong>Subtotal:</strong> £{{ number_format($order->subtotal, 2) }}
                                </td>
                            </tr>
                            @if($order->delivery_charge > 0)
                                <tr>
                                    <td style="padding:8px 0; text-align:right; font-size:13px;">
                                        <strong>Delivery:</strong> £{{ number_format($order->delivery_charge, 2) }}
                                    </td>
                                </tr>
                            @endif
                            @if($order->coupon_discount > 0)
                                <tr>
                                    <td style="padding:8px 0; text-align:right; font-size:13px; color:#ff8a00;">
                                        <strong>Discount:</strong> -£{{ number_format($order->coupon_discount, 2) }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td style="padding:12px 0; text-align:right; border-top:2px solid #ff8a00; font-size:16px; font-weight:bold; color:#ff8a00;">
                                    Total: £{{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0 0 0; font-size:13px; color:#666; border-top:1px solid #e8e8e8; padding-top:16px;">
                            We'll notify you when your order is on the way. If you have any questions, please don't hesitate to contact us.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f8f8f8;padding:20px;font-size:12px;color:#555;text-align:center;">
                        <p style="margin:0;">&copy; {{ date('Y') }} Propertakeaways. All rights reserved.</p>
                        <p style="margin:4px 0 0 0;"><a href="https://www.propertakeaways.co.uk/" style="color:#FF6D33;text-decoration:none;">Visit Website</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>