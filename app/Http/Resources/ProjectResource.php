<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

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
                    'id' => $member->id,
                    'name' => $member->name_first,
                    'role' => $member->roles->first()->name ?? 'N/A',
                ];
            }),
            'project_manager' => $this->projectManager() ? new UserResource($this->projectManager()) : null,
            'member_ids' => $this->members->pluck('id'),
            'budget_amount' => round($this->budget_amount, 2),
            'bucks_share' => $this->bucks_share ? round($this->bucks_share, 2) : null,
            'bucks_share_type' => $this->bucks_share_type,
            'bucks_share_amount' => $this->bucks_share_type === 'fixed' ? number_format($this->bucks_share, 2) : number_format($this->bucks_share * $this->budget_amount / 100, 2),
            // 'bucks_share_amount' => $this->bucks_share_type === 'fixed' ? round($this->bucks_share, 2) : round($this->bucks_share * $this->budget_amount / 100, 2),
            'bucks_earnings' => $this->bucksEarnings(),
            'upcoming_events' => $this->upcomingEvents(),
            'total_estimated_hours' => $this->totalEstimatedHoursAndMinutest(),
            'status' => $this->status ?? 'N/A',
            'start_date' => $this->start_date ?? 'N/A',
            'end_date' => $this->end_date ?? 'N/A',
            'is_completed' => $this->is_completed,
            'is_pm_bucks_awarded' => $this->is_pm_bucks_awarded,
            'pm_bucks' => $this->pm_bucks,
            'comments' => $this->comments,
            'is_bucks_share_assigned_to_pm' => $this->is_bucks_share_assigned_to_pm,
            'progress' => $this->progress()['overallProgress'],
            'launching_date' => $this->progress()['launchingDate'],
            'total_tasks' => $this->total_tasks,
            'completed_tasks' => $this->completed_tasks,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
