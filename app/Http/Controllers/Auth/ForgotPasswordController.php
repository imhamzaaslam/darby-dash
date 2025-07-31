<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'frontendUrl' => 'required|url',
        ]);

        session(['frontend_url' => $request->frontendUrl]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            Log::info('Password reset email sent to: ' . $request->email);
            return response()->json(['message' => __($status)], 200);
        } else {
            Log::error('Failed to send password reset email to: ' . $request->email . '. Status: ' . $status);
            return response()->json(['message' => __($status)], 400);
        }
    }
}