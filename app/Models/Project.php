<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;
use App\Services\ProjectProgressService;
use Illuminate\Database\Eloquent\Builder;

class Project extends Base
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'project_type_id',
        'title',
        'description',
        'project_manager_id',
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

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members');
    }

    public function lists()
    {
        return $this->hasMany(ProjectList::class)->orderBy('display_order', 'asc');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
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

    function scopeFiltered(Builder $query, ?string $keyword, ?string $projectTypeId, ?string $projectManagerId): Builder
    {
        $projectsTable = (new Project())->getTable();

        return $query->when($keyword, function ($query, $keyword) use ($projectsTable) {
            return $query->where(function ($query) use ($projectsTable, $keyword) {
                $query->where($projectsTable . '.title', 'like', '%' . $keyword . '%');
            });
        })->when($projectTypeId, function ($query, $projectTypeId) use ($projectsTable) {
            return $query->where($projectsTable . '.project_type_id', $projectTypeId);
        })->when($projectManagerId, function ($query, $projectManagerId) use ($projectsTable) {
            return $query->where($projectsTable . '.project_manager_id', $projectManagerId);
        });
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'asc'): Builder
    {
        return $query->orderBy($orderBy, $order);
    }
}
