<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\TaskRepositoryInterface;
use App\Models\Base;
use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectList;
use Illuminate\Support\Collection;

class TaskRepository extends AbstractEloquentRepository implements TaskRepositoryInterface
{
    /**
     * @var Task
     */
    protected Base $model;

    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function get(string $projecId): Collection
    {
        return $this->model->where('project_id', $projecId)->get();
    }

    public function create(ProjectList $list, array $attributes): Task
    {
        $list_id = $list->id;
        $project_id = $list->project_id;

        $data = array_merge($attributes, ['list_id' => $list_id, 'project_id' => $project_id]);
        return $this->model->create($data);
    }

    public function update(Task $task, array $attributes): bool
    {
        return $task->fill($attributes)->save();
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function fetchUnlistedTasks(Project $project): Collection
    {
        return $this->model->where(['project_id' => $project->id, 'list_id' => null])->get();
    }

    public function getByProject(Project $project): Collection
    {
        return $this->model->where(['project_id' => $project->id])->get();
    }

    public function createByProject(Project $project, array $attributes): Task
    {
        $data = array_merge($attributes, ['project_id' => $project->id]);
        return $this->model->create($data);
    }

    public function updateTasksOrder(Task $task, array $attributes): Task
    {
        $listId = $attributes['list_id'];
        $this->updateDisplayOrder($attributes['list_tasks']);
        $task->update(['list_id' => $listId]);

        return $task;
    }

    public function updateDisplayOrder(array $listTasks): void
    {
        foreach ($listTasks as $index => $taskDetails) {
            $task = $this->model->find($taskDetails['id']);
            if ($task) {
                $task->update(['display_order' => $index]);
            }
        }
    }

}
