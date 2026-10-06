<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Plan Your Trip Inquiry</title>
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
                        <div class="logo">&#127796;</div>
                        <h1 class="title">New Trip Inquiry</h1>
                        <p class="subtitle">A customer wants to plan a trip</p>
                    </div>
                    <div class="body">
                        <p class="greeting">Hello Admin,</p>
                        <p class="text">A new Plan Your Trip inquiry has been submitted. Review the customer's preferences and prepare a custom itinerary.</p>

                        <div class="details-box">
                            <p class="details-title">Customer Info</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                <tr><td style="padding:6px 0;color:#6b7280;">Name</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $fullName }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Email</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $email }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Phone</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $phone }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Country</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $country }}</td></tr>
                            </table>
                        </div>

                        <div class="details-box">
                            <p class="details-title">Trip Preferences</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                <tr><td style="padding:6px 0;color:#6b7280;">Activity</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $activity ?: 'Multiple options' }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Travellers</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $numberOfPeople ?: $travelGroup }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Dates</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $dateInfo }}</td></tr>
                                <tr><td style="padding:6px 0;color:#6b7280;">Accommodation</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ ucfirst($accommodation) }}</td></tr>
                                @if(!empty($packages))
                                <tr><td style="padding:6px 0;color:#6b7280;">Selected Packages</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ is_array($packages) ? implode(', ', $packages) : $packages }}</td></tr>
                                @endif
                                <tr><td style="padding:6px 0;color:#6b7280;">Exercises Regularly</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $exercise }}</td></tr>
                                @if(!empty($message))
                                <tr><td style="padding:10px 0 6px 0;color:#6b7280;border-top:1px solid #d1fae5;" colspan="2">Message:<br><span style="color:#0b1535;">{{ $message }}</span></td></tr>
                                @endif
                            </table>
                        </div>

                        <p class="text" style="margin-bottom:0;">Please follow up with the customer within 24 hours via the admin dashboard.</p>
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
