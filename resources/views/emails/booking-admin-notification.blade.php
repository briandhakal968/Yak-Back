<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Received</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; }
        body { background-color: #f5f5f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { width: 100%; background-color: #f5f5f5; padding: 32px 16px; }
        .container { max-width: 560px; margin: 0 auto; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 32px 32px 24px; text-align: center; }
        .logo { display: inline-block; width: 56px; height: 56px; line-height: 56px; background-color: rgba(255,255,255,0.15); border-radius: 12px; color: #fff; font-size: 28px; font-weight: 700; margin-bottom: 16px; }
        .title { color: #fff; font-size: 22px; font-weight: 700; margin: 0; }
        .subtitle { color: rgba(255,255,255,0.8); font-size: 14px; margin: 6px 0 0 0; }
        .body { padding: 36px 32px; color: #1f2937; font-size: 16px; line-height: 1.6; }
        .greeting { margin: 0 0 16px 0; font-size: 18px; font-weight: 600; color: #0b1535; }
        .text { margin: 0 0 20px 0; }
        .details-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 20px 24px; margin: 24px 0; }
        .details-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #166534; margin: 0 0 14px 0; }
        .cta-btn { display: inline-block; background: #059669; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 14px; margin: 20px 0; }
        .divider { border: 0; border-top: 1px solid #e5e7eb; margin: 28px 0; }
        .footer { background-color: #f9fafb; padding: 24px 32px; text-align: center; color: #6b7280; font-size: 13px; line-height: 1.5; }
        .footer-brand { color: #0b1535; font-weight: 700; font-size: 14px; margin: 0 0 6px 0; }
        .footer-note { color: #9ca3af; font-size: 11px; margin: 0; }
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 16px 8px; }
            .body { padding: 28px 20px; }
            .header { padding: 28px 20px 20px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr><td align="center">
                <div class="container">
                    <div class="header">
                        <div class="logo">&#128203;</div>
                        <h1 class="title">New Booking</h1>
                        <p class="subtitle">Action required — review and confirm</p>
                    </div>
                    <div class="body">
                        <p class="greeting">Hello Admin,</p>
                        <p class="text">A new booking has been submitted on Yakspirit Adventures. Please review the details below and follow up with the customer.</p>

                        <div class="details-box">
                            <p class="details-title">Booking Details</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                <tr><td style="padding:6px 0;color:#6b7280;">Tour</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $tourTitle }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Date</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $date }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Passengers</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $passengers }}</td></tr>
                                <tr><td style="padding:6px 0;border-top:1px solid #e5e7eb;" colspan="2"></td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Price per person</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $priceCurrency }} {{ number_format($pricePerPerson) }}{{ !empty($priceLabel) ? ' (' . $priceLabel . ')' : '' }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">&times; {{ $passengers }} traveller{{ $passengers > 1 ? 's' : '' }}</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $priceCurrency }} {{ number_format($totalPrice) }}</td></tr>
                                <tr><td style="padding:6px 0;border-top:2px solid #0b1535;" colspan="2"></td></tr>
                                <tr><td style="padding:6px 0;color:#0b1535;font-weight:700;">Total</td><td style="padding:6px 0;color:#0b1535;font-weight:700;text-align:right;">{{ $priceCurrency }} {{ number_format($totalPrice) }}</td></tr>
                            </table>
                        </div>

                        <div class="details-box">
                            <p class="details-title">Contact Details</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                <tr><td style="padding:6px 0;color:#6b7280;">Name</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $fullName }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Email</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $customerEmail }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Phone</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $phone }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Country</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $country }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">City</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $city }}</td></tr>
                                @if(!empty($specialRequests))
                                <tr><td style="padding:6px 0;border-top:1px solid #e5e7eb;" colspan="2"></td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;vertical-align:top;">Special Requests</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $specialRequests }}</td></tr>
                                @endif
                            </table>
                        </div>

                        <p class="text" style="margin-bottom:0;">Please review this booking in the admin dashboard and update the status accordingly.</p>
                    </div>
                    <div class="footer">
                        <p class="footer-brand">Yakspirit Adventures</p>
                        <p class="footer-note">&copy; {{ date('Y') }} Yakspirit Adventures. All rights reserved.</p>
                    </div>
                </div>
            </td></tr>
        </table>
    </div>
</body>
</html>
