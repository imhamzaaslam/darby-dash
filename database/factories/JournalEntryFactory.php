<?php

namespace Database\Factories;

use App\Enums\GeneralLedger;
use App\Models\Country;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'journal_id' => Journal::factory(),
            'country_id' => Country::all()->random(),
            'contact_name' => fake()->company,
            'contact_code' => (string) fake()->randomNumber(9),
            'dossier_name' => fake()->company,
            'dossier_code' => (string) fake()->randomNumber(9),
            'entry_date' => now(),
            'general_ledger_account' => GeneralLedger::random(),
            'amount' => fake()->numberBetween(100000, 999999),
            'description' => fake()->sentence,
        ];
    }
}
