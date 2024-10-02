<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use App\Models\Settings_meta;

interface SettingRepositoryInterface
{
    public function getNotificationSettings(): array;

    public function getSetting($id): Settings_meta;

    public function updateSetting(Settings_meta $setting, array $data): void;
}
