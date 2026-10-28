<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Premium Activation Code</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f7fafc;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 30px 25px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 30px 25px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 16px;
        }
        .code-box {
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }
        .code-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .code-value {
            font-family: 'Courier New', Courier, monospace;
            font-size: 28px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: 2px;
            word-break: break-all;
        }
        .info-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .info-table td.label {
            color: #64748b;
            font-weight: 600;
            width: 40%;
        }
        .info-table td.val {
            color: #1e293b;
            font-weight: 500;
        }
        .instructions {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
        }
        .instructions ol {
            margin: 8px 0 0 20px;
            padding: 0;
        }
        .instructions li {
            margin-bottom: 6px;
        }
        .footer {
            text-align: center;
            padding: 20px 25px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name', 'Malamih') }} Premium Access</h1>
        </div>
        <div class="content">
            <p class="greeting">Hello <strong>{{ $user->name ?? 'Valued User' }}</strong>,</p>
            <p>Your exclusive premium activation promo code is ready. Use this code to unlock full facial analysis, unlimited scans, and complete diagnostics.</p>

            <div class="code-box">
                <div class="code-label">Your Promo Code</div>
                <div class="code-value">{{ $code }}</div>
            </div>

            <table class="info-table">
                <tr>
                    <td class="label">Access Duration:</td>
                    <td class="val">{{ !empty($premiumCode?->validity_days) ? $premiumCode->validity_days . ' Days' : 'Lifetime' }}</td>
                </tr>
                @if(!empty($premiumCode?->code_expires_at))
                <tr>
                    <td class="label">Redeem Before:</td>
                    <td class="val">{{ \Carbon\Carbon::parse($premiumCode->code_expires_at)->format('d M, Y h:i A') }}</td>
                </tr>
                @endif
                @if(!empty($premiumCode?->note))
                <tr>
                    <td class="label">Note:</td>
                    <td class="val">{{ $premiumCode->note }}</td>
                </tr>
                @endif
            </table>

            <div class="instructions">
                <strong>How to redeem your code:</strong>
                <ol>
                    <li>Log in to your account on the mobile app or website.</li>
                    <li>Navigate to the <strong>Subscription / Redeem</strong> page.</li>
                    <li>Enter the promo code above and submit to activate your Premium privileges instantly.</li>
                </ol>
            </div>

            <p style="font-size: 14px; color: #64748b;">If you did not expect this email, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Malamih') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

