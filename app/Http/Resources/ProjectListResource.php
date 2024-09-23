<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectListResource extends JsonResource
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
            'is_deletable' => $this->is_deletable,
            'progress' => $this->progress(),
            'total_tasks' => $this->total_tasks,
            'completed_tasks' => $this->completed_tasks,
            'tasks' => TaskResource::collection($this->tasks),
        ];
    }
}
