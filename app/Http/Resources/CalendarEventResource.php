<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CalendarEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d H:i') : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d H:i') : null,
            'guests' => $this->guests->pluck('id'),
            'url' => $this->url,
            'project_id' => $this->project_id,
            'display_order' => $this->display_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
