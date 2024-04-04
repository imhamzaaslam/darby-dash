<?php

namespace Tests\Feature\Notifications;

use App\Enums\InvoiceType;
use App\Enums\JournalStatus;
use App\Events\OssRegistrationDateChanged;
use App\Helpers\JournalNotificationMessage;
use App\Listeners\BroadcastNotification;
use App\Listeners\UpdateJournal;
use App\Models\Credential;
use App\Models\Journal;
use App\Models\Platform;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\FetchInitialDataNotification;
use App\Notifications\JournalRecalculatedNotification;
use App\Notifications\NewNotification;
use App\Notifications\RecalculateJournalsNotification;
use App\Services\Yuki\JournalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        \Hamcrest\Util::registerGlobalFunctions();
    }

    /** @test */
    public function it_can_listen_to_a_sent_notification(): void
    {
        Event::fake();

        $user = $this->user();
        $this->actingAs($user);

        $user->notify(new FetchInitialDataNotification(\App\Enums\Platform::BOL_COM));

        Event::assertDispatched(NotificationSent::class);
        Event::assertListening(NotificationSent::class, BroadcastNotification::class);
        Event::assertDispatchedTimes(NotificationSent::class);
    }

    /** @test */
    public function it_can_broadcast_notifications(): void
    {
        Notification::fake();

        $this->actingAs($auth = $this->user());

        (new BroadcastNotification())
            ->handle(new NotificationSent(
                $auth,
                new FetchInitialDataNotification(\App\Enums\Platform::BOL_COM),
                'database',
            ));

        Notification::assertSentTo(
            $auth,
            function (NewNotification $notification, array $channels) use ($auth) {
                return $channels[0] === 'broadcast';
            }
        );

        Notification::assertSentTimes(NewNotification::class, 1);
    }

    /** @test */
    public function it_can_broadcast_notifications_two(): void
    {
        Notification::fake();

        $platform = Platform::factory()->create();

        /*
         * We'll be creating 10 test users, with each user having 2 shops.
         */
        $users = User::factory()->count(10)->create();

        $users->each(fn (User $user) => Shop::factory()->count(2)->recycle($user)->recycle($platform)->create());

        $shops = Shop::all();

        $this->assertCount(20, $shops);

        /*
         * Each shop should have 21 test journals, 3 for each status.
         */
        $shops->each(
            fn (Shop $shop) =>
            Journal::factory()->count(21)
                ->sequence(
                    ['status' => JournalStatus::CREATED->value],
                    ['status' => JournalStatus::PENDING->value],
                    ['status' => JournalStatus::APPROVED->value],
                    ['status' => JournalStatus::DISAPPROVED->value],
                    ['status' => JournalStatus::BOOKED->value],
                    ['status' => JournalStatus::FAILED->value],
                    ['status' => JournalStatus::MANUAL->value],
                )
                ->recycle($shop)->recycle($platform)->create()
        );

        // We expect to have 21 x 20 = 420 journals.
        $this->assertCount((21 * $shops->count()), Journal::all());

        $shops->each(fn (Shop $shop) => Credential::factory()->recycle($shop)->recycle($platform)->create());

        /*
         * These are the admins that should receive the notification.
         */
        $admins = User::factory()->count(5)->create();
        $admins->each(fn (User $admin) => $admin->assignRole(Role::firstOrCreate(['name' => 'admin'])));

        /*
         * The user we will use to fire the OssRegistrationDateChanged event.
         */
        $user = $users->first();

        $actualJournals = Journal::whereIn('shop_id', $user->shops->pluck('id'))
            ->whereIn('status', [JournalStatus::PENDING->value, JournalStatus::DISAPPROVED->value]);

        $this->mock(JournalService::class, function (MockInterface $mock) use ($actualJournals) {
            $mock->shouldReceive('update')
                ->with(anInstanceOf(Journal::class), anInstanceOf(InvoiceType::class))
                ->times($actualJournals->count());

            $mock->shouldReceive('createRollbackJournal')
                ->with(anInstanceOf(Journal::class), anInstanceOf(InvoiceType::class))
                ->times($actualJournals->count());
        });

        /** @var UpdateJournal $listener */
        $listener = resolve(UpdateJournal::class);
        $listener->handle(new OssRegistrationDateChanged($user));

        // for every admin there are 2 notifications to be sent.
        // 5 x 2 = 10
        Notification::assertCount(10);

        Notification::assertSentTo(
            $admins,
            function (RecalculateJournalsNotification $notification, array $channels) {
                return $notification->message instanceof JournalNotificationMessage && $channels[0] === 'database';
            }
        );

        Notification::assertSentTo(
            $admins,
            function (JournalRecalculatedNotification $notification, array $channels) {
                return is_int($notification->successfulCount) &&
                    is_int($notification->failedCount) &&
                    $channels[0] === 'database';
            }
        );
    }

    public static function provideAuthentication(): array
    {
        return [
            [true], [false]
        ];
    }
}
