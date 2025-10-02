<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: #3b82f6;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
            color: #ffffff;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .content p {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.5;
        }
        .action-button {
            display: inline-block;
            background: #3b82f6;
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
        }
        .action-button:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }
        .credentials-box {
            background-color: #f7fafc;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .credentials-box h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 18px;
        }
        .credentials-box p {
            margin: 10px 0;
            font-size: 16px;
        }
        .credential-value {
            background: #e2e8f0;
            padding: 8px 12px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #2d3748;
            display: inline-block;
            margin: 0 4px;
        }
        .instructions-box {
            background-color: #edf2f7;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .instructions-box h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 18px;
        }
        .instructions-box ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions-box li {
            margin: 8px 0;
            font-size: 15px;
            line-height: 1.4;
        }
        .warning-box {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box p {
            margin: 0;
            font-size: 14px;
            color: #742a2a;
        }
        .alternative-link {
            background-color: #f7fafc;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .alternative-link p {
            margin: 0;
            font-size: 14px;
            color: #4a5568;
        }
        .alternative-link a {
            color: #3b82f6;
            word-break: break-all;
        }
        .footer {
            background-color: #f7fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 5px;
            }
            .content {
                padding: 20px;
            }
            .header {
                padding: 20px;
            }
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <h2>Welcome!</h2>
            
            <p>Hello {{ $user['name'] ?: $user['username'] }},</p>
            
            <p>Welcome to {{ config('app.name') }}! Your account has been successfully created and you now have access to our platform.</p>
            
            @if($recoveryLink)
                <div class="credentials-box">
                    <h3>Your Account Details</h3>
                    <p><strong>Username:</strong> <span class="credential-value">{{ $user['username'] }}</span></p>
                    <p><strong>Email:</strong> <span class="credential-value">{{ $user['email'] }}</span></p>
                </div>
                
                <div style="text-align: center;">
                    <a href="{{ $recoveryLink }}" class="action-button">Set Up Your Password</a>
                </div>
                
                <div class="alternative-link">
                    <p><strong>Button not working?</strong> Copy and paste this link into your web browser:</p>
                    <p><a href="{{ $recoveryLink }}">{{ $recoveryLink }}</a></p>
                </div>
                
                <div class="instructions-box">
                    <h3>Setup Instructions</h3>
                    <ol>
                        <li><strong>Click the "Set Up Your Password" button above</strong></li>
                        <li><strong>When prompted, enter your credentials:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                <li>Email: <span class="credential-value">{{ $user['email'] }}</span></li>
                                <li>OR Username: <span class="credential-value">{{ $user['username'] }}</span></li>
                            </ul>
                        </li>
                        <li><strong>Create a strong password</strong> (minimum 8 characters recommended)</li>
                        <li><strong>Complete the setup</strong></li>
                    </ol>
                </div>
            @elseif($password)
                <div class="credentials-box">
                    <h3>Your Login Credentials</h3>
                    <p><strong>Username:</strong> <span class="credential-value">{{ $user['username'] }}</span></p>
                    <p><strong>Temporary Password:</strong> <span class="credential-value">{{ $password }}</span></p>
                    <p><strong>Email:</strong> <span class="credential-value">{{ $user['email'] }}</span></p>
                </div>
                
                <div class="warning-box">
                    <p><strong>Security Notice:</strong> This is a temporary password that you will be required to change on your first login. Please keep this information secure and delete this email after you have successfully logged in and changed your password.</p>
                </div>
                
                <div style="text-align: center;">
                    <a href="{{ $loginUrl }}" class="action-button">Login to {{ config('app.name') }}</a>
                </div>
            @else
                <div class="credentials-box">
                    <h3>Your Account</h3>
                    <p><strong>Username:</strong> <span class="credential-value">{{ $user['username'] }}</span></p>
                    <p><strong>Email:</strong> <span class="credential-value">{{ $user['email'] }}</span></p>
                    <p>Please contact your administrator for password setup instructions.</p>
                </div>
                
                <div style="text-align: center;">
                    <a href="{{ $loginUrl }}" class="action-button">Visit {{ config('app.name') }}</a>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>