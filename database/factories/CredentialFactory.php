<?php

namespace Database\Factories;

use App\Models\Platform;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Credential>
 */
class CredentialFactory extends Factory
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
            'shop_id' => Shop::factory(),
            'client_id' => fake()->uuid(),
            'client_secret' => fake()->uuid(),
            'name' => fake()->company,
            'state' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
