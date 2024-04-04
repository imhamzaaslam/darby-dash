<?php

namespace Database\Factories;

use App\Enums\Platform as PlatformEnum;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Platform>
 */
class PlatformFactory extends Factory
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
            'name' => fake()->unique()->company,
            'state' => fake()->randomElement(['active', 'inactive']),
            'client' => PlatformEnum::BOL_COM->value,
        ];
    }
}
