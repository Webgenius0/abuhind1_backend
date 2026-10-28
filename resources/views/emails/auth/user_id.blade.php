<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your User ID</title>
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
            border: 2px dashed #0284c7;
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
            color: #0284c7;
            letter-spacing: 2px;
            word-break: break-all;
        }
        .instructions {
            background-color: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
            color: #0369a1;
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
            <h1>Welcome to {{ config('app.name', 'Malamih') }}</h1>
        </div>
        <div class="content">
            <p class="greeting">Hello <strong>{{ $user->name ?? 'Valued User' }}</strong>,</p>
            <p>Thank you for logging in with Google. Your account has been registered successfully.</p>
            <p>We have generated a unique <strong>User ID</strong> for your account:</p>

            <div class="code-box">
                <div class="code-label">Your Unique User ID</div>
                <div class="code-value">{{ $userId }}</div>
            </div>

            <div class="instructions">
                <strong>Quick Login Access:</strong>
                <p style="margin: 6px 0 0 0;">You can use this User ID at any time to log in directly without needing your password. Please keep this code secure.</p>
            </div>

            <p style="font-size: 14px; color: #64748b;">If you have any questions or did not initiate this login, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Malamih') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

