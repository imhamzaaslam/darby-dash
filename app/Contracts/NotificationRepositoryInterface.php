<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use App\Models\Notification;

interface NotificationRepositoryInterface
{
    public function get(): Collection;

    public function getNotification($uuid): Notification;

    public function updateNotification(Notification $notification, array $data): void;

    public function deleteNotification(Notification $notification): bool;
}
