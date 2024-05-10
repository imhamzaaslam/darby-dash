<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'title' => $this->title ?? 'N/A',
            'description' => $this->description ?? 'N/A',
            'project_type_id' => $this->projectType->name ?? 'N/A',
            'project_manager' => $this->projectManager ? $this->projectManager->name_first . ' ' . $this->projectManager->name_last : 'N/A',
            'project_members' => $this->members->count() > 0 ? $this->members->pluck('name_first')->implode(', ') : 'N/A',
            'project_manager_id' => $this->project_manager_id,
            'member_ids' => $this->members->pluck('id'),
            'est_hours' => $this->est_hours ?? 'N/A',
            'est_budget' => $this->est_budget ?? 'N/A',
            'status' => $this->status ?? 'N/A',
            'start_date' => $this->start_date ?? 'N/A',
            'end_date' => $this->end_date ?? 'N/A',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
