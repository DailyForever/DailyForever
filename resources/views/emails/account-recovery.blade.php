<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Recovery - DailyForever</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Account Recovery</h1>
        <p>DailyForever Security</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $username }}!</h2>
        
        <p>We received a request to reset your PIN for your DailyForever account. If you made this request, please click the button below to proceed with the recovery process.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('auth.recovery.reset', ['username' => $username, 'token' => $token]) }}" class="button">
                Reset My PIN
            </a>
        </div>
        
        <div class="warning">
            <strong>⚠️ Important Security Information:</strong>
            <ul>
                <li>This link will expire in 24 hours</li>
                <li>Only use this link if you requested the PIN reset</li>
                <li>Your content remains encrypted and secure</li>
                <li>We cannot access your pastes or files</li>
            </ul>
        </div>
        
        <h3>What happens next?</h3>
        <ol>
            <li>Click the recovery link above</li>
            <li>Enter your new PIN (4-8 digits)</li>
            <li>Confirm your new PIN</li>
            <li>Log in with your new PIN</li>
        </ol>
        
        <p><strong>Recovery Token:</strong> <code>{{ $token }}</code></p>
        <p><strong>Expires:</strong> {{ $expires_at->format('F j, Y \a\t g:i A T') }}</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <h3>Didn't request this?</h3>
        <p>If you didn't request a PIN reset, please ignore this email. Your account remains secure and no action is needed.</p>
        
        <h3>Need help?</h3>
        <p>If you're having trouble with the recovery process, please contact our support team. Remember, we cannot access your encrypted content, but we can help with account recovery.</p>
    </div>
    
    <div class="footer">
        <p>This email was sent by DailyForever - Your Privacy-First Paste Service</p>
        <p>DailyForever uses zero-knowledge encryption. We cannot access your content.</p>
    </div>
</body>
</html>
