<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $frontendBaseUrl = session('frontend_url', config('app.url'));
        $resetUrl = rtrim($frontendBaseUrl, '/') . '/reset-password';

        $fullResetUrl = $resetUrl . '?token=' . $this->token . '&email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->view('emails.reset_password', [
                'actionUrl' => $fullResetUrl,
            ]);
    }
}