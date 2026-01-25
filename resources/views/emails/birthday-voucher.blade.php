<!doctype html>
<html lang="en" style="margin:0;padding:0;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Happy Birthday!</title>
    <style>
        body, table, td, a { margin:0; padding:0; border-collapse: collapse; }
        img { border:0; height:auto; line-height:100%; outline:none; text-decoration:none; }
        a { text-decoration:none; }
        body { height:100% !important; width:100% !important; font-family:Arial, Helvetica, sans-serif; background:#f8f8f8; }

        @media (max-width: 600px) {
            .container { width:100% !important; }
            .stack { display:block !important; width:100% !important; }
            .p-32 { padding:20px !important; }
            .text-center-sm { text-align:center !important; }
            .btn { display:block !important; width:100% !important; text-align:center !important; }
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
                        <h1 style="margin:0; color:#ffffff; font-size:32px;">🎉 Happy Birthday!</h1>
                        <p style="margin:8px 0 0 0; color:#ffffff; font-size:16px;">{{ $user->first_name }}, we have a special gift for you</p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:32px 24px 16px 24px;" class="p-32">
                        <p style="margin:0 0 16px 0; font-size:15px; line-height:1.6; color:#444;">
                            Hi {{ $user->first_name }},
                        </p>
                        
                        <p style="margin:0 0 24px 0; font-size:15px; line-height:1.6; color:#444;">
                            On your special day, we want to thank you for being a valued customer! 🎁
                        </p>

                        <!-- Voucher Box -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #fff9e6; border: 2px solid #ff8a00; border-radius:8px; margin:24px 0;">
                            <tr>
                                <td style="padding:24px; text-align:center;">
                                    <p style="margin:0 0 12px 0; font-size:13px; color:#666; text-transform:uppercase;">Your Birthday Voucher</p>
                                    <p style="margin:0 0 12px 0; font-size:28px; font-weight:bold; color:#ff8a00;">
                                        @if($voucher->discount_type === 'percent')
                                            {{ $voucher->discount_value }}%
                                        @else
                                            £{{ number_format($voucher->discount_value, 2) }}
                                        @endif
                                    </p>
                                    <p style="margin:0 0 16px 0; font-size:14px; color:#333; font-weight:bold;">{{ $voucher->name }}</p>
                                    <p style="margin:0; font-size:13px; color:#666;">Valid until {{ $voucher->end_date->format('d M, Y') }}</p>
                                </td>
                            </tr>
                        </table>

                        <!-- Coupon Code -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f8f8f8; border-radius:8px; margin:20px 0;">
                            <tr>
                                <td style="padding:16px; text-align:center;">
                                    <p style="margin:0 0 8px 0; font-size:12px; color:#666;">Use Code:</p>
                                    <p style="margin:0; font-size:22px; font-weight:bold; color:#ff8a00; font-family:monospace; letter-spacing:2px;">{{ $voucher->code }}</p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0 0 0; font-size:15px; line-height:1.6; color:#444;">
                            Simply use code <strong>{{ $voucher->code }}</strong> at checkout to claim your discount.
                        </p>

                        <!-- Terms Box -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f0f0f0; border-left: 4px solid #ff8a00; border-radius:6px; margin:20px 0;">
                            <tr>
                                <td style="padding:16px;">
                                    <p style="margin:0 0 8px 0; font-size:12px; font-weight:bold; color:#333;">Terms & Conditions:</p>
                                    <ul style="margin:0; padding-left:20px; font-size:12px; color:#555; line-height:1.6;">
                                        <li>This voucher can only be used <strong>once per account</strong></li>
                                        <li>Valid for customers with <strong>3+ orders</strong> in the last year</li>
                                        <li>Your last order must be <strong>within the last 12 months</strong></li>
                                        <li>Cannot be combined with other offers</li>
                                    </ul>
                                </td>
                            </tr>
                        </table>

                        @if($voucher->min_order_amount > 0)
                            <p style="margin:12px 0 0 0; font-size:13px; color:#666;">
                                <em>Minimum order: £{{ number_format($voucher->min_order_amount, 2) }}</em>
                            </p>
                        @endif
                    </td>
                </tr>

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