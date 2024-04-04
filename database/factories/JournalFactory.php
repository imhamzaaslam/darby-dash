<?php

namespace Database\Factories;

use App\Enums\JournalStatus;
use App\Enums\JournalType;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Platform;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Journal>
 */
class JournalFactory extends Factory
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
//            'platform_id' => Platform::factory(),
            'invoice_id' => Invoice::factory(),
            'type' => JournalType::MAIN->value,
            'administration_key' => fake()->randomNumber(9),
            'administration_coc_number' => fake()->randomNumber(9),
            'document_subject' => fake()->sentence,
            'journal_type' => 'GeneralJournal',
            'status' => JournalStatus::random(),
        ];
    }
}
