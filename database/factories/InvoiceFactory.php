<?php

namespace Database\Factories;

use App\Enums\InvoiceType;
use App\Facades\VatFacade as Vat;
use App\Models\Invoice;
use App\Models\Platform;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAmount = -fake()->numberBetween(1000000, 9999999);

        return [
            'shop_id' => Shop::factory(),
//            'platform_id' => Platform::factory(),
            'issued_at' => now(),
            'uid' => (string) fake()->randomNumber(9),
            'type' => InvoiceType::ALL_IN_ONE->value,
            'year' => now()->year,
            'month' => now()->month,
            'amount' => Vat::priceExcludingVat($totalAmount),
            'vat' => Vat::vatFromTotalAmount(abs($totalAmount)),
            'total_amount' => $totalAmount,
            'payload' => ''
        ];
    }
}
