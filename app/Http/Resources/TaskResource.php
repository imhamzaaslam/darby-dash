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
            'subtasks' => TaskResource::collection($this->subtasks),
            'description' => $this->description,
            'status' => [
                'id' => $this->getStatus->id,
                'uuid' => $this->getStatus->uuid,
                'name' => $this->getStatus->name,
                'color' => $this->getStatus->color,
            ],
            'project_id' => $this->project_id,
            'list_id' => $this->list_id,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'est_time' => $this->est_time ? convertToHoursAndMinutes($this->est_time) : NULL,
            'display_order' => $this->display_order,
            'files_count' => $this->files->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
