<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status Update</title>
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
        .status-badge { display: inline-block; padding: 8px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0; }
        .status-confirmed { background-color: #dcfce7; color: #166534; border: 2px solid #86efac; }
        .status-pending { background-color: #fef9c3; color: #854d0e; border: 2px solid #fde047; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; border: 2px solid #fca5a5; }
        .status-completed { background-color: #dbeafe; color: #1e40af; border: 2px solid #93c5fd; }
        .details-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px 24px; margin: 24px 0; }
        .details-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin: 0 0 14px 0; }
        .cancellation-box { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fca5a5; border-radius: 10px; padding: 20px 24px; margin: 24px 0; }
        .cancellation-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #991b1b; margin: 0 0 10px 0; }
        .cancellation-reason { color: #7f1d1d; font-size: 14px; line-height: 1.6; margin: 0; }
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
                        <p class="subtitle">Booking Status Update</p>
                    </div>
                    <div class="body">
                        <p class="greeting">Dear {{ $name }},</p>
                        <p class="text">We wanted to let you know that the status of your booking has been updated.</p>

                        <div style="text-align:center;">
                            <span class="status-badge status-{{ $status }}">{{ ucfirst($status) }}</span>
                        </div>

                        <div class="details-box">
                            <p class="details-title">Booking Details</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                                @if(!empty($tourTitle))
                                <tr><td style="padding:6px 0;color:#6b7280;">Tour</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $tourTitle }}</td></tr>
                                @endif
                                @if(!empty($date))
                                <tr><td style="padding:6px 0;color:#6b7280;">Date</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $date }}</td></tr>
                                @endif
                                @if(!empty($bookingType))
                                <tr><td style="padding:6px 0;color:#6b7280;">Type</td><td style="padding:6px 0;color:#0b1535;font-weight:600;text-align:right;">{{ $bookingType }}</td></tr>
                                @endif
                            </table>
                        </div>

                        @if($status === 'confirmed')
                            <p class="text">Your booking has been <strong>confirmed</strong>. We're excited to have you on this trip! Our team will reach out soon with further details and preparation instructions.</p>
                        @elseif($status === 'cancelled')
                            <p class="text">We regret to inform you that your booking has been <strong>cancelled</strong>.</p>
                            @if(!empty($cancellationReason))
                            <div class="cancellation-box">
                                <p class="cancellation-title">Cancellation Reason</p>
                                <p class="cancellation-reason">{{ $cancellationReason }}</p>
                            </div>
                            @endif
                            <p class="text">If you have any questions or believe this was done in error, please contact us at <strong>info@yakspiritadventures.com</strong>.</p>
                        @elseif($status === 'completed')
                            <p class="text">Your trip has been marked as <strong>completed</strong>. We hope you had an amazing experience with Yakspirit Adventures! We'd love to hear your feedback.</p>
                        @else
                            <p class="text">Your booking is currently <strong>pending</strong>. We'll update you once it's confirmed.</p>
                        @endif

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
