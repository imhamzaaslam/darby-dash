<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'status' => $this->status,
            'project_id' => $this->project_id,
            'due_date' => $this->due_date,
            'completed_at' => $this->completed_at,
            'time_spent' => $this->time_spent,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
