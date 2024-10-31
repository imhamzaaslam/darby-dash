<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ProjectBucksRepositoryInterface;
use App\Models\Base;
use App\Models\Project;
use App\Models\ProjectBucks;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Enums\UserRole;
use Illuminate\Support\Collection;

class ProjectBucksRepository extends AbstractEloquentRepository implements ProjectBucksRepositoryInterface
{
    /**
     * @var ProjectBucks
     */
    protected Base $model;

    public function __construct(ProjectBucks $model)
    {
        parent::__construct($model);
    }

    public function index(Project $project): Collection
    {
        $roles = Role::where('name', '!=', UserRole::ADMIN)->get();
        $shares = collect();
        foreach ($roles as $role) {
            $projectBuck = $project->projectBucks->where('role_id', $role->id)->first();
            $shares->push([
                'role_id' => $role->id,
                'role_name' => $role->name,
                'bucks_share' => $projectBuck ? number_format($projectBuck->shares, 2) : '0.00',
                'bucks_share_type' => $project->bucks_share_type,
            ]);
        }
        return $shares;
    }
    
    public function update(Project $project, array $data): ProjectBucks
    {
        $projectBuck = $project->projectBucks->where('role_id', $data['roleId'])->first();
        $data['shares'] = number_format($data['shares'], 2); 
        if ($projectBuck) {
            $projectBuck->update([
                'shares' => $data['shares'],
            ]);
            return $projectBuck;
        }
        return $project->projectBucks()->create([
            'role_id' => $data['roleId'],
            'shares' => $data['shares'],
        ]);
    }
    
    public function updateTaskApprovalStatus(Task $task, array $attributes): bool
    {
        $assignee = TaskAssignee::where(['task_id' => $task->id, 'user_id' => $attributes['user_id']])->first();
        return $assignee->fill($attributes)->save();
    }
    
    public function getRoleShare(Project $project, int $roleId): string
    {
        $projectBuck = $project->projectBucks->where('role_id', $roleId)->first();
        return $projectBuck ? number_format($projectBuck->shares, 2) : '0.00';
    }
}
