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
        'bucks_amount',
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
    
    public function remaingBucks()
    {
        $assignees = $this->assignees;
        $roleIds = $assignees->map(function ($assignee) {
            return $assignee->roles->first()->id;
        });
        
        // getting shares based on all assigned users roles
        $assignBucksShares = $this->project->projectBucks->whereIn('role_id', $roleIds)->sum('shares');
        
        $allRoleBasedUsersIds = User::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('role_id', $roleIds);
        })->pluck('id');
        
        $assignedTasksIds = TaskAssignee::whereIn('user_id', $allRoleBasedUsersIds)->pluck('task_id')->unique();
        $allocateBucks = Task::whereIn('id', $assignedTasksIds)->where('is_bucks_allowed', true)->sum('bucks_amount');
        
        return number_format($assignBucksShares - $allocateBucks, 2);
    }
}
