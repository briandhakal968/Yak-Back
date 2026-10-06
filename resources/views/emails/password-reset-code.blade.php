<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Password Reset Verification Code</title>
    <style>
        /* Reset styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }

        /* Remove default styling */
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

        /* Template styles */
        body {
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f5f5f5;
            padding: 32px 16px;
        }
        .email-container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }
        .email-header {
            background: linear-gradient(135deg, #0b40c0 0%, #1a5de6 100%);
            padding: 32px 32px 24px;
            text-align: center;
        }
        .email-logo {
            display: inline-block;
            width: 56px;
            height: 56px;
            line-height: 56px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #d9991a;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .email-title {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }
        .email-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin: 6px 0 0 0;
        }
        .email-body {
            padding: 36px 32px;
            color: #1f2937;
            font-size: 16px;
            line-height: 1.6;
        }
        .email-greeting {
            margin: 0 0 16px 0;
            font-size: 18px;
            font-weight: 600;
            color: #0b1535;
        }
        .email-text {
            margin: 0 0 24px 0;
        }
        .code-box {
            background: linear-gradient(135deg, #fff8e6 0%, #fff0cc 100%);
            border: 2px dashed #d9991a;
            border-radius: 12px;
            padding: 28px 24px;
            text-align: center;
            margin: 24px 0;
        }
        .code-label {
            color: #6b7280;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            margin: 0 0 12px 0;
        }
        .code-value {
            color: #0b1535;
            font-size: 38px;
            font-weight: 700;
            letter-spacing: 10px;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            user-select: all;
            -webkit-user-select: all;
        }
        .code-expiry {
            color: #92400e;
            font-size: 13px;
            font-weight: 600;
            margin: 16px 0 0 0;
        }
        .email-warning {
            background-color: #fef3c7;
            border-left: 4px solid #d9991a;
            border-radius: 6px;
            padding: 14px 18px;
            margin: 24px 0;
            color: #78350f;
            font-size: 14px;
            line-height: 1.5;
        }
        .email-divider {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 28px 0;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 24px 32px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.5;
        }
        .email-footer-brand {
            color: #0b1535;
            font-weight: 700;
            font-size: 14px;
            margin: 0 0 6px 0;
        }
        .email-footer-tagline {
            color: #6b7280;
            font-size: 12px;
            margin: 0 0 12px 0;
        }
        .email-footer-note {
            color: #9ca3af;
            font-size: 11px;
            margin: 0;
        }

        /* Mobile responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 16px 8px; }
            .email-body { padding: 28px 20px; }
            .email-header { padding: 28px 20px 20px; }
            .email-footer { padding: 20px; }
            .code-value { font-size: 32px; letter-spacing: 8px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td align="center">
                    <div class="email-container">
                        <!-- Header -->
                        <div class="email-header">
                            <div class="email-logo">Y</div>
                            <h1 class="email-title">Yakspirit Adventures</h1>
                            <p class="email-subtitle">Admin Dashboard Security</p>
                        </div>

                        <!-- Body -->
                        <div class="email-body">
                            <p class="email-greeting">Hi {{ $name }},</p>

                            <p class="email-text">
                                We received a request to reset the password for your Yakspirit Adventures admin account. Use the verification code below to continue with your password reset.
                            </p>

                            <div class="code-box">
                                <p class="code-label">Your Verification Code</p>
                                <p class="code-value">{{ $code }}</p>
                                <p class="code-expiry">⏱ Expires in {{ $minutes }} minutes</p>
                            </div>

                            <div class="email-warning">
                                <strong>⚠ Didn't request this?</strong><br>
                                If you did not request a password reset, you can safely ignore this email. Your account is still secure. For your safety, never share this code with anyone.
                            </div>

                            <hr class="email-divider">

                            <p class="email-text" style="margin-bottom: 0;">
                                <strong>How to use this code:</strong><br>
                                1. Return to the password reset page<br>
                                2. Enter the 6-digit code above<br>
                                3. Set your new password
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="email-footer">
                            <p class="email-footer-brand">Yakspirit Adventures</p>
                            <p class="email-footer-tagline">From Trails To Summit — Adventures for Every Explorer</p>
                            <p class="email-footer-note">
                                &copy; {{ date('Y') }} Yakspirit Adventures. All rights reserved.<br>
                                This is an automated security email. Please do not reply.
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
