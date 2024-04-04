<?php

namespace Services\Yuki;

use App\Contracts\InvoiceRepositoryInterface;
use App\Enums\Currency;
use App\Enums\GeneralLedger;
use App\Enums\InvoiceLineType;
use App\Enums\InvoiceType;
use App\Enums\JournalStatus;
use App\Enums\JournalType;
use App\Enums\Platform as EnumsPlatform;
use App\Enums\VatRate;
use App\Exceptions\NotFoundOrdersException;
use App\Models\Category;
use App\Models\Country;
use App\Models\Credential;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Platform;
use App\Models\Product;
use App\Models\Shop;
use App\Models\UserInfo;
use App\Services\Bol\BolClient;
use App\Services\Yuki\JournalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Mockery;

class JournalServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected InvoiceRepositoryInterface $invoiceRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->invoiceRepository = app(InvoiceRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider provideNumbers
     */
    public function it_can_create_an_all_in_one_journal(
        ?string $oss,
        string $invoiceId,
        array $invoiceData,
        array $journalData,
        array $expectedEntries,
        ?array $products
    ): void
    {
        Event::fake();
        Notification::fake();

        $user = $this->user('customer', ['oss_registered_at' => $oss]);
        UserInfo::factory()->recycle($user)->create();
        $this->be($user);

        $platform = Platform::factory()->create();

        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        $invoice = $this->invoiceRepository->create($shop, $this->invoiceMeta($invoiceId));

        if ($products) {
            foreach ($products as $ean => $category) {
                $product = Product::factory()->create([
                    'platform_id' => $platform->id,
                    'category_id' => Category::whereType($category)->first()->id,
                    'ean' => $ean,
                ]);

                $user->products()->attach($product, ['sku' => $ean, 'purchase_price' => rand(999, 9999)]);
            }
        }

        $this->mock(BolClient::class, function (MockInterface $mock) use ($invoice, $invoiceId) {
            /*
             * Note that we should receive these methods with the Shop object that's stored in the invoice model,
             * since we're passing this exact object into the parameter of the setShop method in the JournalService.
             *
             * If we just pass in the $shop variable created above, this test will not pass.
             *
             * Optionally we could use anInstance() if we do not have the exact object instance, but in this case we do
             * have the exact needed object instance.
             */
            $mock->shouldReceive('getInvoiceSpecification')
                ->with($invoice->shop, $invoiceId)
                ->once()
                ->andReturn($this->specification($invoiceId));

            foreach ($this->orders($invoiceId) as $orderId => $order) {
                $mock->shouldReceive('getOrder')
                    ->with($invoice->shop, (string) $orderId)
                    ->once()
                    ->andReturn($this->order($invoiceId, $orderId));
            }
        });

        app(JournalService::class)->create($invoice, InvoiceType::ALL_IN_ONE);

        $journals = $invoice->journals;

        $this->assertCount(2, $journals);
        $mainJournal = $journals->where('type', JournalType::MAIN->value)->first();
        $rollbackJournal = $journals->where('type', JournalType::ROLLBACK->value)->first();

        $journals->each(function (Journal $journal) use ($invoice, $expectedEntries) {
            $this->assertNotNull($journal);
            $this->assertEquals(JournalStatus::PENDING->value, $journal->status);
            $this->assertEquals('GeneralJournal', $journal->journal_type);
            $this->assertEquals('23062639', $journal->administration_coc_number);

            if ($journal->type === JournalType::MAIN->value) {
                $this->assertEquals("{$invoice->getPeriod()->lastOfMonth()->format('Y-m')} | {$invoice->uid}", $journal->document_subject);
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

        foreach ($this->orders($invoiceId) as $orderId => $order) {
            $this->assertDatabaseHas(
                'orders',
                [
                    'uid' => $orderId,
                    'shop_id' => $shop->id,
                    'currency' => Currency::EUR->name,
                ]
            );
        }
    }

    /**
     * @test
     * @dataProvider provideAdvertisingNumbers
     */
    public function it_can_create_an_advertising_journal(string $invoiceId, array $invoiceData, array $journalData): void
    {
        Event::fake();
        Notification::fake();

        $user = $this->user('customer');
        UserInfo::factory()->recycle($user)->create();
        $this->be($user);

        $platform = Platform::factory()->create();

        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        $invoice = $this->invoiceRepository->create($shop, $this->invoiceMeta($invoiceId), InvoiceType::ADVERTISING);

        $this->mock(BolClient::class, function (MockInterface $mock) use ($user) {
            $mock->shouldNotHaveReceived('getInvoiceSpecification');
        });

        app(JournalService::class)->create($invoice, InvoiceType::ADVERTISING);

        $journals = $invoice->journals;

        $this->assertCount(2, $journals);
        $mainJournal = $journals->where('type', JournalType::MAIN->value)->first();
        $rollbackJournal = $journals->where('type', JournalType::ROLLBACK->value)->first();

        $journals->each(function (Journal $journal) use ($invoice, $journalData, $platform) {
            $this->assertNotNull($journal);
            $this->assertEquals(JournalStatus::PENDING->value, $journal->status);
            $this->assertEquals('GeneralJournal', $journal->journal_type);
            $this->assertEquals('23062639', $journal->administration_coc_number);

            $entries = $journal->entries;

            if ($journal->type === JournalType::MAIN->value) {
                $this->assertEquals(
                    "{$invoice->getPeriod()->lastOfMonth()->format('Y-m')} | {$platform->name} Ads {$invoice->uid}",
                    $journal->document_subject
                );
                $this->assertCount(count($journalData[JournalType::MAIN->value]), $journal->entries);
            }

            if ($journal->type === JournalType::ROLLBACK->value) {
                $this->assertEquals(
                    "{$invoice->issued_at->format('Y-m')} | {$platform->name} Ads {$invoice->uid}",
                    $journal->document_subject
                );
                $this->assertCount(count($journalData[JournalType::ROLLBACK->value]), $journal->entries);
            }

            $entries->each(fn (JournalEntry $entry) => $this->assertNull($entry->country));
        });

        foreach ($journalData as $type => $data) {
            foreach ($data as $glAccount => $amount) {
                $journal = $mainJournal;

                if ($type === JournalType::ROLLBACK->value) {
                    $journal = $rollbackJournal;
                }

                $entry = $journal
                    ->entries
                    ->where('general_ledger_account', $glAccount)
                    ->first();

                $this->assertEquals(
                    $amount,
                    $entry->amount
                );

                $expectedEntryDate = $journal->invoice->getPeriod()->endOfMonth();

                if ($type === JournalType::ROLLBACK->value) {
                    $expectedEntryDate = $journal->invoice->issued_at->startOfMonth();
                }

                $this->assertEquals($amount, $entry->amount);
                $this->assertEquals(
                    $expectedEntryDate->format('Y-m-d'),
                    $entry->entry_date->format('Y-m-d')
                );
            }
        }

        $journals->each(function (Journal $journal) use ($invoice, $journalData) {
            $rounding = $journal->entries
                ->where('general_ledger_account', GeneralLedger::ROUNDING_DIFFERENCES->value)->first();
            $this->assertNull($rounding);
        });
    }

    /** @test */
    public function it_should_throw_an_exception_when_no_specification_is_found()
    {
        $user = $this->user('customer');
        UserInfo::factory()->recycle($user)->create();

        $platform = Platform::factory()->create();

        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        $invoice = Invoice::factory()->recycle($shop)->create([
            'uid' => '3908410750704',
            'amount' => -188225,
            'vat' => 23857,
            'total_amount' => -212082,
        ]);

        app(InvoiceRepositoryInterface::class)->storeBolLineItemsMeta($invoice, $this->invoiceMeta('3908410750704'));

        $this->mock(BolClient::class, function (MockInterface $mock) use ($invoice) {
            $mock->shouldReceive('getInvoiceSpecification')
                ->with($invoice->shop, '3908410750704')
                ->once()
                ->andReturnNull();
        });

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No invoice specification received for invoice: ' . $invoice->uuid);

        app(JournalService::class)->create($invoice, InvoiceType::ALL_IN_ONE);
    }

    /** @test */
    public function it_should_log_error_when_orders_are_not_found()
    {
        Notification::fake();
        Event::fake();

        $user = $this->user('customer');
        UserInfo::factory()->recycle($user)->create();

        $platform = Platform::factory()->create();

        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        Credential::factory()->recycle($shop)->create([
            'state' => 'active'
        ]);

        $invoiceId = '3908410750704';

        $invoice = $this->invoiceRepository->create($shop, $this->invoiceMeta($invoiceId));

        $this->mock(BolClient::class, function (MockInterface $mock) use ($invoice, $invoiceId) {
            $mock->shouldReceive('getInvoiceSpecification')
                ->with($invoice->shop, $invoiceId)
                ->once()
                ->andReturn($this->specification($invoiceId));

            foreach ($this->orders($invoiceId) as $orderId => $order) {
                /*
                 * Please note that the '->once()' expectation is not used here, since we want this piece of code to throw
                 * an exception. Because of this exception and the fact that some order numbers are registered twice in the
                 * specification (once with the #TURNOVER hashtag and once with the #CORRECTION_TURNOVER), the getOrder method
                 * might be received twice for those cases.
                 */
                $mock->shouldReceive('getOrder')
                    ->with($invoice->shop, (string) $orderId)
                    ->andThrow(
                        HttpClientException::class,
                        "Order for order id {$orderId} was not found.",
                        404
                    );
            }
        });

        $orderIds = array_keys($this->orders($invoiceId));

        $this->expectException(NotFoundOrdersException::class);
        $this->expectExceptionMessage(
            'The following orders were not found for user ' . $user->uuid . ': ' . implode(',', $orderIds)
        );

        app(JournalService::class)->create($invoice, InvoiceType::ALL_IN_ONE);
    }

    /**
     * @test
     * @dataProvider provideNumbers
     */
    public function it_can_recalculate_a_main_journal_and_create_a_rollback_journal(
        ?string $oss,
        string $invoiceId,
        array $invoiceData,
        array $journalData,
        array $expectedEntries,
        ?array $products
    ): void
    {
        $user = $this->user('customer', ['oss_registered_at' => $oss]);
        $info = UserInfo::factory()->recycle($user)->create();

        $platform = Platform::factory()->create();

        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        $invoice = Invoice::factory()->recycle($shop)->create([
            'uid' => $invoiceId,
            ...$invoiceData,
        ]);

        $journal = Journal::factory()->recycle($shop)->recycle($invoice)->create([
            'administration_coc_number' => $info->coc_number,
            'document_subject' => "{$invoice->created_at->format('Y-m-d')} | {$invoice->uid}",
            'journal_type' => 'GeneralJournal',
            'status' => JournalStatus::DISAPPROVED->value
        ]);

        $oldEntries = collect();

        foreach ($this->generalLedgerAccounts() as $generalLedger => $code) {
            $country = null;

            if ($code) {
                $country = Country::whereCode($code);
            }

            $entry = JournalEntry::factory()->recycle($journal)->recycle($country)->create([
                'contact_name' => $platform->name,
                'general_ledger_account' => $generalLedger,
                'amount' => rand(1000, 999999),
                'entry_date' => $invoice->issued_at,
                'description' => GeneralLedger::tryFrom($generalLedger)->description(),
            ]);

            $oldEntries->add($entry);
        }

        if ($oss) {
            $entry = JournalEntry::factory()->recycle($journal)->create([
                'contact_name' => $platform->name,
                'general_ledger_account' => GeneralLedger::VAT_OSS->value,
                'amount' => rand(1000, 999999),
                'entry_date' => $invoice->issued_at,
                'description' => GeneralLedger::tryFrom(GeneralLedger::VAT_OSS->value)->description(),
            ]);

            $oldEntries->add($entry);
        }

        // created some soft deleted records.
        $oldEntries->add(JournalEntry::factory()->recycle($journal)->create(
            ['general_ledger_account' => GeneralLedger::ACCRUED_CHARGES->value, 'deleted_at' => now()]
        ));

        $oldEntries->add(JournalEntry::factory()->recycle($journal)->create(
            ['general_ledger_account' => GeneralLedger::ACCRUED_INCOME->value, 'deleted_at' => now()]
        ));

        $journal = $journal->fresh();

        if ($oss) {
            $this->assertCount(11, $journal->entries()->withTrashed()->get());
        } else {
            $this->assertCount(10, $journal->entries()->withTrashed()->get());
        }

        app(InvoiceRepositoryInterface::class)->storeBolLineItemsMeta($invoice, $this->invoiceMeta($invoiceId));

        if ($products) {
            foreach ($products as $ean => $category) {
                $product = Product::factory()->create([
                    'platform_id' => $platform->id,
                    'category_id' => Category::whereType($category)->first()->id,
                    'ean' => $ean,
                ]);

                $user->products()->attach($product, ['sku' => $ean, 'purchase_price' => rand(999, 9999)]);
            }
        }

        $this->mock(BolClient::class, function (MockInterface $mock) use ($journal, $invoiceId) {
            $mock->shouldReceive('getInvoiceSpecification')
                ->with($journal->invoice->shop, $invoiceId)
                ->once()
                ->andReturn($this->specification($invoiceId));

            foreach ($this->orders($invoiceId) as $orderId => $order) {
                $mock->shouldReceive('getOrder')
                    ->with($journal->invoice->shop, (string) $orderId)
                    ->once()
                    ->andReturn($this->order($invoiceId, $orderId));
            }
        });

        $service = app(JournalService::class);

        $service->update(
            $journal,
            InvoiceType::ALL_IN_ONE
        );

        $service->createRollbackJournal($journal->refresh(), InvoiceType::ALL_IN_ONE);

        $this->assertCount(2, $journals = $invoice->journals()->get());

        $mainJournal = $journal->refresh();
        $rollBackJournal = $journals->where('type', JournalType::ROLLBACK->value)->first();

        $this->assertNotNull($rollBackJournal);
        $this->assertCount($expectedEntries[JournalType::MAIN->value], $mainJournal->entries()->withTrashed()->get());

        $this->assertCount($expectedEntries[JournalType::ROLLBACK->value], $rollBackJournal->entries);
        $this->assertEquals(JournalStatus::PENDING->value, $mainJournal->status);
        $this->assertEquals(JournalStatus::PENDING->value, $rollBackJournal->status);

        foreach ($oldEntries as $oldEntry) {
            $this->assertDatabaseMissing('journal_entries', $oldEntry->toArray());
        }

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
                        $journal = $rollBackJournal;
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

        $rounding = $journal->entries()->where('general_ledger_account', GeneralLedger::ROUNDING_DIFFERENCES->value)->first();
        $this->assertNull($rounding);
    }

    /** @test */
    public function it_can_disapprove_a_journal(): void
    {
        Event::fake();
        Notification::fake();

        $user = $this->user('customer');
        UserInfo::factory()->recycle($user)->create();
        $this->be($user);

        $platform = Platform::factory()->create();

        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        Credential::factory()->recycle($shop)->create(['state' => 'active']);

        $invoiceId = '3909605370161';

        $invoice = $this->invoiceRepository->create($shop, $this->invoiceMeta($invoiceId));

        $foundOrders = $this->orders($invoiceId);
        $notFoundOrders = $this->notFoundOrders($invoiceId);

        $this->expectException(NotFoundOrdersException::class);
        $this->expectExceptionMessage(
            "The following orders were not found for user $user->uuid: " . implode(',', array_unique($notFoundOrders))
        );

        $this->mock(BolClient::class, function (MockInterface $mock) use ($user, $invoice, $invoiceId, $foundOrders, $notFoundOrders) {
            $mock->shouldReceive('getInvoiceSpecification')
                ->with($invoice->shop, $invoiceId)
                ->once()
                ->andReturn($this->specification($invoiceId));

            foreach ($foundOrders as $orderId => $order) {
                $mock->shouldReceive('getOrder')
                    ->with($invoice->shop, (string) $orderId)
                    ->once()
                    ->andReturn($this->order($invoiceId, $orderId));
            }

            foreach ($notFoundOrders as $orderId) {
                $mock->shouldReceive('getOrder')
                    ->with($invoice->shop, (string) $orderId)
                    ->once()
                    ->andThrow(
                        HttpClientException::class,
                        "Order for order id {$orderId} was not found.",
                        404
                    );
            }
        });

        app(JournalService::class)->create($invoice, InvoiceType::ALL_IN_ONE);
    }

    public function provideGeneralLedgerAccounts(): array
    {
        return [
            [
                [
                    GeneralLedger::PACKAGING_COSTS->value,
                    GeneralLedger::OTHER_SALES_COSTS->value,
                    GeneralLedger::TURNOVER_NL->value,
                    GeneralLedger::TURNOVER_BE->value,
                    GeneralLedger::VAT_STANDARD->value,
                    GeneralLedger::SUSPENSE_ACCOUNT_BOL_COM->value,
                    GeneralLedger::PRE_TAX->value,
                ],
            ],
        ];
    }

    public static function provideNumbers(): array
    {
        return [
            [
                null, // oss registration date
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
                null // product test data
            ],
            [
                '2022-01-01 00:00:00', // oss registration date
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
                null // product test data
            ],
            [
                '2022-01-01 00:00:00', // oss registration date
                '3910028370887', // invoice id
                ['amount' => -129814, 'vat' => 12250, 'total_amount' => -142064], // basic invoice data
                [
                    JournalType::MAIN->value => [
                        // general ledger account -> country -> amount
                        GeneralLedger::PACKAGING_COSTS->value => [null => 31881],
                        GeneralLedger::OTHER_SALES_COSTS->value => [null => 26981],
                        GeneralLedger::COMPENSATIONS->value => [null => -1361],
                        GeneralLedger::TURNOVER_NL->value => ['NL' => -82764],
                        GeneralLedger::VAT_OSS->value => ['BE' => -6190],
                        GeneralLedger::TURNOVER_BE->value => ['BE' => -103162],
                        GeneralLedger::VAT_REDUCED->value => ['NL' => -7449],
                        GeneralLedger::ACCRUED_INCOME->value => [null => 200926],
                        GeneralLedger::ACCRUED_CHARGES->value => [null => -58862],
                    ],
                    JournalType::ROLLBACK->value => [
                        // general ledger account -> country -> amount
                        GeneralLedger::PRE_TAX->value => [null => 12250],
                        GeneralLedger::ACCRUED_INCOME->value => [null => -200926],
                        GeneralLedger::ACCRUED_CHARGES->value => [null => 58862],
                        GeneralLedger::SUSPENSE_ACCOUNT_BOL_COM->value => [null => 129814],
                    ],
                ],
                [JournalType::MAIN->value => 9, JournalType::ROLLBACK->value => 4], // expected amount of journal entries
                ['7423435673695' => 'A_FOOD_TEA',] // product test data
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

    private function advertisingMeta(string $invoiceId): array
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

    private function notFoundOrders(string $invoiceId): array
    {
        $foundOrders = $this->orders($invoiceId);

        $orders = [];
        foreach ($this->specification($invoiceId)['invoiceSpecification'] as $line) {
            if (!str_contains($line['id'], InvoiceLineType::TURNOVER->name)) {
                continue;
            }

            $lineProperties = $line['item']['AdditionalItemProperty'];
            $orderId = $this->orderIdFromSpecification($lineProperties);

            if (!array_key_exists($orderId, $foundOrders)) {
                $orders[] = $orderId;
            }
        }

        sort($orders);

        return $orders;
    }

    private function orderIdFromSpecification(array $lineProperties): string
    {
        $orderId = null;

        foreach ($lineProperties as $lineProperty) {
            if ($lineProperty['Name']['value'] === 'OrderId' || $lineProperty['Name']['value'] === 'Bestelnummer') {
                $orderId = $lineProperty['Value']['value'];
            }
        }

        return $orderId;
    }

    public static function provideAdvertisingNumbers(): array
    {
        return [
            [
                '3908410670365',
                ['amount' => 53454, 'vat' => 11225, 'total_amount' => 64679],
                [
                    JournalType::MAIN->value => [
                        GeneralLedger::ADVERTISING_COSTS->value => 53454,
                        GeneralLedger::ACCRUED_CHARGES->value => -53454,
                    ],
                    JournalType::ROLLBACK->value => [
                        GeneralLedger::PRE_TAX->value => 11225,
                        GeneralLedger::ACCRUED_CHARGES->value => 53454,
                        GeneralLedger::SUSPENSE_ACCOUNT_BOL_COM->value => -64679,
                    ]
                ],
            ],
            [
                '3908581226894',
                ['amount' => 90360, 'vat' => 18976, 'total_amount' => 109336],
                [
                    JournalType::MAIN->value => [
                        GeneralLedger::ADVERTISING_COSTS->value => 90360,
                        GeneralLedger::ACCRUED_CHARGES->value => -90360,
                    ],
                    JournalType::ROLLBACK->value => [
                        GeneralLedger::PRE_TAX->value => 18976,
                        GeneralLedger::ACCRUED_CHARGES->value => 90360,
                        GeneralLedger::SUSPENSE_ACCOUNT_BOL_COM->value => -109336,
                    ]
                ],
            ],
        ];
    }
}
