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
        
        @if($password)
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
        @endif
        
        <h3>Getting Started</h3>
        <ol>
            <li>Click the login button below to access the system</li>
            @if($password)
                <li>Use the credentials provided above to sign in</li>
                <li>You will be prompted to change your password on first login</li>
            @endif
            <li>Complete your profile information if required</li>
            <li>Explore the available features and applications</li>
        </ol>
        
        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Login to {{ config('app.name') }}</a>
        </div>
        
        <h3>Need Help?</h3>
        <p>If you have any questions or need assistance getting started, please don't hesitate to contact our support team.</p>
        
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