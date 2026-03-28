<!doctype html>
<html lang="en" style="margin:0;padding:0;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Welcome to Propertakeaways</title>
    <style>
        @media screen and (max-width:600px) {
            table[class="wrapper"] {
                width: 100% !important;
            }

            td[class="padding"] {
                padding: 12px 20px !important;
            }

            td[class="point-cell"] {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box;
                margin-bottom: 10px;
            }

            a[class="cta-btn"] {
                width: 100% !important;
                padding: 12px 0 !important;
                font-size: 16px !important;
            }
        }
    </style>
</head>

<body style="margin:0;padding:0;background-color:#f0f0f0;font-family:Arial,Helvetica,sans-serif;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f0f0f0">
        <tr>
            <td align="center" style="padding:40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                    style="max-width:580px;" class="wrapper">

                    <tr>
                        <td bgcolor="#ff5a00"
                            style="padding:48px 40px 40px 40px;text-align:center;border-radius:16px 16px 0 0;">
                            <p style="margin:0 0 16px 0;font-size:48px;line-height:1;">🎉</p>
                            <h1
                                style="margin:0;color:#ffffff;font-size:28px;font-weight:800;letter-spacing:0.5px;line-height:1.3;">
                                Welcome to Propertakeaways!</h1>
                            <p style="margin:12px 0 0 0;color:#ffffff;font-size:15px;line-height:1.6;">We're so glad
                                you're here. Your account is live and ready.</p>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#ffffff" style="padding:36px 40px 0 40px;" class="padding">
                            <p style="margin:0;font-size:17px;color:#111111;font-weight:800;">Hi
                                {{ $user->first_name }}! 👋</p>
                            <p style="margin:10px 0 0 0;font-size:14px;color:#555555;line-height:1.8;">
                                Thank you for creating your account. You've earned <strong style="color:#ff5a00;">500
                                    bonus points</strong> just for registering! You're now part of our rewards community
                                — every time you shop, share, or refer, you earn even more points.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#ffffff" style="padding:28px 40px 16px 40px;" class="padding">
                            <table rorgb(15, 15, 15)sentation" width="100%" cellspacing="0" cellpadding="0"
                                border="0">
                                <tr>
                                    <td style="border-top:2px dashed #ffe0cc;"></td>
                                    <td style="white-space:nowrap;padding:0 14px;">
                                        <span
                                            style="background-color:#fff5f0;border:1.5px solid #ffd5c0;color:#ff5a00;font-size:13px;font-weight:700;padding:6px 18px;border-radius:30px;">&#x1F525;
                                            How to Earn Points</span>
                                    </td>
                                    <td style="border-top:2px dashed #ffe0cc;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @php
                        $points = [
                            [
                                'emoji' => '🛒',
                                'title' => 'Place an Order',
                                'points' => '+Points per £1',
                                'desc' => 'Spend £200 → Earn 200 points instantly',
                            ],
                            [
                                'emoji' => '🎁',
                                'title' => 'Register Bonus',
                                'points' => '+500 Points',
                                'desc' => 'Get 500 bonus points just for creating your account',
                            ],
                            [
                                'emoji' => '👥',
                                'title' => 'Refer a Friend',
                                'points' => 'Both Earn 50 pts',
                                'desc' => 'You earn 50 pts and your friend earns 50 pts too',
                            ],
                            [
                                'emoji' => '⭐',
                                'title' => 'Review Us on Google',
                                'points' => '+100 Points',
                                'desc' => 'Leave a review & send us a screenshot to claim',
                            ],
                        ];
                    @endphp

                    @foreach ($points as $point)
                        <tr>
                            <td bgcolor="#ffffff" style="padding:6px 40px;" class="padding">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                    bgcolor="#fdf6f0" style="border-radius:12px;border-left:4px solid #ff5a00;">
                                    <tr>
                                        <td width="54" bgcolor="#fdf6f0"
                                            style="padding:14px 0 14px 14px;vertical-align:middle;" class="point-cell">
                                            <table cellspacing="0" cellpadding="0" border="0">
                                                <tr>
                                                    <td bgcolor="#ff5a00"
                                                        style="width:40px;height:40px;border-radius:10px;text-align:center;line-height:40px;font-size:20px;padding:0 8px;">
                                                        {{ $point['emoji'] }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td bgcolor="#fdf6f0" style="padding:14px 14px 14px 12px;vertical-align:middle;"
                                            class="point-cell">
                                            <p style="margin:0;font-size:14px;font-weight:700;color:#111111;">
                                                {{ $point['title'] }}<br>
                                            <table cellspacing="0" cellpadding="0" border="0"
                                                style="display:inline-table;margin-top:4px;">
                                                <tr>
                                                    <td bgcolor="#ff5a00"
                                                        style="border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;color:#ffffff;white-space:nowrap;">
                                                        {{ $point['points'] }}
                                                    </td>
                                                </tr>
                                            </table>
                                            </p>
                                            <p style="margin:5px 0 0 0;font-size:13px;color:#666666;">
                                                {{ $point['desc'] }}</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td bgcolor="#ffffff" style="padding:32px 20px;text-align:center;" class="padding">
                            <table cellspacing="0" cellpadding="0" border="0" align="center">
                                <tr>
                                    <td bgcolor="#ff5a00" style="border-radius:50px;">
                                        <a href="{{ route('menu') }}" class="cta-btn"
                                            style="display:inline-block;background-color:#ff5a00;color:#ffffff;font-size:16px;font-weight:800;padding:15px 30px;border-radius:50px;text-decoration:none;letter-spacing:0.4px;">
                                            🍽️ Go Order Now
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:16px 0 0 0;font-size:13px;color:#999999;">
                                Browse the menu and start earning points on your first order!
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#fafafa"
                            style="padding:22px 40px;text-align:center;border-top:1px solid #f0f0f0;border-radius:0 0 16px 16px;"
                            class="padding">
                            <p style="margin:0;font-size:12px;color:#999999;">&copy; {{ date('Y') }}
                                Propertakeaways. All rights reserved.</p>
                            <p style="margin:6px 0 0 0;">
                                <a href="https://www.propertakeaways.co.uk/"
                                    style="color:#ff5a00;text-decoration:none;font-size:12px;font-weight:700;">Visit
                                    Website</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>