<?php

namespace Tests\Feature\Repositories;

use App\Contracts\JournalEntryRepositoryInterface;
use App\Enums\GeneralLedger;
use App\Exceptions\DuplicateJournalEntryException;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JournalEntryRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected JournalEntryRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2023-05-01 00:00:00');

        $this->repository = app(JournalEntryRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    /**
     * @test
     * @dataProvider provideCountries
     */
    public function it_can_create_an_entry(?string $code): void
    {
        $user = $this->user('customer');
        $invoice = Invoice::factory()->create(['issued_at' => now()]);
        $journal = Journal::factory()->create(['user_id' => $user->id, 'invoice_id' => $invoice->id]);

        $country = is_null($code) ? null : Country::whereCode($code);

        $entry = $this->repository->create($journal, $generalLedger = GeneralLedger::randomName(), $amount = rand(10, 999999), $country ?? null);

        $this->assertNotNull($entry);
        $this->assertEquals($journal->uuid, $entry->journal->uuid);
        $this->assertEquals($code, $entry->country?->code);
        $this->assertEquals($generalLedger->value, $entry->general_ledger_account);
        $this->assertEquals($generalLedger->description(), $entry->description);
        $this->assertEquals($amount, $entry->amount);
        $this->assertEquals('2023-04-30', $entry->entry_date->format('Y-m-d'));
    }

    public static function provideCountries(): array
    {
        return [
            [null], ['NL'], ['BE'],
        ];
    }

    /** @test */
    public function it_can_update_an_entry(): void
    {
        $entry = JournalEntry::factory()->create();

        $response = $this->repository->update($entry, ['amount' => $amount = rand(10, 99999)]);

        $this->assertTrue($response);
        $this->assertEquals($amount, $entry->amount);
    }

    /**
     * @test
     * @dataProvider provideHardDeleteBooleans
     */
    public function it_can_delete_an_entry(bool $hardDelete): void
    {
        $entry = JournalEntry::factory()->create();
        $array = $entry->toArray();
        $response = $this->repository->delete($entry, $hardDelete);

        $this->assertTrue($response);

        if ($hardDelete) {
            $this->assertDatabaseMissing('journal_entries', $array);
        } else {
            $this->assertSoftDeleted($entry);
        }
    }

    public static function provideHardDeleteBooleans(): array
    {
        return [
            [true], [false]
        ];
    }

    /** @test */
    public function it_cannot_create_duplicate_entries(): void
    {
        $generalLedger = GeneralLedger::randomName();
        $entry = JournalEntry::factory()->create(['general_ledger_account' => $generalLedger->value, 'country_id' => null]);

        $this->expectException(DuplicateJournalEntryException::class);
        $this->expectExceptionMessage(
            sprintf('Entry with general ledger account %s already exists for Journal %s', $generalLedger->value, $entry->journal->uuid)
        );

        $this->repository->create($entry->journal, $generalLedger, rand(10, 999999));
    }
}
