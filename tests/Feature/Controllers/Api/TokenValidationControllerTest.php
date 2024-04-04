<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class TokenValidationControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_token_validation_successful()
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->postJson('api/v1/token/validate', [
            'email' => $user->email,
            'token' => $token
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Token is valid'
        ]);
    }

    public function test_validation_fails_with_invalid_token()
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/token/validate', [
            'email' => $user->email,
            'token' => 'invalid-token'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Token is invalid'
        ]);
    }

    public function test_validation_fails_with_expired_token()
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        DB::table('password_resets')->where('email', $user->email)->update([
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') + 1)
        ]);

        $response = $this->postJson('api/v1/token/validate', [
            'email' => $user->email,
            'token' => $token
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Token is invalid'
        ]);
    }
}