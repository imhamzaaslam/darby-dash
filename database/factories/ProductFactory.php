<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Platform;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'category_id' => Category::factory(), // always recycle a category though
            'title' => fake()->sentence(3),
            'ean' => fake()->ean8(),
            'asin' => fake()->randomNumber(9),
        ];
    }
}
