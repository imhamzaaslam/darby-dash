<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BucksTaskResource extends JsonResource
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
            'status' => [
                'id' => $this->getStatus->id,
                'uuid' => $this->getStatus->uuid,
                'name' => $this->getStatus->name,
                'color' => $this->getStatus->color,
            ],
            'project_id' => $this->project_id,
            'list_id' => $this->list_id,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('m/d/Y') : NULL,
            'due_date' => $this->due_date,
            'est_time' => $this->est_time ? convertToHoursAndMinutes($this->est_time) : NULL,
            'est_time_hours' => $this->est_time ? floor($this->est_time / 60) : NULL,
            'est_time_minutes' => $this->est_time ? $this->est_time % 60 : NULL,
            'display_order' => $this->display_order,
            'files_count' => $this->files->count(),
            'assignee' => $this->assignee,
            'is_bucks_allowed' => $this->is_bucks_allowed ? true : false,
            'remaining_bucks' => $this->getRoleWiseRemainingBucks(),
            'assignees_bucks' => $this->getAssigneesBucks(),
            'approval_status' => $this->approval_status,
            'bucks_amount' => $this->bucks_amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}