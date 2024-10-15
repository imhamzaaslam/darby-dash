<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use Validator;
use Carbon\Carbon;
use App\Services\EmailService;
use App\Events\UserCreated;

class AuthController extends Controller
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new EmailService();
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'company' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $company = Company::where('name', $request->company)->first();

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
            ], 401);
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
            ], 401);
        }

        $user = $request->user();
        /* if (!$user->companies->contains($company->id)) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to this company.',
            ], 401);
        } */

        if ($user->is_2fa) {

            $user->generate2FACode();
            $this->mailer->send2FACode($user);

            return response()->json([
                'success' => true,
                'message' => '2FA code sent to your email. Please verify.',
                'requires_2fa' => true,
            ]);
        }

        $user->role = $user->getRoleNames()->first();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        // call for existing user to add settings meta
        event(new UserCreated($user));

        return response()->json([
            'success' => true,
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    /**
     * Logout user (Revoke the token)
    *
    * @return [string] message
    */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);

    }

    public function verify2FA(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'code' => 'required|string',
            ]);
            $user = User::where('email', $request->email)->firstOrFail();
            $verificationExpiresAt = Carbon::parse($user->verification_expires_at);
            if ($user->verification_code == $request->code && $verificationExpiresAt->gt(now())) {
                $user->reset2FACode();
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->plainTextToken;

                return response()->json([
                    'success' => true,
                    'accessToken' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user,
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ]);
            }

            return response()->json([
                'message' => 'Invalid or expired 2FA code',
                'success' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }

    public function tempQuery()
    {
        $company = Company::firstOrCreate([
            'name' => 'Darby',
        ]);
        $users = User::all();
        foreach ($users as $user) {
            if (!$user->companies->contains($company->id)) {
                $user->companies()->attach($company->id);
            }
        }
    }
}
