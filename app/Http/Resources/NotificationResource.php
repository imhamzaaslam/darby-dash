<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = json_decode($this->data, true);
        $sender = getUser($data['sender_id']) ?? null;
        $img = $sender->avatar ?? null;
        $name = $sender ? $sender->name_first . " " . $sender->name_last : null;
        $isOnline = $sender ? $sender->isOnline() : null;
        $role = $sender ? ucwords($sender->getRoleNames()->first() ?? '') : null;

        $currentDate = Carbon::now()->startOfDay();
        $createdAt = Carbon::parse($this->created_at);
        if ($createdAt->isToday()) {
            $timeDisplay = 'Today';
        } elseif ($createdAt->isYesterday()) {
            $timeDisplay = 'Yesterday';
        } else {
            $timeDisplay = $createdAt->format('d M Y');
        }

        return [
            'id' => $this->id,
            'read_at' => $this->read_at,
            'title' => $data['title'] ?? null,
            'subtitle' => $data['message'] ?? null,
            'time' => $timeDisplay,
            'img' => $img,
            'name' => $name,
            'is_online' => $isOnline,
            'role' => $role,
            'url' => $data['url'] ?? null,
        ];
    }
}
