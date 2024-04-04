<?php

namespace Tests\Feature\Listeners;

use App\Enums\JournalStatus;
use App\Events\InvoiceFetched;
use App\Listeners\CreateJournal;
use App\Models\Credential;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Platform;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\JournalCreatedNotification;
use App\Services\Yuki\JournalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateJournalTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_broadcast_the_journal_created_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();
        $invoice = Invoice::factory()->recycle($shop)->create();
        Credential::factory()->recycle($shop)->create();

        $admins = User::factory()->count(5)->create();

        $admins->each(fn (User $admin) => $admin->assignRole(Role::firstOrCreate(['name' => 'admin'])));

        $this->mock(JournalService::class, function (MockInterface $mock) {
            $mock->shouldReceive('create')->once();
        });

        /** @var CreateJournal $listener */
        $listener = resolve(CreateJournal::class);
        $listener->handle(new InvoiceFetched($invoice));

        Notification::assertCount(5);

        Notification::assertSentTo(
            $admins,
            fn (JournalCreatedNotification $notification, array $channels) =>
                $notification->user->is($user) && $notification->invoice->is($invoice) && $channels[0] === 'database'
        );
    }
}
