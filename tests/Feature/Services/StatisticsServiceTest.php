<?php

namespace Tests\Feature\Services;

use App\Enums\GeneralLedger;
use App\Enums\JournalStatus;
use App\Models\Shop;
use App\Models\UserInfo;
use App\Models\Credential;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Services\StatisticsService;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class StatisticsServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    /**
     * @test
     * @dataProvider providerOrdersProducts
     */
    public function test_services_response_matches_stub(array $products, array $orders, array $invoice, array $dateParams): void
    {
        $shop = Shop::factory()->create();
        $user = $shop->user;

        foreach ($products as $product) {
            $productCreated = Product::factory()->recycle($shop->platform)->create([
                'id' => $product['id'],
                'title' => $product['title']
            ]);

            $shop->products()->attach($productCreated, ['sku' => $product['sku']]);
        }

        foreach ($orders as $order) {
            $country = Country::where('code', $order['country_id'])->firstOrFail();

            $orderCreated = Order::factory()->recycle($shop)->create([
                'country_id' => $country->id,
                'ordered_at' => $order['ordered_at'],
                'total_price' => $order['total_price'],
            ]);

            foreach ($order['products'] as $product) {
                $orderCreated->products()->attach(
                    [
                        'id' => $product['product_id']
                    ],
                    [
                        'quantity_shipped' => $product['quantity_shipped'],
                        'quantity_cancelled' => $product['quantity_cancelled']
                    ]
                );
            }
        }

        Carbon::setTestNow(Carbon::create(2023, 7, 10, 12, 00, 00));

        $fromDate = $dateParams['fromDate'];
        $toDate = $dateParams['toDate'];

        /** @var StatisticsService $statisticsService */
        $service = app(StatisticsService::class);
        $statistics = $service->make($user, $fromDate, $toDate)->format();

        if (!is_null($fromDate) && !is_null($toDate)) {
            $this->assertEquals($this->statisticswithParams(), $statistics);
        } else {
            $this->assertEquals($this->statisticsWithoutParams(), $statistics);
        }
    }

    /**
     * @test
     * @dataProvider providerOrdersProducts
     */
    public function it_calculates_from_journal_if_it_exists(array $products, array $orders, array $invoices): void
    {
        $fromDate = Carbon::parse("2023-05-01");
        $toDate = Carbon::parse("2023-05-31");

        $shop = Shop::factory()->create();
        $user = $shop->user;
        $info = UserInfo::factory()->recycle($user)->create();

        Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        foreach ($invoices as  $invoiceData) {
            $invoice = Invoice::factory()->recycle($shop)->create([
                'month' => $invoiceData['month'],
                'year' => $invoiceData['year'],
                'uid' => $invoiceData['uid'],
                'amount' => $invoiceData['amount'],
                'vat' => $invoiceData['vat'],
                'total_amount' => $invoiceData['total_amount'],
            ]);

            foreach ($invoiceData['journals'] as $journalData) {
                $journal = Journal::factory()->recycle($invoice)->recycle($shop)->create([
                    'administration_coc_number' => $info->coc_number,
                    'document_subject' => "{$invoice->created_at->format('Y-m')} | {$invoice->uid}",
                    'journal_type' => $journalData['journal_type'],
                    'status' => $journalData['status']
                ]);

                foreach ($journalData['entries'] as $journalEntryData) {
                    $country = Country::where("code", $journalEntryData['country_code'])->first();

                    JournalEntry::factory()->recycle($journal)->create([
                        'country_id' => $country->id,
                        'contact_name' => $shop->platform->name,
                        'general_ledger_account' => $journalEntryData['general_ledger_account'],
                        'amount' => $journalEntryData['amount'],
                        'entry_date' => $invoice->issued_at,
                        'description' => $journalEntryData['description'],
                    ]);
                }
            }
        }

        foreach ($orders as $order) {
            $country = Country::where("code", $order['country_id'])->first();
            Order::factory()->recycle($shop)->create([
                'country_id' => $country->id,
                'ordered_at' => $order['ordered_at'],
                'total_price' => $order['total_price'],
            ]);

//            dd($order->country?->code ?? 'no code');
        }

        /** @var StatisticsService $service */
        $service = app(StatisticsService::class);
        $statistics = $service->make($user, $fromDate, $toDate)->format();
        $novStats = $statistics['turnover']['countries']['months'][0];
        $turnover = $novStats['products'][0]['turnover'];
        $countryName = $novStats['products'][0]['code'];
        $this->assertEquals("NL", $countryName);
        $this->assertEquals(4000, $turnover);
        $this->assertEquals('2022-11-01', $novStats['start']);
        $this->assertEquals('2022-11-30', $novStats['end']);
    }

    /**
     * @test
     * @dataProvider providerOrdersProducts
     */
    public function it_calculates_turnover_from_orders_if_journal_does_not_exist(array $products, array $orders): void
    {
        $fromDate = Carbon::parse("2023-05-01");
        $toDate = Carbon::parse("2023-05-31");

        $shop = Shop::factory()->create();
        $user = $shop->user;
        UserInfo::factory()->recycle($user)->create();
        $platform = $shop->platform;

        foreach ($products as $product) {
            $productCreated = Product::factory()->recycle($platform)->create([
                'id' => $product['id'],
                'title' => $product['title']
            ]);
            $user->products()->attach($productCreated, ['sku' => $product['sku']]);
        }
        foreach ($orders as $order) {
            $country = Country::where("code", $order['country_id'])->first();

            $orderCreated = Order::factory()->recycle($shop)->create([
                'country_id' => $country->id,
                'ordered_at' => $order['ordered_at'],
                'total_price' => $order['total_price'],
            ]);

            foreach ($order['products'] as $product) {
                $orderCreated->products()->attach(
                    [
                        'id' => $product['product_id']
                    ],
                    [
                        'quantity_shipped' => $product['quantity_shipped'],
                        'quantity_cancelled' => $product['quantity_cancelled']
                    ]
                );
            }
        }

        /** @var StatisticsService $service */
        $service = app(StatisticsService::class);
        $statistics = $service->make($user, $fromDate, $toDate)->format();
        $januaryStats = $statistics['turnover']['countries']['months'][2];
        $turnover = $januaryStats['products'][0]['turnover'];
        $countryName = $januaryStats['products'][0]['code'];
        $this->assertEquals("NL", $countryName);
        $this->assertEquals(230, $turnover);
        $this->assertEquals('2023-01-01', $januaryStats['start']);
        $this->assertEquals('2023-01-31', $januaryStats['end']);
    }

    /**
     * @test
     * @dataProvider providerOrdersProducts
     */
    public function test_vat_statistics_response_matches_stub(array $products, array $orders, array $invoices, array $dateParams): void
    {
        $shop = Shop::factory()->create();
        $user = $shop->user;
        $info = UserInfo::factory()->recycle($user)->create();
        $platform = $shop->platform;

        Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        foreach ($invoices as  $invoiceData) {
            $invoice = Invoice::factory()->recycle($shop)->create([
                'month' => $invoiceData['month'],
                'year' => $invoiceData['year'],
                'uid' => $invoiceData['uid'],
                'amount' => $invoiceData['amount'],
                'vat' => $invoiceData['vat'],
                'total_amount' => $invoiceData['total_amount'],
            ]);
            foreach ($invoiceData['journals'] as $journalData) {
                $journal = Journal::factory()->recycle($invoice)->recycle($shop)->create([
                    'administration_coc_number' => $info->coc_number,
                    'document_subject' => "{$invoice->created_at->format('Y-m-d')} | {$invoice->uid}",
                    'journal_type' => $journalData['journal_type'],
                    'status' => $journalData['status']

                ]);
                foreach ($journalData['entries'] as $journalEntryData) {
                    $country = Country::where("code", $journalEntryData['country_code'])->first();
                    JournalEntry::factory()->recycle($journal)->create([
                        'country_id' => $country->id,
                        'contact_name' => $platform->name,
                        'general_ledger_account' => $journalEntryData['general_ledger_account'],
                        'amount' => $journalEntryData['amount'],
                        'entry_date' => $invoice->issued_at,
                        'description' => $journalEntryData['description'],
                    ]);
                }
            }
        }

        Carbon::setTestNow(Carbon::create(2023, 05, 15, 12, 00, 00));
        $fromDate = $dateParams['fromDate'];
        $toDate = $dateParams['toDate'];

        /** @var StatisticsService $service */
        $service = app(StatisticsService::class);
        $statistics = $service->makeVat($user, $fromDate, $toDate)->toArray();

        if (!is_null($fromDate) && !is_null($toDate)) {
            $this->assertEquals($this->vatStatisticsWithParams(), $statistics);
        } else {
            $this->assertEquals($this->vatStatisticsWithoutParams(), $statistics);
        }
    }

    private function statisticsWithoutParams(): array
    {
        $file = File::get('tests/stubs/services/statistics_without_filters.json');
        return json_decode($file, true);
    }

    private function statisticsWithParams(): array
    {
        $file = File::get('tests/stubs/services/statistics_with_filters.json');
        return json_decode($file, true);
    }

    private function vatStatisticsWithoutParams(): array
    {
        $file = File::get('tests/stubs/services/vat_statistics_without_filters.json');
        return json_decode($file, true);
    }

    private function vatStatisticsWithParams(): array
    {
        $file = File::get('tests/stubs/services/vat_statistics_with_filtered.json');
        return json_decode($file, true);
    }

    public static function providerOrdersProducts(): array
    {
        $countryNL = "NL";
        $countryBE = "BE";
        $dateParams = [
            [
                'fromDate' => Carbon::parse("2023-05-01"),
                'toDate' => Carbon::parse("2023-05-31")
            ],
            [
                'fromDate' => null,
                'toDate' => null
            ]
        ];

        $products = [
            [
                'id' => 1,
                'title' => 'test product one',
                'sku' => 1001,
            ],
            [
                'id' => 2,
                'title' => 'test product two',
                'sku' => 1002,
            ]
        ];

        $orders = [

            [
                'ordered_at' => "2022-11-04",
                'total_price' => 450,
                'country_id' => $countryNL,
                'products' => [
                    [
                        'product_id' => 2,
                        'quantity_shipped' => 2,
                        'quantity_cancelled' => 0,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-01-04",
                'total_price' => 145,
                'country_id' => $countryNL,
                'products' => [
                    [
                        'product_id' => 2,
                        'quantity_shipped' => 2,
                        'quantity_cancelled' => 1,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-01-29",
                'total_price' => 85,
                'country_id' => $countryNL,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 7,
                        'quantity_cancelled' => 0,
                    ],
                ]
            ],
            [
                'ordered_at' => "2023-01-29",
                'total_price' => 100,
                'country_id' => $countryBE,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 3,
                        'quantity_cancelled' => 0,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-02-09",
                'total_price' => 710,
                'country_id' => $countryNL,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 6,
                        'quantity_cancelled' => 1,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-02-16",
                'total_price' => 290,
                'country_id' => $countryNL,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 4,
                        'quantity_cancelled' => 0,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-3-27",
                'total_price' => 100,
                'country_id' => $countryBE,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 1,
                        'quantity_cancelled' => 0,
                    ]
                ]
            ],

            [
                'ordered_at' => "2023-04-04",
                'total_price' => 200,
                'country_id' => $countryNL,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 37,
                        'quantity_cancelled' => 0
                    ],
                    [
                        'product_id' => 2,
                        'quantity_shipped' => 13,
                        'quantity_cancelled' => 0,
                    ]
                ]

            ],
            [
                'ordered_at' => "2023-04-04",
                'total_price' => 190,
                'country_id' => $countryBE,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 6,
                        'quantity_cancelled' => 4,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-04-15",
                'total_price' => 540,
                'country_id' => $countryBE,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 4,
                        'quantity_cancelled' => 0,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-05-5",
                'total_price' => 160,
                'country_id' => $countryBE,
                'products' => [
                    [
                        'product_id' => 1,
                        'quantity_shipped' => 1,
                        'quantity_cancelled' => 0,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-05-13",
                'total_price' => 480,
                'country_id' => $countryBE,
                'products' => [
                    [
                        'product_id' => 2,
                        'quantity_shipped' => 1,
                        'quantity_cancelled' => 0,
                    ]
                ]
            ],
            [
                'ordered_at' => "2023-6-9",
                'total_price' => 100,
                'country_id' => $countryNL,
                'products' => [
                    [
                        'product_id' => 2,
                        'quantity_shipped' => 4,
                        'quantity_cancelled' => 0,
                    ]
                ]
            ]

        ];

        $invoices = [

            [
                'month' => 11,
                'year' => 2022,
                'uid' => '3908410750698',
                'amount' => -188225,
                'vat' => 23857,
                'total_amount' => -212082,
                'journals' => [
                    [
                        'status' => JournalStatus::APPROVED->value,
                        'journal_type' => 'GeneralJournal',
                        'entries' => [
                            [
                                'general_ledger_account' => GeneralLedger::VAT_STANDARD->value,
                                'amount' => 4000,
                                'country_code' => "NL",
                                'entry_date' => "2022-11-05",
                                'description' => GeneralLedger::VAT_STANDARD->description(),
                            ],
                            [
                                'general_ledger_account' => GeneralLedger::VAT_OSS->value,
                                'country_code' => "BE",
                                'amount' => 5000,
                                'entry_date' => "2022-11-17",
                                'description' => GeneralLedger::VAT_OSS->description(),
                            ],

                        ]
                    ]
                ],

            ],
            [
                'month' => 03,
                'year' => 2023,
                'uid' => '3908410750709',
                'amount' => -188225,
                'vat' => 23857,
                'total_amount' => -212082,
                'journals' => [
                    [
                        'status' => JournalStatus::APPROVED->value,
                        'journal_type' => 'GeneralJournal',
                        'entries' => [
                            [
                                'general_ledger_account' => GeneralLedger::VAT_STANDARD->value,
                                'amount' => 4000,
                                'country_code' => "NL",
                                'entry_date' => "2023-03-15",
                                'description' => GeneralLedger::VAT_STANDARD->description(),
                            ],
                            [
                                'general_ledger_account' => GeneralLedger::VAT_OSS->value,
                                'country_code' => "BE",
                                'amount' => 5000,
                                'entry_date' => "2023-03-20",
                                'description' => GeneralLedger::VAT_OSS->description(),
                            ],

                        ]
                    ]
                ],

            ],
            [
                'month' => 05,
                'year' => 2023,
                'uid' => '390841075042704',
                'amount' => -188277,
                'vat' => 23857,
                'total_amount' => -212082,
                'journals' => [
                    [
                        'status' => JournalStatus::APPROVED->value,
                        'journal_type' => 'GeneralJournal',
                        'entries' => [
                            [
                                'general_ledger_account' => GeneralLedger::VAT_STANDARD->value,
                                'amount' => 11000,
                                'country_code' => "NL",
                                'entry_date' => "2023-5-15",
                                'description' => GeneralLedger::VAT_STANDARD->description(),
                            ],
                            [
                                'general_ledger_account' => GeneralLedger::VAT_REDUCED->value,
                                'country_code' => "NL",
                                'amount' => 9000,
                                'entry_date' => "2023-5-18",
                                'description' => GeneralLedger::VAT_REDUCED->description(),
                            ],

                            [
                                'general_ledger_account' => GeneralLedger::VAT_STANDARD->value,
                                'amount' => 15000,
                                'country_code' => "BE",
                                'entry_date' => "2023-5-19",
                                'description' => GeneralLedger::VAT_STANDARD->description(),
                            ],
                            [
                                'general_ledger_account' => GeneralLedger::VAT_OSS->value,
                                'country_code' => "BE",
                                'amount' => 2000,
                                'entry_date' => "2023-5-20",
                                'description' => GeneralLedger::VAT_OSS->description(),
                            ],

                        ]
                    ],

                ],

            ],
            [
                'month' => 6,
                'year' => 2023,
                'uid' => '390841075042706',
                'amount' => -188277,
                'vat' => 23857,
                'total_amount' => -212082,
                'journals' => [
                    [
                        'status' => JournalStatus::APPROVED->value,
                        'journal_type' => 'GeneralJournal',
                        'entries' => [
                            [
                                'general_ledger_account' => GeneralLedger::VAT_STANDARD->value,
                                'amount' => 14000,
                                'country_code' => "NL",
                                'entry_date' => "2023-06-10",
                                'description' => GeneralLedger::VAT_STANDARD->description(),
                            ],
                            [
                                'general_ledger_account' => GeneralLedger::VAT_REDUCED->value,
                                'country_code' => "NL",
                                'amount' => 9000,
                                'entry_date' => "2023-06-18",
                                'description' => GeneralLedger::VAT_REDUCED->description(),
                            ],

                            [
                                'general_ledger_account' => GeneralLedger::VAT_STANDARD->value,
                                'amount' => 15000,
                                'country_code' => "BE",
                                'entry_date' => "2023-06-27",
                                'description' => GeneralLedger::VAT_STANDARD->description(),
                            ]
                        ]
                    ]
                ]

            ]
        ];
        return [
            [
                $products,
                $orders,
                $invoices,
                $dateParams[0],
            ],
            [
                $products,
                $orders,
                $invoices,
                $dateParams[1],
            ]
        ];
    }
}
