<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Platform;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'platform_id' => Platform::factory(),
            'country_id' => Country::all()->random(),
            'invoice_id' => Invoice::factory(),
            'ordered_at' => now(),
            'currency' => Currency::EUR->name,
            'uid' => (string) fake()->randomNumber(9),
            'total_price' => 2450
        ];
    }
}
