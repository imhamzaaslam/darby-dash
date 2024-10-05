<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
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
            'template_name' => $this->template_name,
            'lists' => $this->templateLists->map(function ($list) {
                return [
                    'list_name' => $list->name,
                    'tasks_count' => $list->templateListParentTasks->count()
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
