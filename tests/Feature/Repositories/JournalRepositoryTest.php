<?php

namespace Tests\Feature\Repositories;

use App\Contracts\JournalRepositoryInterface;
use App\Enums\JournalStatus;
use App\Enums\JournalType;
use App\Exceptions\InvalidStatusChangeException;
use App\Exceptions\RollbackJournalCreationException;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Platform;
use App\Models\Shop;
use App\Models\UserInfo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JournalRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected JournalRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2023-05-01 00:00:00');

        $this->repository = app(JournalRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    /**
     * @test
     */
    public function it_can_create_a_journal(): void
    {
        $user = $this->user('customer');
        $info = UserInfo::factory()->recycle($user)->create();
        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();
        $invoice = Invoice::factory()->recycle($shop)->create(['issued_at' => now()]);

        $journal = $this->repository->create($invoice, JournalType::MAIN, [
            'administration_coc_number' => $info->coc_number,
            'document_subject' => "{$invoice->getPeriod()->lastOfMonth()->format('Y-m-d')} | {$invoice->uid}",
            'journal_type' => 'GeneralJournal'
        ]);

        $this->assertNotNull($journal);
        $this->assertEquals($shop->uuid, $journal->shop->uuid);
        $this->assertEquals($invoice->uuid, $journal->invoice->uuid);
        $this->assertEquals($info->coc_number, $journal->administration_coc_number);
        $this->assertEquals(JournalStatus::CREATED->value, $journal->status);
        $this->assertEquals(JournalType::MAIN->value, $journal->type);
        $this->assertEquals("2023-04-30 | {$invoice->uid}", $journal->document_subject);
        $this->assertEquals('01/04/2023 - 30/04/2023', $journal->period);
    }

    /** @test */
    public function it_can_update_a_journal(): void
    {
        $shop = Shop::factory()->create();
        $journal = Journal::factory()->recycle($shop)->create();

        $response = $this->repository->update($journal, ['administration_coc_number' => $number = rand(10, 99999)]);

        $this->assertTrue($response);
        $this->assertEquals($number, $journal->administration_coc_number);
    }

    /**
     * @test
     */
    public function it_can_delete_a_journal(): void
    {
        $shop = Shop::factory()->create();
        $journal = Journal::factory()->recycle($shop)->create();
        $response = $this->repository->delete($journal);

        $this->assertTrue($response);
        $this->assertSoftDeleted($journal);
    }

    /** @test */
    public function it_can_add_a_status_to_the_journal(): void
    {
        $shop = Shop::factory()->create();
        $journal = Journal::factory()->recycle($shop)->create(['status' => JournalStatus::CREATED->value]);

        $status = JournalStatus::PENDING;

        $this->repository->addStatus($journal, $status);

        $this->assertEquals($status->value, $journal->status);
    }

    /** @test */
    public function it_cannot_add_an_invalid_status_to_the_journal(): void
    {
        $shop = Shop::factory()->create();
        $journal = Journal::factory()->recycle($shop)->create(['status' => $current = JournalStatus::PENDING->value]);

        $status = JournalStatus::CREATED;

        $this->expectException(InvalidStatusChangeException::class);
        $this->expectExceptionMessage(
            "Cannot change journal status from {$current} to {$status->value}"
        );

        $this->repository->addStatus($journal, $status);
    }

    /** @test */
    public function it_cannot_create_a_rollback_journal_without_an_existing_main_journal()
    {
        $shop = Shop::factory()->create();
        $invoice = Invoice::factory()->recycle($shop)->create(['issued_at' => now()]);

        $this->expectException(RollbackJournalCreationException::class);
        $this->expectExceptionMessage(
            "Cannot create journal with type 'rollback' for invoice $invoice->uuid when there is no journal with type 'main'."
        );

        $this->repository->create($invoice, JournalType::ROLLBACK, []);
    }
}
