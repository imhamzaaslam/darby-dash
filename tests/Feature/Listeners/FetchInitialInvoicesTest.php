<?php

namespace Tests\Feature\Listeners;

use App\Events\InvoiceFetched;
use App\Events\CredentialCreated;
use App\Listeners\CreateJournal;
use App\Listeners\FetchInitialInvoices;
use App\Models\Credential;
use App\Models\Platform;
use App\Models\Shop;
use App\Services\Bol\BolClient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Mockery\MockInterface;
use Tests\TestCase;

class FetchInitialInvoicesTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        \Hamcrest\Util::registerGlobalFunctions();

        Carbon::setTestNow('2023-03-27 00:00:00');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    /**
     * @test
     */
    public function it_can_create_invoices()
    {
        Event::fake();

        $user = $this->user('customer', ['state' => 'active', 'bookkeeping_started_at' => now()->subMonths(6)]);
        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        $credential = Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        $this->mock(BolClient::class, function (MockInterface $mock) use ($credential) {
            // August - december invoices, requested in months september - january
            $mock->shouldReceive('getInvoices')
                ->with(
                    $credential->shop,
                    anInstanceOf(\Illuminate\Support\Carbon::class),
                    anInstanceOf(\Illuminate\Support\Carbon::class)
                )
                ->times(5)
                ->andReturn([]);

            // january invoice, requested in month february
            $mock->shouldReceive('getInvoices')
                ->with(
                    $credential->shop,
                    anInstanceOf(\Illuminate\Support\Carbon::class),
                    anInstanceOf(\Illuminate\Support\Carbon::class)
                )
                ->once()
                ->andReturn($this->january());

            $mock->shouldReceive('getInvoice')
                ->with($credential->shop, '4500022543922')
                ->once()
                ->andReturn($this->januaryInvoice());

            // february invoice, requested in month march
            $mock->shouldReceive('getInvoices')
                ->with($credential->shop, null, null)
                ->once()
                ->andReturn($this->february());

            $mock->shouldReceive('getInvoice')
                ->with($credential->shop, '3908410750704')
                ->once()
                ->andReturn($this->februaryInvoice());

            $mock->shouldReceive('getInvoice')
                ->with($credential->shop, '3908410670365')
                ->once()
                ->andReturn($this->advertisingInvoice());
        });

        /** @var FetchInitialInvoices $listener */
        $listener = resolve(FetchInitialInvoices::class);
        $listener->handle(new CredentialCreated($credential));

        $shop->refresh();

        $first = $shop->invoices->first();
        $last = $shop->invoices->skip(1)->first();
        $advertising = $shop->invoices->last();

        $this->assertCount(3, $shop->invoices);

        // first (january)
        $this->assertNotNull($first);
        $this->assertEquals($this->januaryInvoice(), json_decode($first->payload, true));
        $this->assertNotNull($first->issued_at);
        $this->assertEquals('01-02-2023', $first->issued_at->format('d-m-Y'));
        $this->assertEquals(2023, $first->year);
        $this->assertEquals(01, $first->month);
        $this->assertTrue($first->issued_at instanceof Carbon);

        Event::assertDispatched(
            fn (InvoiceFetched $event) => $event->invoice->shop->uuid === $shop->uuid &&
                $event->invoice->shop->platform->uuid === $platform->uuid &&
                $event->invoice->uuid == $first->uuid &&
                !empty($event->invoice->getMeta())
        );

        // last (february)
        $this->assertNotNull($last);
        $this->assertEquals($this->februaryInvoice(), json_decode($last->payload, true));
        $this->assertNotNull($last->issued_at);
        $this->assertEquals('01-03-2023', $last->issued_at->format('d-m-Y'));
        $this->assertEquals(2023, $last->year);
        $this->assertEquals(02, $last->month);
        $this->assertTrue($last->issued_at instanceof Carbon);

        Event::assertDispatched(
            fn (InvoiceFetched $event) => $event->invoice->shop->uuid === $shop->uuid &&
                $event->invoice->shop->platform->uuid === $platform->uuid &&
                $event->invoice->uuid == $first->uuid &&
                !empty($event->invoice->getMeta())
        );

        // advertising (february)
        $this->assertNotNull($advertising);
        $this->assertEquals($this->advertisingInvoice(), json_decode($advertising->payload, true));
        $this->assertNotNull($advertising->issued_at);
        $this->assertEquals('01-03-2023', $advertising->issued_at->format('d-m-Y'));
        $this->assertEquals(2023, $advertising->year);
        $this->assertEquals(02, $advertising->month);
        $this->assertTrue($advertising->issued_at instanceof Carbon);

        Event::assertDispatched(
            fn (InvoiceFetched $event) => $event->invoice->shop->uuid === $shop->uuid &&
                $event->invoice->shop->platform->uuid === $platform->uuid &&
                $event->invoice->uuid == $first->uuid &&
                !empty($event->invoice->getMeta())
        );

        Event::assertListening(InvoiceFetched::class, CreateJournal::class);
    }

    private function january(): array
    {
        $file = File::get('tests/stubs/bol_com/invoices.json');

        return json_decode($file, true);
    }

    private function february(): array
    {
        $file = File::get('tests/stubs/bol_com/invoices_2.json');

        return json_decode($file, true);
    }

    private function januaryInvoice(): array
    {
        $file = File::get('tests/stubs/bol_com/invoice.json');

        return json_decode($file, true);
    }

    private function februaryInvoice(): array
    {
        $file = File::get('tests/stubs/bol_com/invoices/invoice_3908410750704.json');

        return json_decode($file, true);
    }

    private function advertisingInvoice(): array
    {
        $file = File::get('tests/stubs/bol_com/invoices/invoice_3908410670365.json');

        return json_decode($file, true);
    }
}
