<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\TemplateTaskRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Base;
use App\Models\TemplateList;
use App\Models\TemplateListTask;

class TemplateTaskRepository  extends AbstractEloquentRepository implements TemplateTaskRepositoryInterface
{
    /**
     * @var TemplateListTask
     */
    protected Base $model;

    public function __construct(TemplateListTask $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes, TemplateList $list): void
    {

        $task = $this->model->create([
            'name' => $attributes['name'],
            'parent_id' => $attributes['parent_id'] ?? null,
            'template_list_id' => $list->id
        ]);
    }

    public function update(TemplateListTask $task, array $attributes): bool
    {
        return $task->fill($attributes)->save();
    }

    public function delete(TemplateListTask $task): bool
    {
        $this->deleteSubTasks($task);

        // Delete the list itself
        return $task->delete();
    }

    private function deleteSubTasks(TemplateListTask $task)
    {
        $subtasks = $task->subtasks;
        foreach ($subtasks as $subtask) {
            $subtask->delete();
        }
    }
}
