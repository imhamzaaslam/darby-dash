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
        'approval_status',
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
    
    public function getRoleWiseRemainingBucks()
    {
        $assignees = $this->assignees;
        $roleIds = $assignees->map(function ($assignee) {
            return $assignee->roles->first()->id;
        });
        
        $roleBasedBucks = [];
        foreach ($roleIds as $roleId) {
            // get all users with this role
            $users = User::whereHas('roles', function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })->get();
            
            // get assgin bucks shares for this role, and sum of allocated bucks
            $assignBucksShares = $this->project->projectBucks->where('role_id', $roleId)->pluck('shares')->first();
            $allocatedBucks = TaskAssignee::whereIn('user_id', $users->pluck('id'))->sum('bucks_amount');
            
            $roleBasedBucks[] = [
                'role_id' => $roleId,
                'role_name' => Role::find($roleId)->name,
                'total_bucks' => $assignBucksShares,
                'allocated_bucks' => $allocatedBucks,
                'remaining_bucks' => number_format($assignBucksShares - $allocatedBucks, 2),
            ];
        }
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
}
