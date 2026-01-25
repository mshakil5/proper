<!doctype html>
<html lang="en" style="margin:0;padding:0;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Subscription Renewal Reminder</title>
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
                        <h1 style="margin:0; color:#ffffff; font-size:28px;">⏰ Renewal Reminder</h1>
                        <p style="margin:8px 0 0 0; color:#ffffff; font-size:16px;">Your Free Delivery Pass expires soon</p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:32px 24px 16px 24px;" class="p-32">
                        <p style="margin:0 0 16px 0; font-size:15px; line-height:1.6; color:#444;">
                            Hi {{ $user->first_name }},
                        </p>
                        
                        <p style="margin:0 0 24px 0; font-size:15px; line-height:1.6; color:#444;">
                            We wanted to remind you that your Free Delivery Pass will expire in <strong>{{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }}</strong>.
                        </p>

                        <!-- Expiry Box -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #fff3e0; border: 2px solid #ff9800; border-radius:8px; margin:24px 0;">
                            <tr>
                                <td style="padding:24px; text-align:center;">
                                    <p style="margin:0 0 12px 0; font-size:13px; color:#666; text-transform:uppercase;">Expires On</p>
                                    <p style="margin:0; font-size:24px; font-weight:bold; color:#ff9800;">
                                        {{ $subscription->ends_at->format('M d, Y') }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:24px 0; font-size:15px; line-height:1.6; color:#444;">
                            Don't miss out on unlimited free delivery! Renew your subscription now to continue enjoying free delivery on all your orders.
                        </p>

                        <div style="text-align:center; margin:24px 0;">
                            <a href="{{ url('/user/subscription') }}" style="display:inline-block; background-color:#ff8a00; color:#ffffff; padding:14px 40px; border-radius:6px; font-size:15px; font-weight:bold; text-decoration:none;">
                                Renew Subscription Now
                            </a>
                        </div>

                        <p style="margin:24px 0 0 0; font-size:13px; color:#999; border-top:1px solid #e8e8e8; padding-top:16px;">
                            Your Free Delivery Pass gives you:
                        </p>
                        <ul style="margin:12px 0 0 0; padding-left:20px; font-size:13px; color:#555; line-height:1.8;">
                            <li>Unlimited free delivery on all orders</li>
                            <li>No minimum spend required</li>
                            <li>Pay only £5 per month</li>
                            <li>Cancel anytime</li>
                        </ul>
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