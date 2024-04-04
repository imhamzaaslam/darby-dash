<?php

namespace Database\Factories;

use App\Enums\State;
use App\Models\Platform;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
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
            'user_id' => User::factory(),
            'platform_id' => Platform::factory(),
            'name' => fake()->company,
            'description' => fake()->sentence,
            'state' => State::ACTIVE->value,
        ];
    }
}
