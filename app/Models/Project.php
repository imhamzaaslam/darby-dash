<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\ProjectBucks;
use App\Services\ProjectProgressService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'project_type_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'budget_amount',
        'bucks_share',
        'bucks_share_type',
        'is_completed',
        'is_pm_bucks_awarded',
        'comments',
        'completed_at',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                ->with('roles')
                ->wherePivot('deleted_at', null);
    }

    public function projectManager()
    {
        return $this->members->first(function ($member) {
            return $member->roles->contains('id', 2);
        });
    }

    public function lists()
    {
        return $this->hasMany(ProjectList::class)->orderBy('display_order', 'asc');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function uncompletedTasks()
    {
        return $this->hasMany(Task::class)->where('status', '!=', 3);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_members');
    }

    public function progress()
    {
        return (new ProjectProgressService())->getProgress($this);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function milestones()
    {
        return $this->hasMany(MileStone::class);
    }

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function projectBucks()
    {
        return $this->hasMany(ProjectBucks::class);
    }

    public function chat()
    {
        return $this->hasOne(Chat::class);
    }

    public function totalUnseenMessagesOfProject()
    {
        $totalUnSeenCount = ChatMessage::where('sender_id', '!=', auth()->id())
        ->whereHas('chat', function ($query) {
            $query->where('project_id', $this->id);
        })
        ->where('is_seen', false) 
        ->count();

        return $totalUnSeenCount;
    }

    public function bucksEarnings(): float
    {
        $taskIds = $this->tasks->pluck('id');
        $authId = auth()->id();
        $amount = TaskAssignee::whereIn('task_id', $taskIds)->where('user_id', $authId)->where('approval_status', 'approved')->sum('bucks_amount');
        return $amount ? number_format($amount, 2) : '0.00';
    }

    public function upcomingEvents()
    {
        return $this->calendarEvents()->whereDate('start_date', '>=', now())->orderBy('start_date', 'asc')->limit(3)->get();
    }

    public function totalEstimatedHoursAndMinutest()
    {
        $totalMinutes = $this->tasks->sum('est_time');
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
    }

    function scopeFiltered(Builder $query, ?string $keyword, ?string $projectTypeId, ?string $projectManagerId): Builder
    {
        $projectsTable = (new Project())->getTable();
        // projects Members tabel
        $projectMembersTable = (new ProjectMember())->getTable();

        // check projectManager in projectMembers table
        return $query->when($keyword, function ($query, $keyword) use ($projectsTable) {
            return $query->where(function ($query) use ($projectsTable, $keyword) {
                $query->where($projectsTable . '.title', 'like', '%' . $keyword . '%');
            });
        })->when($projectTypeId, function ($query, $projectTypeId) use ($projectsTable) {
            return $query->where($projectsTable . '.project_type_id', $projectTypeId);
        })->when($projectManagerId, function ($query, $projectManagerId) use ($projectsTable, $projectMembersTable) {
            return $query->whereHas('users', function ($query) use ($projectManagerId, $projectMembersTable) {
                $query->where($projectMembersTable . '.user_id', $projectManagerId);
            });
        });
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'asc'): Builder
    {
        return $query->orderBy($orderBy, $order);
    }

    public function getTotalTasksAttribute()
    {
        return $this->tasks()->whereNull('parent_id')->count();
    }

    public function getCompletedTasksAttribute()
    {
        return $this->tasks()->where('status', 3)->whereNull('parent_id')->count();
    }

    public function getIsBucksShareAssignedToPmAttribute()
    {
        return $this->projectBucks()->where('role_id', 2)->where('shares', '>', 0)->exists();
    }

    public function getPmBucksAttribute()
    {
        $projectBucks = $this->projectBucks()
        ->where('role_id', 2)
        ->first();

        return $projectBucks ? $projectBucks->shares : null;
    }
}
