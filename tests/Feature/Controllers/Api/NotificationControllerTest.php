<?php

namespace Controllers\Api;

use App\Notifications\FetchInitialDataNotification;
use App\Notifications\JournalRecalculatedNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @dataProvider provideResources
     */
    public function it_can_get_all_notifications(bool $ownResource): void
    {
        Event::fake();

        $user = $this->user();
        $this->actingAs($user);

        $uuid = $user->uuid;

        if (!$ownResource) {
            $randomAdmin = $this->user();
            $uuid = $randomAdmin->uuid;
            $randomAdmin->notifyNow(new FetchInitialDataNotification(\App\Enums\Platform::BOL_COM));
        } else {
            $user->notifyNow(new JournalRecalculatedNotification(3, 1));
        }

        $response = $this->getJson("api/v1/users/{$uuid}/notifications");

        if (!$ownResource) {
            $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
            return;
        }

        $response->assertOk()
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'id',
                            'type',
                            'notifiable_type',
                            'notifiable_id',
                            'data',
                            'created_at',
                            'updated_at',
                            'read_at',
                        ],
                    ]
                ]
            );

        $this->assertDatabaseCount('notifications', 1);
    }

    /**
     * @test
     * @dataProvider provideResources
     */
    public function it_can_retrieve_a_notification(bool $ownResource): void
    {
        Event::fake();

        $user = $this->user();
        $this->actingAs($user);

        $uuid = $user->uuid;

        if (!$ownResource) {
            $randomAdmin = $this->user();
            $uuid = $randomAdmin->uuid;
            $randomAdmin->notifyNow(new FetchInitialDataNotification(\App\Enums\Platform::BOL_COM), ['database']);
            $notificationId = $randomAdmin->notifications()->first()->id;
        } else {
            $user->notifyNow(new FetchInitialDataNotification(\App\Enums\Platform::BOL_COM), ['database']);
            $notificationId = $user->notifications()->first()->id;
        }

        $response = $this->getJson("api/v1/users/{$uuid}/notifications/{$notificationId}");

        if (!$ownResource) {
            $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
            return;
        }

        $response->assertOk()
            ->assertJsonStructure(
                [
                    'id',
                    'type',
                    'notifiable_type',
                    'notifiable_id',
                    'data',
                    'created_at',
                    'updated_at',
                    'read_at',
                ]
            );

        $this->assertDatabaseCount('notifications', 1);
    }

    /**
     * @test
     * @dataProvider provideResources
     */
    public function it_can_update_a_notification(bool $ownResource): void
    {
        Event::fake();

        $user = $this->user();
        $this->actingAs($user);

        $uuid = $user->uuid;

        if (!$ownResource) {
            $randomAdmin = $this->user();
            $uuid = $randomAdmin->uuid;
            $randomAdmin->notify(new FetchInitialDataNotification(\App\Enums\Platform::BOL_COM));
            $notification = $randomAdmin->notifications()->first();
        } else {
            $user->notify(new FetchInitialDataNotification(\App\Enums\Platform::BOL_COM));
            $notification = $user->notifications()->first();
        }

        $readAt = $notification->read_at;

        $response = $this->patchJson("api/v1/users/{$uuid}/notifications/{$notification->id}");

        if (!$ownResource) {
            $response->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
            return;
        }

        $response->assertOk()
            ->assertJsonStructure(
                [
                    'id',
                    'type',
                    'notifiable_type',
                    'notifiable_id',
                    'data',
                    'created_at',
                    'updated_at',
                    'read_at',
                ]
            );

        $user->refresh();
        $this->assertEmpty($user->unreadNotifications()->get());
        $this->assertCount(1, $user->readNotifications()->get());

        $this->assertNotEquals($readAt, $notification->refresh()->read_at);
    }


    public static function provideResources(): array
    {
        return [
            'can retrieve own resources' => [true],
            'can retrieve other resources' => [false],
        ];
    }
}
