<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Your Password</title>
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
        background-color: #f8f9fa;
        color: #333333;
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

    .email-footer {
        padding: 20px;
        background-color: #f8f9fa;
        color: #777777;
        text-align: center;
        border-radius: 0 0 8px 8px;
        font-size: 12px;
    }

    .email-footer a {
        color: #0d6efd;
        text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="email-container">
    @include('emails._partials.header', ['title' => 'Reset Password'])

    <div class="email-content">
        <strong>Hello!</strong>
        <p>You are receiving this email because we received a password reset request for your account.</p>

        <p>
            <a
                href="{{ $actionUrl }}"
                style="display: inline-block; padding: 8px 18px; background-color: #2d3748; color: #ffffff !important; text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 14px; text-align: center;">
                Reset Password
            </a>
        </p>

        <p>If you did not request a password reset, no further action is required.</p>
        <p>Thank,<br>{{ config('app.name') }}</p>
    </div>

    @include('emails._partials.footer')
  </div>
</body>
</html>