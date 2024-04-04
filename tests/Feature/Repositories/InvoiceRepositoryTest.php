<?php

namespace Tests\Feature\Repositories;

use App\Contracts\InvoiceRepositoryInterface;
use App\Enums\InvoiceLineType;
use App\Enums\InvoiceType;
use App\Models\Credential;
use App\Models\Platform;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InvoiceRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected InvoiceRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = app(InvoiceRepositoryInterface::class);
    }

    /**
     * @test
     * @dataProvider invoiceLineTypeProvider
     */
    public function it_can_create_an_invoice(array $invoiceLineTypes): void
    {
        Event::fake();

        $user = $this->user('customer');
        $shop = Shop::factory()->recycle($user)->create();

        Credential::factory()->recycle($shop)->recycle($shop->platform)->create([
            'state' => 'active',
        ]);

        $invoice = $this->repository->create($shop, $this->invoice());
        $date = Carbon::createFromTimestampMs($this->invoice()['IssueDate']['value']);
        $meta = $invoice->getMeta()->toArray();

        $this->assertNotNull($invoice);
        $this->assertEquals($this->invoice(), json_decode($invoice->payload, true));
        $this->assertEquals($date->format('d-m-Y'), $invoice->issued_at->format('d-m-Y'));
        $this->assertEquals(2023, $invoice->year);
        $this->assertEquals(02, $invoice->month);
        $this->assertEquals(-188225, $invoice->amount);
        $this->assertEquals(23857, $invoice->vat);
        $this->assertEquals(-212082, $invoice->total_amount);
        $this->assertEquals('01-03-2023', $invoice->issued_at->format('d-m-Y'));
        $this->assertTrue($invoice->issued_at instanceof Carbon);
        foreach ($invoiceLineTypes as $type) {
            $this->assertArrayHasKey($type, $meta);

            if ($type === strtolower(InvoiceLineType::COMMISSION->name)) {
                $this->assertEquals(52769, $meta[$type]['amount']);
                $this->assertEquals(11081, $meta[$type]['vat']);
                $this->assertNotEmpty($meta[$type]['description']);
            }

            if ($type === strtolower(InvoiceLineType::COMPENSATION_LOST_GOODS->name)) {
                $this->assertEquals(-2011, $meta[$type]['amount']);
                $this->assertEquals(0, $meta[$type]['vat']);
                $this->assertNotEmpty($meta[$type]['description']);
            }

            if ($type === strtolower(InvoiceLineType::CORRECTION_COMMISSION->name)) {
                $this->assertEquals(-2625, $meta[$type]['amount']);
                $this->assertEquals(-551, $meta[$type]['vat']);
                $this->assertNotEmpty($meta[$type]['description']);
            }

            if ($type === strtolower(InvoiceLineType::CORRECTION_DISTRIBUTION_BY_BOLCOM_LABEL->name)) {
                $this->assertEquals(-803, $meta[$type]['amount']);
                $this->assertEquals(-169, $meta[$type]['vat']);
                $this->assertNotEmpty($meta[$type]['description']);
            }

            if ($type === strtolower(InvoiceLineType::CORRECTION_TURNOVER->name)) {
                $this->assertEquals(16741, $meta[$type]['amount']);
                $this->assertEquals(0, $meta[$type]['vat']);
                $this->assertNotEmpty($meta[$type]['description']);
            }

            if ($type === strtolower(InvoiceLineType::DISTRIBUTION_BY_BOLCOM_LABEL->name)) {
                $this->assertEquals(63680, $meta[$type]['amount']);
                $this->assertEquals(13373, $meta[$type]['vat']);
                $this->assertNotEmpty($meta[$type]['description']);
            }

            if ($type === strtolower(InvoiceLineType::PLAZA_RETURN_SHIPPING_LABEL->name)) {
                $this->assertEquals(1118, $meta[$type]['amount']);
                $this->assertEquals(123, $meta[$type]['vat']);
                $this->assertNotEmpty($meta[$type]['description']);
            }

            if ($type === strtolower(InvoiceLineType::TURNOVER->name)) {
                $this->assertEquals(-340951, $meta[$type]['amount']);
                $this->assertEquals(0, $meta[$type]['vat']);
                $this->assertNotEmpty($meta[$type]['description']);
            }
        }
    }

    public static function invoiceLineTypeProvider(): array
    {
        return [
            [
                [
                    strtolower(InvoiceLineType::COMMISSION->name),
                    strtolower(InvoiceLineType::COMPENSATION_LOST_GOODS->name),
                    strtolower(InvoiceLineType::CORRECTION_COMMISSION->name),
                    strtolower(InvoiceLineType::CORRECTION_DISTRIBUTION_BY_BOLCOM_LABEL->name),
                    strtolower(InvoiceLineType::CORRECTION_TURNOVER->name),
                    strtolower(InvoiceLineType::DISTRIBUTION_BY_BOLCOM_LABEL->name),
                    strtolower(InvoiceLineType::PLAZA_RETURN_SHIPPING_LABEL->name),
                    strtolower(InvoiceLineType::TURNOVER->name),
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideAdvertisingNumbers
     */
    public function it_can_create_an_advertising_invoice(string $invoiceId, array $invoiceData): void
    {
        Event::fake();

        $user = $this->user('customer');
        $shop = Shop::factory()->recycle($user)->create();

        Credential::factory()->recycle($shop)->recycle($shop->platform)->create([
            'state' => 'active',
        ]);

        $invoice = $this->repository->create($shop, $this->advertisingInvoice($invoiceId), InvoiceType::ADVERTISING);
        $date = Carbon::createFromTimestampMs($this->advertisingInvoice($invoiceId)['IssueDate']['value']);
        $meta = $invoice->getMeta()->toArray();

        $this->assertNotNull($invoice);
        $this->assertEquals($this->advertisingInvoice($invoiceId), json_decode($invoice->payload, true));
        $this->assertEquals($date->format('d-m-Y'), $invoice->issued_at->format('d-m-Y'));
        $this->assertEquals($invoiceData['year'], $invoice->year);
        $this->assertEquals($invoiceData['month'], $invoice->month);
        $this->assertEquals($invoiceData['amount'], $invoice->amount);
        $this->assertEquals($invoiceData['vat'], $invoice->vat);
        $this->assertEquals($invoiceData['total_amount'], $invoice->total_amount);
        $this->assertEquals($invoiceData['issued_at'], $invoice->issued_at->format('d-m-Y'));
        $this->assertTrue($invoice->issued_at instanceof Carbon);
        $this->assertEquals($invoiceData['sponsored_products_amount'], $meta['sponsored_products']['amount']);
        $this->assertEquals($invoiceData['sponsored_products_vat'], $meta['sponsored_products']['vat']);
        $this->assertNotEmpty($meta['sponsored_products']['description']);
    }

    public static function provideAdvertisingNumbers(): array
    {
        return [
            [
                '3908410670365',
                [
                    'year' => 2023,
                    'month' => 02,
                    'amount' => 53454,
                    'vat' => 11225,
                    'total_amount' => 64679,
                    'issued_at' => '01-03-2023',
                    'sponsored_products_amount' => 53454,
                    'sponsored_products_vat' => 11225,
                ],
            ],
            [
                '3908581226894',
                [
                    'year' => 2023,
                    'month' => 03,
                    'amount' => 90360,
                    'vat' => 18976,
                    'total_amount' => 109336,
                    'issued_at' => '01-04-2023',
                    'sponsored_products_amount' => 90360,
                    'sponsored_products_vat' => 18976,
                ],
            ],
        ];
    }

    private function invoice(): array
    {
        $file = File::get('tests/stubs/bol_com/invoices/invoice_3908410750704.json');

        return json_decode($file, true);
    }

    public function advertisingInvoice(string $invoiceId): array
    {
        $file = File::get("tests/stubs/bol_com/invoices/invoice_{$invoiceId}.json");

        return json_decode($file, true);
    }
}
