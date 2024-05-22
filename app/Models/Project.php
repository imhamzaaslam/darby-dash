<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;
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

    public function phases()
    {
        return $this->hasMany(ProjectPhase::class);
    }

    function scopeFiltered(Builder $query, ?string $keyword): Builder
    {
        $projectsTable = (new Project())->getTable();
        $projectTypesTable = (new ProjectType())->getTable();

        return $query->when($keyword, function(Builder $query) use ($keyword, $projectsTable, $projectTypesTable) {
            return $query->where("$projectsTable.id", 'like', '%' . $keyword . '%')
                ->orWhere("$projectsTable.title", 'like', '%' . $keyword . '%')
                ->orWhere("$projectsTable.description", 'like', '%' . $keyword . '%')
                ->orWhere("$projectsTable.est_hours", 'like', '%' . $keyword . '%')
                ->orWhere("$projectsTable.est_budget", 'like', '%' . $keyword . '%')
                ->orWhere("$projectsTable.start_date", 'like', '%' . $keyword . '%')
                ->orWhere("$projectsTable.end_date", 'like', '%' . $keyword . '%')
                ->orWhere("$projectsTable.status", 'like', '%' . $keyword . '%')
                ->orWhereHas('projectType', function (Builder $query) use ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                });
        });
    }

    function scopeOrdered(Builder $query, string $orderBy = 'id', string $order = 'asc'): Builder
    {
        return $query->orderBy($orderBy, $order);
    }
}
