<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;

class TokenValidationController extends Controller
{
    private TokenRepositoryInterface $tokenRepository;

    public function __construct() {
        $this->tokenRepository = Password::getRepository();
    }

    /**
     * Validate token
     * 
     * @param ValidateTokenRequest $request
     * @return JsonResponse
     */
    public function validateToken(ValidateTokenRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        try {
            
            $validateToken = $this->tokenRepository->exists($user, $request->token);
            return response()->json([
                'success' => $validateToken,
                'message' => $validateToken ? 'Token is valid' : 'Token is invalid'
            ], 200);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while validating the token. Please try again later.'
            ], 500);
        }
    }

    /**
     * Invoke the controller
     * 
     * @param ValidateTokenRequest $request
     * @return JsonResponse
     */
    public function __invoke(ValidateTokenRequest $request): JsonResponse
    {
        return $this->validateToken($request);
    }
}
