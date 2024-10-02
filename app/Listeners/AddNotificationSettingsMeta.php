<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\UserCreated;
use App\Models\Settings_meta;
use App\Enums\Settings;
use App\Enums\Management;

class AddNotificationSettingsMeta
{

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        $settings = Settings_meta::where('user_id', $user->id)->first();

        if (!$settings) {
            $notificationSettings = Management::getNotificationSettings();
            foreach ($notificationSettings as $managementType => $notifications) {
                foreach ($notifications as $notification) {
                    Settings_meta::create([
                        'user_id' => $user->id,
                        'type' => 'string',
                        'setting_id' => Settings::NOTIFICATION->value,
                        'key' => $notification['key'],
                        'value' => $notification['label'],
                        'deliverable_channel' => 'database',
                    ]);
                }
            }
        }
    }
}
