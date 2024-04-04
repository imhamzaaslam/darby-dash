<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserInfo>
 */
class UserInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company' => fake()->company,
            'coc_number' => '23062639',
            'phone' => fake()->phoneNumber,
            'website' => fake()->url,
            'country' => fake()->randomElement(['NL', 'BE']),
            'communication_language' => 'NL'
        ];
    }
}
