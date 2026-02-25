<!doctype html>
<html lang="en" style="margin:0;padding:0;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Welcome to Propertakeaways</title>
    <style>
        /* Mobile responsiveness */
        @media screen and (max-width:600px){
            table[class="wrapper"] { width:100% !important; }
            td[class="padding"] { padding:12px 20px !important; }
            td[class="point-cell"] { display:block !important; width:100% !important; box-sizing:border-box; margin-bottom:10px; }
            a[class="cta-btn"] { width:100% !important; padding:12px 0 !important; font-size:16px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f0f0f0;font-family:Arial,Helvetica,sans-serif;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f0f0f0;">
    <tr>
        <td align="center" style="padding:40px 20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width:580px;" class="wrapper">

                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#ff8a00,#ff5a00);padding:48px 40px 40px 40px;text-align:center;border-radius:16px 16px 0 0;">
                        <p style="margin:0 0 16px 0;font-size:48px;line-height:1;">🎉</p>
                        <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:800;letter-spacing:0.5px;line-height:1.3;">Welcome to Propertakeaways!</h1>
                        <p style="margin:12px 0 0 0;color:rgba(255,255,255,0.85);font-size:15px;line-height:1.6;">We're so glad you're here. Your account is live and ready.</p>
                    </td>
                </tr>

                <!-- White body -->
                <tr>
                    <td style="background:#ffffff;padding:36px 40px 0 40px;" class="padding">
                        <p style="margin:0;font-size:17px;color:#111;font-weight:800;">Hi {{ $user->first_name }}! 👋</p>
                        <p style="margin:10px 0 0 0;font-size:14px;color:#777;line-height:1.8;">
                            Thank you for creating your account. You're now part of our rewards community — every time you shop, share, or refer, you earn points you can use on future orders.
                        </p>
                    </td>
                </tr>

                <!-- Divider label -->
                <tr>
                    <td style="background:#ffffff;padding:28px 40px 16px 40px;" class="padding">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td style="border-top:2px dashed #ffe0cc;"></td>
                                <td style="white-space:nowrap;padding:0 14px;">
                                    <span style="background:#fff5f0;border:1.5px solid #ffd5c0;color:#ff5a00;font-size:13px;font-weight:700;padding:6px 18px;border-radius:30px;">&#x1F525; How to Earn Points</span>
                                </td>
                                <td style="border-top:2px dashed #ffe0cc;"></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Point Sections -->
                @php
                    $points = [
                        ['emoji'=>'🛒','title'=>'Place an Order','points'=>'+Points per £1','desc'=>'Spend £200 → Earn 200 points instantly'],
                        ['emoji'=>'🎁','title'=>'First Order Bonus','points'=>'+500 Points','desc'=>'Get 500 bonus points just for placing your first order'],
                        ['emoji'=>'👥','title'=>'Refer a Friend','points'=>'Both Earn 50 pts','desc'=>'You earn 50 pts and your friend earns 50 pts too'],
                        ['emoji'=>'⭐','title'=>'Review Us on Google','points'=>'+100 Points','desc'=>'Leave a review & send us a screenshot to claim'],
                    ];
                @endphp

                @foreach($points as $point)
                <tr>
                    <td style="background:#ffffff;padding:6px 40px;" class="padding">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="14" border="0" style="background:#fdf6f0;border-radius:12px;border-left:4px solid #ff5a00;">
                            <tr>
                                <td width="46" style="padding:14px 0 14px 14px;vertical-align:middle;" class="point-cell">
                                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#ff8a00,#ff5a00);border-radius:10px;text-align:center;line-height:40px;font-size:20px;">{{ $point['emoji'] }}</div>
                                </td>
                                <td style="padding:14px 14px 14px 12px;vertical-align:middle;" class="point-cell">
                                    <p style="margin:0;font-size:14px;font-weight:700;color:#111;">
                                        {{ $point['title'] }} <br>
                                        <span style="background:linear-gradient(135deg,#ff8a00,#ff5a00);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $point['points'] }}</span>
                                    </p>
                                    <p style="margin:5px 0 0 0;font-size:13px;color:#888;">{{ $point['desc'] }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach

                <!-- CTA -->
                <tr>
                    <td style="background:#ffffff;padding:32px 20px;text-align:center;" class="padding">
                        <a href="{{ route('menu') }}" class="cta-btn"
                           style="display:inline-block;max-width:100%;box-sizing:border-box;width:auto;background:linear-gradient(135deg,#ff8a00,#ff5a00);color:#ffffff;font-size:16px;font-weight:800;padding:15px 30px;border-radius:50px;text-decoration:none;letter-spacing:0.4px;">
                            🍽️ Go Order Now
                        </a>
                        <p style="margin:16px 0 0 0;font-size:13px;color:#aaa;">
                            Browse the menu and start earning points on your first order!
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#fafafa;padding:22px 40px;text-align:center;border-top:1px solid #f0f0f0;border-radius:0 0 16px 16px;" class="padding">
                        <p style="margin:0;font-size:12px;color:#bbb;">&copy; {{ date('Y') }} Propertakeaways. All rights reserved.</p>
                        <p style="margin:6px 0 0 0;">
                            <a href="https://www.propertakeaways.co.uk/" style="color:#ff5a00;text-decoration:none;font-size:12px;font-weight:700;">Visit Website</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>