<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #a12592;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-content {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }

        .email-content p {
            margin: 0 0 15px;
        }

        .code-box {
            font-size: 18px;
            font-weight: bold;
            color: #333333;
            background-color: #f8f9fa;
            padding: 15px;
            border: 2px dashed #a12592;
            text-align: center;
            margin: 20px 0;
            border-radius: 4px;
        }

        .email-footer {
            padding: 20px;
            background-color: #f8f9fa;
            color: #777777;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
        }

        .email-footer a {
            color: #a12592;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        @include('emails._partials.header', ['title' => 'Two-Factor Authentication'])
        <div class="email-content">
            <p>Hi {{ $user->name_first }},</p>
            <p>To continue with your login, please use the following two-factor authentication (2FA) code:</p>
            <div class="code-box">
                {{ $user->verification_code }}
            </div>
            <p>This code will expire in 15 minutes. If you did not request this, please ignore this email or contact support.</p>
            @include('emails._partials.greeting_message')
        </div>
        @include('emails._partials.footer')
    </div>
</body>
</html>
