<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
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
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .otp-code {
            background-color: #4f46e5;
            color: white;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            letter-spacing: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Crocodic Project Manager</h1>
        <h2>Email Verification</h2>
    </div>
    
    <div class="content">
        <p>Hello {{ $userName }},</p>
        
        <p>Thank you for registering with Project Manager. To complete your registration, please use the following OTP code:</p>
        
        <div class="otp-code">
            {{ $otp }}
        </div>
        
        <p><strong>Important:</strong></p>
        <ul>
            <li>This OTP code will expire in 15 minutes</li>
            <li>Do not share this code with anyone</li>
            <li>If you didn't request this code, please ignore this email</li>
        </ul>
        
        <p>If you're having trouble with verification, please contact our support team.</p>
        
        <p>Best regards,<br>Crocodic Project Manager Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>
