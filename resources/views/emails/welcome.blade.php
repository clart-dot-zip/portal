<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e9ecef;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e9ecef;
            border-top: none;
        }
        .credentials {
            background-color: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 10px;
            margin: 15px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ config('app.name') }}</h1>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user['name'] ?: $user['username'] }}!</h2>
        
        <p>Welcome to {{ config('app.name') }}! Your account has been successfully created and you now have access to our systems.</p>
        
        @if($recoveryLink)
            <div class="credentials">
                <h3>🔑 Account Setup Required</h3>
                <p><strong>Your Username:</strong> <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">{{ $user['username'] }}</code></p>
                <p><strong>Your Email:</strong> <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">{{ $user['email'] }}</code></p>
                <p><strong>Next Step:</strong> Click the button below to set up your password</p>
            </div>
            
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ $recoveryLink }}" class="button">🚀 Set Up Your Password</a>
            </div>
            
            <div class="warning">
                <strong>📋 Setup Instructions:</strong>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>Click the "Set Up Your Password" button above</strong></li>
                    <li><strong>When prompted, enter your email:</strong> <code style="background: #fff; padding: 1px 4px;">{{ $user['email'] }}</code></li>
                    <li><strong>Or enter your username:</strong> <code style="background: #fff; padding: 1px 4px;">{{ $user['username'] }}</code></li>
                    <li><strong>Create a strong password</strong> (minimum 8 characters recommended)</li>
                    <li><strong>Complete the setup</strong> and you'll be ready to log in!</li>
                </ol>
                <p style="margin-top: 15px;"><strong>💡 Pro tip:</strong> Copy your email or username from above to avoid typos!</p>
            </div>
        @elseif($password)
            <div class="credentials">
                <h3>Your Login Credentials</h3>
                <p><strong>Username:</strong> {{ $user['username'] }}</p>
                <p><strong>Temporary Password:</strong> <code>{{ $password }}</code></p>
                <p><strong>Email:</strong> {{ $user['email'] }}</p>
            </div>
            
            <div class="warning">
                <strong>Important Security Notice:</strong>
                <ul>
                    <li>This is a temporary password that you will be required to change on your first login</li>
                    <li>Please keep this information secure and do not share it with anyone</li>
                    <li>Delete this email after you have successfully logged in and changed your password</li>
                </ul>
            </div>
        @else
            <div class="credentials">
                <h3>Your Account</h3>
                <p><strong>Username:</strong> {{ $user['username'] }}</p>
                <p><strong>Email:</strong> {{ $user['email'] }}</p>
                <p>Please contact your administrator for password setup instructions.</p>
            </div>
        @endif
        
        <h3>🎯 Getting Started</h3>
        @if($recoveryLink)
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff;">
                <p><strong>Follow these simple steps:</strong></p>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>Click the "🚀 Set Up Your Password" button above</strong></li>
                    <li><strong>Enter your details when prompted:</strong>
                        <ul style="margin: 5px 0;">
                            <li>Email: <code style="background: #fff; padding: 1px 4px;">{{ $user['email'] }}</code> (you can copy this!)</li>
                            <li>OR Username: <code style="background: #fff; padding: 1px 4px;">{{ $user['username'] }}</code></li>
                        </ul>
                    </li>
                    <li><strong>Create your password</strong> (make it strong and memorable)</li>
                    <li><strong>Complete the setup</strong> and bookmark the login page</li>
                    <li><strong>Start exploring!</strong> You'll have access to all authorized applications</li>
                </ol>
            </div>
        @else
            <ol>
                <li>Click the login button below to access the system</li>
                @if($password)
                    <li>Use the credentials provided above to sign in</li>
                    <li>You will be prompted to change your password on first login</li>
                @endif
                <li>Complete your profile information if required</li>
                <li>Explore the available features and applications</li>
            </ol>
        @endif
        
        @if(!$recoveryLink)
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Login to {{ config('app.name') }}</a>
            </div>
        @endif
        
        <p>Best regards,<br>
        The {{ config('app.name') }} Team</p>
    </div>
    
    <div class="footer">
        <p style="margin: 0; font-size: 12px; color: #666;">
            This email was sent automatically. Please do not reply to this email address.
        </p>
        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
            {{ config('app.name') }} | {{ config('app.url') }}
        </p>
    </div>
</body>
</html>