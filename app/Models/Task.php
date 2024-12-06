<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TaskAssignee;
use App\Models\User;
use App\Models\Base;

class Task extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'status',
        'project_id',
        'parent_id',
        'list_id',
        'display_order',
        'start_date',
        'due_date',
        'est_time',
        'is_bucks_allowed',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function subtasks()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getStatus()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees')->withTimestamps();
    }

    public function list()
    {
        return $this->belongsTo(ProjectList::class, 'list_id');
    }

    public function getRoleWiseRemainingBucks()
    {
        $assignees = $this->assignees()->with('roles')->get();
        // Get unique role IDs
        $roleIds = $assignees->pluck('roles')->flatten()->pluck('id')->unique();

        // Prepare a collection of users grouped by role_id
        $usersByRole = User::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('role_id', $roleIds);
        })->with('roles')->get()->groupBy(function ($user) {
            return $user->roles->first()->id;
        });

        $roleBasedBucks = [];
        foreach ($roleIds as $roleId) {
            // Get users for this role
            $users = $usersByRole->get($roleId, collect());

            // Get assigned bucks shares for this role, and sum of allocated bucks
            $assignBucksShares = $this->project->projectBucks->where('role_id', $roleId)->pluck('shares')->first() ?? 0;

            $taskIds = $this->project->tasks()->pluck('id');
            $allocatedBucks = TaskAssignee::whereIn('task_id', $taskIds)
                ->whereIn('user_id', $users->pluck('id'))
                ->sum('bucks_amount');

            $roleBasedBucks[] = [
                'role_id' => $roleId,
                'role_name' => Role::find($roleId)->name,
                'total_bucks' => $assignBucksShares,
                'allocated_bucks' => $allocatedBucks,
                'remaining_bucks' => number_format($assignBucksShares - $allocatedBucks, 2),
            ];
        }

        // $assignees = $this->assignees; // get all assignees of this task
        // $roleIds = $assignees->map(function ($assignee) {
        //     return $assignee->roles->first()->id;
        // });

        // $roleBasedBucks = [];
        // foreach ($roleIds as $roleId) {
        //     // get all users with this role
        //     $users = User::whereHas('roles', function ($query) use ($roleId) {
        //         $query->where('role_id', $roleId);
        //     })->get();

        //     // get assgin bucks shares for this role, and sum of allocated bucks
        //     $assignBucksShares = $this->project->projectBucks->where('role_id', $roleId)->pluck('shares')->first();
        //     $taskIds = $this->project->tasks()->pluck('id');
        //     $allocatedBucks = TaskAssignee::whereIn('task_id', $taskIds)->whereIn('user_id', $users->pluck('id'))->sum('bucks_amount');

        //     $roleBasedBucks[] = [
        //         'role_id' => $roleId,
        //         'role_name' => Role::find($roleId)->name,
        //         'total_bucks' => $assignBucksShares,
        //         'allocated_bucks' => $allocatedBucks,
        //         'remaining_bucks' => number_format($assignBucksShares - $allocatedBucks, 2),
        //     ];
        // }
        return $roleBasedBucks;
    }

    public function getAssigneesBucks()
    {
        $assignees = $this->assignees;
        $assigneesBucks = [];
        foreach ($assignees as $assignee) {
            $allocatedBucks = TaskAssignee::where(['task_id' => $this->id, 'user_id' => $assignee->id])->pluck('bucks_amount')->first();

            $assigneesBucks[] = [
                'id' => $assignee->id,
                'user_name' => $assignee->name_first . ' ' . $assignee->name_last,
                'bucks_amount' => $allocatedBucks ? number_format($allocatedBucks, 2) : '',
                'role_id' => $assignee->roles->first()->id,
                'role_name' => $assignee->roles->first()->name,
            ];
        }
        return $assigneesBucks;
    }

    public function scopeFilter($query, $filters)
    {
        if (!empty($filters['searchQuery'])) {
            $query->where('name', 'like', '%' . $filters['searchQuery'] . '%');
        }

        if (!empty($filters['assignees'])) {
            $query->whereHas('assignees', function ($q) use ($filters) {
                $q->whereIn('users.id', $filters['assignees']);
            });
        }

        if (!empty($filters['statuses'])) {
            $query->whereIn('status', $filters['statuses']);
        }

        if (!empty($filters['createdAt'])) {
            $createdAtDates = parseDateRange($filters['createdAt']);
            if ($createdAtDates['from'] && $createdAtDates['to']) {
                $query->whereBetween('created_at', [$createdAtDates['from'], $createdAtDates['to']]);
            }
        }

        if (!empty($filters['dueDate'])) {
            $dueDateDates = parseDateRange($filters['dueDate']);
            if ($dueDateDates['from'] && $dueDateDates['to']) {
                $query->whereBetween('due_date', [$dueDateDates['from'], $dueDateDates['to']]);
            }
        }

        if (!empty($filters['sortBy'])) {
            if ($filters['sortBy'] === 'assignee') {
                $query->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
                      ->join('users', 'task_assignees.user_id', '=', 'users.id')
                      ->orderBy('users.name_first', $filters['sortDirection'] ?? 'asc')
                      ->select('tasks.*');
            } else {
                $query->orderBy($filters['sortBy'], $filters['sortDirection'] ?? 'asc');
            }
        }

        return $query;
    }

    public function getHasBucksShareAttribute()
    {
        return $this->project && $this->project->bucks_share > 0;
    }
}
