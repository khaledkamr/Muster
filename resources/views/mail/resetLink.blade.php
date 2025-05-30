<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
        }

        .content {
            padding: 20px;
            background-color: #ffffff;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Request</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset your password. If you didn't make this request, you can safely ignore this
                email.</p>
            <p>To reset your password, click the button below:</p>
            <p style="text-align: center;">
                <a href="{{ route('password.reset', ['email' => $email]) }}" class="button">Reset Password</a>
            </p>
            <p>If the button above doesn't work, you can copy and paste the following link into your browser:</p>
            <p style="word-break: break-all;">{{ route('password.reset', ['email' => $email]) }}</p>
            <p>This password reset link will expire in 60 minutes.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>Best regards,<br>Your Application Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
