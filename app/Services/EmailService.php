<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailService
{
    public function send2FACode($user)
    {
        try {
            Mail::send('emails.two_factor_authentication', compact('user'), function ($message) use ($user) {
                $message->subject('Your 2FA code')->to($user->email);
            });
            return true;
        } catch (Exception $e) {
            Log::info("2FA email failed: " . $e->getMessage());
            return false;
        }
    }
}

