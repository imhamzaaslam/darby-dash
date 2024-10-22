<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = json_decode($this->properties, true);
        $causer = getUser($this->causer_id) ?? null;
        $img = $causer->avatar ?? null;
        $name = $causer ? $causer->name_first . " " . $causer->name_last : null;
        $isOnline = $causer ? $causer->isOnline() : null;
        $role = $causer ? ucwords($causer->getRoleNames()->first() ?? '') : null;

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
            'title' => $data['log_title'] ?? null,
            'subtitle' => $data['log_subtitle'] ?? null,
            'time' => $timeDisplay,
            'img' => $img,
            'name' => $name,
            'is_online' => $isOnline,
            'role' => $role,
        ];
    }
}
