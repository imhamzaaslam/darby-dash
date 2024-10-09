<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectServiceResource extends JsonResource
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
            'title' => $this->title,
            'project_type_id' => $this->project_type_id,
            'service_type' => $this->projectType->name ?? 'N/A',
            'description' => $this->description,
            'status' => $this->status,
            'image' => $this->serviceImage,
            'created_at' => $this->created_at,
        ];
    }
}
