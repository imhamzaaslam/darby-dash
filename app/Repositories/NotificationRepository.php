<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\NotificationRepositoryInterface;
use App\Models\Base;
use App\Models\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class NotificationRepository extends AbstractEloquentRepository implements NotificationRepositoryInterface
{
    /**
     * @var Notification
     */
    protected Base $model;

    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    public function get(): Collection
    {
        return $this->model->where('notifiable_id', Auth::id())->orderBy('created_at', 'desc')->get();
    }

    public function getNotification($uuid): Notification
    {
        return $this->model->where('id', $uuid)->first();
    }

    public function updateNotification(Notification $notification, array $data): void
    {
       $notification->fill($data)->save();
    }

    public function deleteNotification(Notification $notification): bool
    {
       return $notification->delete();
    }
}
