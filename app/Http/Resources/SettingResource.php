<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\Management;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'setting_id' => $this->setting_id,
            'user_id' => $this->user_id,
            'key' => $this->key,
            'value' => $this->value,
            'deliverable_channel' => $this->deliverable_channel,
            'management_types' => array_map(fn($case) => ucfirst($case->value), Management::cases()),
        ];
    }
}
