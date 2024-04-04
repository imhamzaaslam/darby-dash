<?php

namespace Database\Seeders;

use App\Enums\GeneralLedger;
use App\Facades\VatFacade as Vat;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Platform;
use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $platform = Platform::whereClient('Bol.com')->firstOrFail();
        Invoice::all()->each(function (Invoice $invoice) use ($platform) {
            Journal::factory()->count(rand(1, 2))
                ->recycle($platform)
                ->recycle($invoice)
                ->for($invoice->shop)
                ->create()
                ->each(fn (Journal $journal) => $this->addEntries($journal));
        });
    }

    private function addEntries(Journal $journal): void
    {
        $totalAmount = abs($journal->invoice->total_amount);
        $countryNL = Country::where("code", 'NL')->firstOrFail();
        $countryBE = Country::where("code", 'BE')->firstOrFail();

        foreach (GeneralLedger::credit() as $credit) {
            $amountPerCountry = round($totalAmount / 2);
            $amount = 0;

            if ($credit === GeneralLedger::TURNOVER_NL->value) {
                $amount = Vat::priceExcludingVat($amountPerCountry);
            }

            if ($credit === GeneralLedger::TURNOVER_BE->value) {
                $amount = Vat::priceExcludingVat($amountPerCountry);
            }

            if ($credit === GeneralLedger::VAT_STANDARD->value) {
                $amount = Vat::vatFromTotalAmount($totalAmount);
            }

            JournalEntry::factory()->recycle($journal)->create([
                'general_ledger_account' => $credit,
                'amount' => -$amount,
                'country_id' => $credit == GeneralLedger::VAT_STANDARD->value ? $countryNL : $countryBE,
            ]);
        }

        $otherSalesCosts = rand(10000, 50000);
        $outsourcedCosts = rand(10000, 100000);
        $packagingCosts = rand(10000, 50000);

        foreach (GeneralLedger::debit() as $debit) {
            if ($debit === GeneralLedger::ADVERTISING_COSTS->value) {
                continue;
            }

            $amount = 0;

            if ($debit === GeneralLedger::OTHER_SALES_COSTS->value) {
                $amount = $otherSalesCosts;
            }

            if ($debit === GeneralLedger::OUTSOURCED_WORK->value) {
                $amount = $outsourcedCosts;
            }

            if ($debit === GeneralLedger::PACKAGING_COSTS->value) {
                $amount = $packagingCosts;
            }

            JournalEntry::factory()->recycle($journal)->create([
                'general_ledger_account' => $debit,
                'amount' => $amount,
                'country_id' => rand($countryNL->id, $countryBE->id),
            ]);
        }

        JournalEntry::factory()->recycle($journal)->create([
            'general_ledger_account' => GeneralLedger::SUSPENSE_ACCOUNT_BOL_COM->value,
            'amount' => ($totalAmount - $otherSalesCosts - $outsourcedCosts - $packagingCosts),
            'country_id' => rand($countryNL->id, $countryBE->id),
        ]);
    }
}
