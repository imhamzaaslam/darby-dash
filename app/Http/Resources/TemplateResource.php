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
                    'id' => $list->id,
                    'uuid' => $list->uuid,
                    'name' => $list->name,
                    'tasks' => $list->templateListParentTasks->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'uuid' => $task->uuid,
                            'name' => $task->name,
                            'subtasks' => $task->subtasks,
                        ];
                    }),
                    'tasks_count' => $list->templateListParentTasks->count()
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
