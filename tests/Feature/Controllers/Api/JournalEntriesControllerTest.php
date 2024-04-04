<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Shop;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class JournalEntriesControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_update_a_journal_entry(): void
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum');

        $journal = Journal::factory()->recycle(Shop::factory()->recycle($user)->create())->create();
        $entry = JournalEntry::factory()->recycle($journal)->create();

        $payload = [
            'entry_date' => '2023-01-01',
            'amount' => 9999,
        ];

        $response = $this->patchJson("api/v1/admin/journals/{$journal->uuid}/entries/{$entry->id}", $payload);
        $response->assertOk();
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'uuid',
                    'platform',
                    'invoice',
                    'user',
                    'updated_at',
                    'period',
                    'status',
                    'entries',
                ],
            ]
        );

        $entry->refresh();

        $this->assertEquals(9999, $entry->amount);
        $this->assertEquals('2023-01-01', $entry->entry_date->format('Y-m-d'));
    }

    /** @test  */
    public function it_can_delete_a_journal_entry(): void
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum');

        $shop = Shop::factory()->recycle($user)->create();
        $journal = Journal::factory()->recycle($shop)->create();
        $entry = JournalEntry::factory()->recycle($journal)->create();

        $response = $this->deleteJson("api/v1/admin/journals/{$journal->uuid}/entries/{$entry->id}");
        $response->assertStatus(204);

        $this->assertSoftDeleted('journal_entries', [
            'id' => $entry->id,
            'journal_id' => $journal->id,
            'general_ledger_account' => $entry->general_ledger_account,
            'amount' => $entry->amount,
        ]);
    }
}
