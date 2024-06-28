<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;
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
        'est_hours',
        'est_budget',
        'start_date',
        'end_date',
        'status'
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')->with('roles');
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
        return $this->hasMany(Milestone::class);
    }

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
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
}
