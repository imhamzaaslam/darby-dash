<?php

namespace Database\Factories;

use App\Models\AccessToken;
use App\Models\Credential;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccessToken>
 */
class AccessTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid,
            'credential_id' => Credential::factory(),
            'expires_at' => now()->addSeconds(298),
            'access_token' => fake()->uuid,
        ];
    }
}
