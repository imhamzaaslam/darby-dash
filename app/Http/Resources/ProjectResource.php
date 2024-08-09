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
            'project_type_id' => $this->project_type_id,
            'project_type' => $this->projectType->name ?? 'N/A',
            'project_members' => $this->members->map(function ($member) {
                return [
                    'name' => $member->name_first,
                    'role' => $member->roles->first()->name ?? 'N/A',
                ];
            }),
            'project_manager' => $this->projectManager(),
            'member_ids' => $this->members->pluck('id'),
            'est_hours' => $this->est_hours ?? 'N/A',
            'est_budget' => $this->est_budget ?? 'N/A',
            'budget_amount' => $this->budget_amount,
            'bucks_share' => $this->bucks_share,
            'bucks_share_type' => $this->bucks_share_type,
            'status' => $this->status ?? 'N/A',
            'start_date' => $this->start_date ?? 'N/A',
            'end_date' => $this->end_date ?? 'N/A',
            'progress' => $this->progress()['overallProgress'],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
