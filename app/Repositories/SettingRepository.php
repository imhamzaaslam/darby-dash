<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\SettingRepositoryInterface;
use App\Models\Base;
use App\Models\Settings_meta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Enums\Management;

class SettingRepository extends AbstractEloquentRepository implements SettingRepositoryInterface
{
    /**
     * @var Settings_meta
     */
    protected Base $model;

    public function __construct(Settings_meta $model)
    {
        parent::__construct($model);
    }

    public function getNotificationSettings(): array
    {
        $userSettings = $this->model->where('user_id', Auth::id())->get();
        $notificationSettings = Management::getNotificationSettings();
        $organizedSettings = [];

        foreach ($notificationSettings as $managementType => $settings) {
            $organizedNotifications = [];

            foreach ($settings as $setting) {
                $userSetting = $userSettings->firstWhere('key', $setting['key']);
                $organizedNotifications[] = [
                    'id' => $userSetting ? $userSetting->id : null,
                    'type' => $setting['label'],
                    'deliverableChannel' => $userSetting ? $userSetting->deliverable_channel : null,
                    'title' => ucfirst($managementType),
                ];
            }

            $organizedSettings[$managementType] = $organizedNotifications;
        }

        return $organizedSettings;
    }

    public function getSetting($id): Settings_meta
    {
        return $this->model->where('id', $id)->first();
    }

    public function updateSetting(Settings_meta $setting, array $data): void
    {
       $setting->fill($data)->save();
    }
}
