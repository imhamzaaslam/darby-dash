<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\TaskRepositoryInterface;
use App\Models\Base;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\Project;
use App\Models\ProjectList;
use Illuminate\Support\Collection;
use Carbon\Carbon;

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
        $display_order = $this->model->where('list_id', $list_id)->whereNull('parent_id')->count();
        $data = array_merge($attributes, ['list_id' => $list_id, 'project_id' => $project_id, 'display_order' => $display_order + 1]);
        return $this->model->create($data);
    }

    public function update(Task $task, array $attributes): bool
    {
        if (isset($attributes['est_time'])) {
            $hours = (int)explode('h', $attributes['est_time'])[0];
            $minutes = (int)explode('m', explode('h', $attributes['est_time'])[1])[0];
            $attributes['est_time'] = $hours * 60 + $minutes;
        }
        if (isset($attributes['assignees'])) {
            $task->assignees()->sync($attributes['assignees']);
        }
        if(isset($attributes['start_date'])) {
            $attributes['start_date'] = Carbon::parse($attributes['start_date']);
        }
        if (isset($attributes['is_bucks_allowed']) && isset($attributes['assignees_bucks'])) {
            foreach ($attributes['assignees_bucks'] as $assignee) {
                $taskAssignee = TaskAssignee::where(['task_id' => $task->id, 'user_id' => $assignee['id']])->first();
                if ($taskAssignee) {
                    $taskAssignee->update(['bucks_amount' => $assignee['bucks_amount']]);
                }
            }
        }
        if(isset($attributes['is_bucks_allowed']) && $attributes['is_bucks_allowed'] == 0) {
            TaskAssignee::where('task_id', $task->id)->update(['bucks_amount' => null]);
        }
        
        return $task->fill($attributes)->save();
    }

    public function delete(Task $task): bool
    {
        // delete related entities
        $task->subtasks()->delete();
        $task->files()->delete();
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
        $statusId = $attributes['status_id'];
        $task->update(['status' => $statusId]);

        return $task;
    }

    public function getMembersForTask(Project $project, Task $task, string $keyword = null): Collection
    {
        $projectMembers = $project->members;

        $taskAssignees = $task->assignees;

        if ($keyword) {
            $projectMembers = $projectMembers->filter(function ($member) use ($keyword) {
                return str_contains($member->name, $keyword) || str_contains($member->email, $keyword);
            });
        }

        return $projectMembers->diff($taskAssignees);
    }

    public function assginMember(Task $task, array $attributes): bool
    {
        $previousAssginees = $task->assignees->pluck('id')->toArray();
        $task->assignees()->sync(array_merge($previousAssginees, [$attributes['assignee']]));
        return true;
    }
    
    public function fetchBucksTasks(Project $project): Collection
    {
        return $this->model->where(['project_id' => $project->id, 'is_bucks_allowed' => 1])->get();
    }
}
