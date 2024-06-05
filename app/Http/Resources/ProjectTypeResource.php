<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTypeResource extends JsonResource
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
            'name' => $this->name,
            'icon' => $this->icon,
            'total_projects' => $this->projects->count(),
            'total_tasks' => $this->tasks->count(),
            'total_members' => $this->members->unique('user_id')->count(),
            'total_files' => 0,
        ];
    }
}