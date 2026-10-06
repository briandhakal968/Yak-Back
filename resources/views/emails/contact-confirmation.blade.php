<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Received — Yakspirit Adventures</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; }
        body { background-color: #f5f5f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { width: 100%; background-color: #f5f5f5; padding: 32px 16px; }
        .container { max-width: 560px; margin: 0 auto; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0b40c0 0%, #1a5de6 100%); padding: 32px 32px 24px; text-align: center; }
        .logo { display: inline-block; width: 56px; height: 56px; line-height: 56px; background-color: rgba(255,255,255,0.15); border-radius: 12px; color: #d9991a; font-size: 28px; font-weight: 700; margin-bottom: 16px; }
        .title { color: #fff; font-size: 22px; font-weight: 700; margin: 0; }
        .subtitle { color: rgba(255,255,255,0.8); font-size: 14px; margin: 6px 0 0 0; }
        .body { padding: 36px 32px; color: #1f2937; font-size: 16px; line-height: 1.6; }
        .greeting { margin: 0 0 16px 0; font-size: 18px; font-weight: 600; color: #0b1535; }
        .text { margin: 0 0 20px 0; }
        .message-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px 24px; margin: 24px 0; }
        .message-label { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin: 0 0 10px 0; }
        .message-content { color: #374151; font-size: 14px; line-height: 1.6; margin: 0; }
        .whatsapp-box { background: linear-gradient(135deg, #dcfce7 0%, #d1fae5 100%); border: 1px solid #86efac; border-radius: 10px; padding: 16px 20px; margin: 20px 0; text-align: center; }
        .whatsapp-text { color: #166534; font-size: 14px; font-weight: 600; margin: 0; }
        .whatsapp-sub { color: #15803d; font-size: 12px; margin: 4px 0 0 0; }
        .divider { border: 0; border-top: 1px solid #e5e7eb; margin: 28px 0; }
        .footer { background-color: #f9fafb; padding: 24px 32px; text-align: center; color: #6b7280; font-size: 13px; line-height: 1.5; }
        .footer-brand { color: #0b1535; font-weight: 700; font-size: 14px; margin: 0 0 6px 0; }
        .footer-tagline { color: #6b7280; font-size: 12px; margin: 0 0 12px 0; }
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
                        <div class="logo">Y</div>
                        <h1 class="title">Yakspirit Adventures</h1>
                        <p class="subtitle">We've Received Your Message</p>
                    </div>
                    <div class="body">
                        <p class="greeting">Dear {{ $name }},</p>
                        <p class="text">Thank you for contacting Yakspirit Adventures.</p>
                        <p class="text">We have received your email and will get back to you as soon as possible. Our team is reviewing your inquiry and will respond with the information you need.</p>

                        <div class="message-box" style="padding: 12px 20px;">
                            <p class="message-label" style="margin-bottom: 12px;">Your Submission Details</p>
                            <table style="width:100%; font-size:14px; color:#374151; line-height:1.6;">
                                <tr><td style="padding:4px 8px 4px 0; color:#6b7280; white-space:nowrap; vertical-align:top; font-weight:600;">Name</td><td style="padding:4px 0;">{{ $name }}</td></tr>
                                <tr><td style="padding:4px 8px 4px 0; color:#6b7280; white-space:nowrap; vertical-align:top; font-weight:600;">Email</td><td style="padding:4px 0;">{{ $email }}</td></tr>
                                @if(!empty($phone))
                                <tr><td style="padding:4px 8px 4px 0; color:#6b7280; white-space:nowrap; vertical-align:top; font-weight:600;">Phone</td><td style="padding:4px 0;">{{ $phone }}</td></tr>
                                @endif
                                @if(!empty($country))
                                <tr><td style="padding:4px 8px 4px 0; color:#6b7280; white-space:nowrap; vertical-align:top; font-weight:600;">Country</td><td style="padding:4px 0;">{{ $country }}</td></tr>
                                @endif
                                @if(!empty($city))
                                <tr><td style="padding:4px 8px 4px 0; color:#6b7280; white-space:nowrap; vertical-align:top; font-weight:600;">City</td><td style="padding:4px 0;">{{ $city }}</td></tr>
                                @endif
                                <tr><td style="padding:4px 8px 4px 0; color:#6b7280; white-space:nowrap; vertical-align:top; font-weight:600;">Subject</td><td style="padding:4px 0;">{{ $subject }}</td></tr>
                            </table>
                        </div>

                        <hr class="divider" />

                        <div class="message-box">
                            <p class="message-label">Your Message</p>
                            <p class="message-content">{{ $message }}</p>
                        </div>

                        <div class="whatsapp-box">
                            <p class="whatsapp-text">Need a faster response?</p>
                            <p class="whatsapp-sub">If your inquiry is urgent, feel free to contact us via WhatsApp for a quicker reply.</p>
                        </div>

                        <p class="text">Thank you for your patience. We look forward to helping you plan an unforgettable adventure with Yakspirit Adventures.</p>

                        <p class="text" style="margin-bottom:0;">Warm regards,<br><strong>The Yakspirit Adventures Team</strong></p>
                    </div>
                    <div class="footer">
                        <p class="footer-brand">Yakspirit Adventures</p>
                        <p class="footer-tagline">From Trails To Summit — Adventures for Every Explorer</p>
                        <p class="footer-note">&copy; {{ date('Y') }} Yakspirit Adventures. All rights reserved.</p>
                    </div>
                </div>
            </td></tr>
        </table>
    </div>
</body>
</html>
