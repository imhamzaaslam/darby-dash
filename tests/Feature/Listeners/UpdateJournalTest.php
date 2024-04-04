<?php

namespace Tests\Feature\Listeners;

use App\Contracts\InvoiceRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Enums\GeneralLedger;
use App\Enums\JournalStatus;
use App\Enums\JournalType;
use App\Events\OssRegistrationDateChanged;
use App\Events\ProductCategoryChanged;
use App\Listeners\UpdateJournal;
use App\Models\Country;
use App\Models\Credential;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Platform;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserInfo;
use App\Services\Bol\BolClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateJournalTest extends TestCase
{
    use DatabaseTransactions;

    private InvoiceRepositoryInterface $invoiceRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->invoiceRepository = app(InvoiceRepositoryInterface::class);

        \Hamcrest\Util::registerGlobalFunctions();
    }

    /** @test */
    public function it_can_retrieve_journals_to_be_updated_when_product_category_changed_event_is_dispatched(): void
    {
        $user = $this->user('customer');
        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();
        $product = Product::factory()->recycle($platform)->create();

        Credential::factory()->recycle($shop)->create();

        /*
         * Creates:
         * - 35 journals
         * - 140 entries, 4 for each journal
         * - 35 invoices, 1 for each journal
         * - 5 journals per journal status
         */
        $journals = Journal::factory()->count(35)
            ->sequence(
                ['status' => JournalStatus::CREATED->value],
                ['status' => JournalStatus::PENDING->value],
                ['status' => JournalStatus::APPROVED->value],
                ['status' => JournalStatus::DISAPPROVED->value],
                ['status' => JournalStatus::BOOKED->value],
                ['status' => JournalStatus::FAILED->value],
                ['status' => JournalStatus::MANUAL->value],
            )->recycle($shop)
            ->has(
                JournalEntry::factory()->count(4)
                    ->sequence(
                        ['general_ledger_account' => GeneralLedger::ACCRUED_INCOME->value],
                        ['general_ledger_account' => GeneralLedger::ACCRUED_CHARGES->value],
                        ['general_ledger_account' => GeneralLedger::TURNOVER_NL->value],
                        ['general_ledger_account' => GeneralLedger::TURNOVER_BE->value],
                    ),
                relationship: 'entries'
            )
            ->create();

        $invoices = Invoice::all();

        $invoices->each(
            fn (Invoice $invoice) => Order::factory()
                ->count(4)
                ->hasAttached($product, ['uid' => fake()->word, 'quantity' => 10, 'quantity_shipped' => 8, 'quantity_cancelled' => 2, 'price' => 9999])
                ->recycle($shop)
                ->recycle($platform)
                ->recycle($invoice)
                ->create(['country_id' => null])
        );

        $expectedJournals = $journals
            ->unique()
            ->reject(
                fn (Journal $journal) => !$journal->isStatusIn([JournalStatus::PENDING->value, JournalStatus::DISAPPROVED->value])
            );

        /** @var UpdateJournal $listener */
        $listener = resolve(UpdateJournal::class);
        $actualJournals = $listener->getForEventType(new ProductCategoryChanged($product));

        $this->assertInstanceOf(Collection::class, $actualJournals);

        $difference = $expectedJournals->diff($actualJournals);

        $this->assertEquals($expectedJournals->count(), $actualJournals->count());
        $this->assertTrue($difference->isEmpty());

        $actualJournals->each(
            fn (Journal $journal) =>
            $this->assertTrue(
                $journal->isStatusIn([JournalStatus::PENDING->value, JournalStatus::DISAPPROVED->value])
            )
        );
    }

    /** @test */
    public function it_can_retrieve_journals_to_be_updated_when_oss_registration_date_changed_is_dispatched(): void
    {
        $shops = Shop::factory()->count(10)->create();

        $shops->each(
            fn (Shop $shop) =>
            Journal::factory()->count(7)
                ->sequence(
                    ['status' => JournalStatus::CREATED->value],
                    ['status' => JournalStatus::PENDING->value],
                    ['status' => JournalStatus::APPROVED->value],
                    ['status' => JournalStatus::DISAPPROVED->value],
                    ['status' => JournalStatus::BOOKED->value],
                    ['status' => JournalStatus::FAILED->value],
                    ['status' => JournalStatus::MANUAL->value],
                )
                ->recycle($shop)->create()
        );

        $shop = $shops->first();

        $expectedJournals = Journal::all()
            ->unique()
            ->reject(
                fn (Journal $journal) => $journal->shop->isNot($shop) || !$journal->isStatusIn([JournalStatus::PENDING->value, JournalStatus::DISAPPROVED->value])
            );

        /** @var UpdateJournal $listener */
        $listener = resolve(UpdateJournal::class);
        $actualJournals = $listener->getForEventType(new OssRegistrationDateChanged(User::first()));

        $this->assertInstanceOf(Collection::class, $actualJournals);

        $difference = $expectedJournals->diff($actualJournals);

        $this->assertEquals($expectedJournals->count(), $actualJournals->count());
        $this->assertTrue($difference->isEmpty());

        $actualJournals->each(
            fn (Journal $journal) =>
            $this->assertTrue(
                $journal->isStatusIn([JournalStatus::PENDING->value, JournalStatus::DISAPPROVED->value])
            )
        );
    }

    /**
     * @test
     * @dataProvider provideJournalData
     */
    public function it_can_update_journals_for_an_invoice(
        ?string $oss,
        bool $shouldCreateRollbackJournal,
        string $invoiceId,
        array $invoiceData,
        array $journalData,
        array $expectedEntries,
    ): void
    {
        Notification::fake();

        $user = $this->user('customer', ['oss_registered_at' => $oss]);
        $info = UserInfo::factory()->recycle($user)->create();

        $platform = Platform::factory()->create(['state' => 'active']);
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        Credential::factory()->recycle($shop)->create(['state' => 'active']);

        // Note that we are using a factory here and not the repository to create an invoice, because the repository will fire an event.
        // Firing events can normally be prevented by faking the Event, but faking the event will prevent
        // the metadata from being saved/persisted into the database.
        $invoice = Invoice::factory()->recycle($shop)->create([
            'uuid' => fake()->uuid,
            'uid' => $invoiceId,
            ...$invoiceData,
        ]);

        app(InvoiceRepositoryInterface::class)->storeBolLineItemsMeta($invoice, $this->invoiceMeta($invoiceId));

        Journal::factory()->recycle($shop)->recycle($invoice)
            ->has(JournalEntry::factory()->count(rand(1, 10)),'entries')
            ->create([
                'administration_coc_number' => $info->coc_number,
                'document_subject' => "{$invoice->created_at->format('Y-m')} | {$invoice->uid}",
                'journal_type' => 'GeneralJournal',
                'status' => JournalStatus::DISAPPROVED->value
            ]);

        if ($shouldCreateRollbackJournal) {
            Journal::factory()->recycle($shop)->recycle($invoice)
                ->has(JournalEntry::factory()->count(rand(1, 10)),'entries')
                ->create([
                    'administration_coc_number' => $info->coc_number,
                    'document_subject' => "{$invoice->created_at->format('Y-m')} | {$invoice->uid}",
                    'journal_type' => 'GeneralJournal',
                    'status' => JournalStatus::DISAPPROVED->value,
                    'type' => JournalType::ROLLBACK->value,
                ]);
        }

        $this->mock(BolClient::class, function (MockInterface $mock) use ($user, $invoiceId, $shop) {
            $mock->shouldReceive('getInvoiceSpecification')
                ->with(anInstanceOf(Shop::class), $invoiceId)
                ->once()
                ->andReturn($this->specification($invoiceId));

            foreach ($this->orders($invoiceId) as $orderId => $order) {
                $mock->shouldReceive('getOrder')
                    ->with(anInstanceOf(Shop::class), (string) $orderId)
                    ->once()
                    ->andReturn($this->order($invoiceId, $orderId));
            }
        });

        $this->partialMock(UserRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAdmins')->once()->andReturn(collect());
        });

        /** @var UpdateJournal $listener */
        $listener = resolve(UpdateJournal::class);
        $listener->handle(new OssRegistrationDateChanged($user->refresh()));

        $invoice = $invoice->refresh();
        $journals = $invoice->journals;

        $this->assertCount(2, $journals);
        $mainJournal = $journals->where('type', JournalType::MAIN->value)->first();
        $rollbackJournal = $journals->where('type', JournalType::ROLLBACK->value)->first();

        $journals->each(function (Journal $journal) use ($expectedEntries, $invoice) {
            $this->assertEquals(JournalStatus::PENDING->value, $journal->status);
            $this->assertNotNull($journal->type);

            if ($journal->type === JournalType::MAIN->value) {
                $this->assertCount($expectedEntries[JournalType::MAIN->value], $journal->entries);
            }

            if ($journal->type === JournalType::ROLLBACK->value) {
                $this->assertEquals("{$invoice->issued_at->format('Y-m')} | {$invoice->uid}", $journal->document_subject);
                $this->assertCount($expectedEntries[JournalType::ROLLBACK->value], $journal->entries);
            }
        });

        foreach ($journalData as $type => $data) {
            foreach ($data as $glAccount => $amountData) {
                foreach ($amountData as $code => $amount) {
                    if (empty($code)) {
                        $country = null;
                    } else {
                        $country = Country::whereCode($code);
                    }

                    $journal = $mainJournal;

                    if ($type === JournalType::ROLLBACK->value) {
                        $journal = $rollbackJournal;
                    }

                    $entry = $journal->entries
                        ->where('general_ledger_account', $glAccount)
                        ->where('country_id', $country?->id)
                        ->first();

                    $expectedEntryDate = $journal->invoice->getPeriod()->endOfMonth();

                    if ($type === JournalType::ROLLBACK->value) {
                        $expectedEntryDate = $journal->invoice->issued_at->startOfMonth();
                    }

                    $this->assertEquals($amount, $entry->amount);
                    $this->assertEquals(
                        $expectedEntryDate->format('Y-m-d'),
                        $entry->entry_date->format('Y-m-d')
                    );

                    if (empty($code)) {
                        $this->assertNull($entry->country);
                    } else {
                        $this->assertEquals($code, $entry->country->code);
                    }
                }
            }
        }

        $journals->each(function (Journal $journal) use ($invoice, $expectedEntries) {
            $rounding = $journal->entries
                ->where('general_ledger_account', GeneralLedger::ROUNDING_DIFFERENCES->value)->first();
            $this->assertNull($rounding);
        });
    }

    public static function provideJournalData(): array
    {
        return [
            [
                null, // oss registration date,
                false, // should create rollback journal
                '3908410750704', // invoice id
                ['amount' => -188225, 'vat' => 23857, 'total_amount' => -212082], // basic invoice data
                [
                    JournalType::MAIN->value => [
                        // general ledger account -> country -> amount
                        GeneralLedger::COMPENSATIONS->value => [null => -2011],
                        GeneralLedger::PACKAGING_COSTS->value => [null => 63995],
                        GeneralLedger::OTHER_SALES_COSTS->value => [null => 50144],
                        GeneralLedger::TURNOVER_NL->value => ['NL' => -129938],
                        GeneralLedger::TURNOVER_BE->value => ['BE' => -138004],
                        GeneralLedger::VAT_STANDARD->value => ['NL' => -27287, 'BE' => -28981], // 56617
                        GeneralLedger::ACCRUED_INCOME->value => [null => 326221],
                        GeneralLedger::ACCRUED_CHARGES->value => [null => -114139],
                    ],
                    JournalType::ROLLBACK->value => [
                        // general ledger account -> country -> amount
                        GeneralLedger::PRE_TAX->value => [null => 23857],
                        GeneralLedger::ACCRUED_INCOME->value => [null => -326221],
                        GeneralLedger::ACCRUED_CHARGES->value => [null => 114139],
                        GeneralLedger::SUSPENSE_ACCOUNT_BOL_COM->value => [null => 188225],
                    ],
                ],
                [JournalType::MAIN->value => 9, JournalType::ROLLBACK->value => 4], // expected amount of journal entries
            ],
            [
                '2022-01-01 00:00:00', // oss registration date
                true, // should create rollback journal
                '3908581257388', // invoice id
                ['amount' => -359490, 'vat' => 47109, 'total_amount' => -406599], // basic invoice data
                [
                    JournalType::MAIN->value => [
                        // general ledger account -> country -> amount
                        GeneralLedger::PACKAGING_COSTS->value => [null => 127543],
                        GeneralLedger::OTHER_SALES_COSTS->value => [null => 98158],
                        GeneralLedger::COMPENSATIONS->value => [null => -8489],
                        GeneralLedger::TURNOVER_NL->value => ['NL' => -272185],
                        GeneralLedger::VAT_OSS->value => ['BE' => -51106],
                        GeneralLedger::TURNOVER_BE->value => ['BE' => -243361],
                        GeneralLedger::VAT_STANDARD->value => ['NL' => -57159],
                        GeneralLedger::ACCRUED_INCOME->value => [null => 632300],
                        GeneralLedger::ACCRUED_CHARGES->value => [null => -225701],
                    ],
                    JournalType::ROLLBACK->value => [
                        // general ledger account -> country -> amount
                        GeneralLedger::PRE_TAX->value => [null => 47109],
                        GeneralLedger::ACCRUED_INCOME->value => [null => -632300],
                        GeneralLedger::ACCRUED_CHARGES->value => [null => 225701],
                        GeneralLedger::SUSPENSE_ACCOUNT_BOL_COM->value => [null => 359490],
                    ],
                ],
                [JournalType::MAIN->value => 9, JournalType::ROLLBACK->value => 4], // expected amount of journal entries
            ],
        ];
    }

    private function generalLedgerAccounts(): array
    {
        return [
            GeneralLedger::PACKAGING_COSTS->value => null,
            GeneralLedger::OTHER_SALES_COSTS->value => null,
            GeneralLedger::TURNOVER_NL->value => 'NL',
            GeneralLedger::TURNOVER_BE->value => 'BE',
            GeneralLedger::VAT_STANDARD->value => 'NL',
            GeneralLedger::VAT_STANDARD->value => 'BE',
            GeneralLedger::SUSPENSE_ACCOUNT_BOL_COM->value => null,
            GeneralLedger::PRE_TAX->value => null,
            GeneralLedger::ROUNDING_DIFFERENCES->value => null,
        ];
    }

    private function specification(string $invoiceId): array
    {
        $file = File::get("tests/stubs/bol_com/invoices/invoice_{$invoiceId}_specification.json");

        return json_decode($file, true);
    }

    private function order(string $invoiceId, string $orderId): array
    {
        return $this->orders($invoiceId)[$orderId];
    }

    private function invoiceMeta(string $invoiceId): array
    {
        $file = File::get("tests/stubs/bol_com/invoices/invoice_{$invoiceId}.json");

        return json_decode($file, true);
    }

    private function orders(string $invoiceId): array
    {
        $file = File::get("tests/stubs/bol_com/invoices/{$invoiceId}/orders.json");

        $array = json_decode($file, true);
        $keys = [];

        foreach ($array as $order) {
            $keys[] = $order['orderId'];
        }

        return array_combine($keys, $array);
    }
}
