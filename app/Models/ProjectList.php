<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\ProjectProgressService;
use App\Models\Base;

class ProjectList extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'project_id',
        'name',
        'display_order',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'list_id')->whereNull('parent_id')->orderBy('display_order');
    }

    public function allTasks()
    {
        return $this->hasMany(Task::class, 'list_id')->orderBy('display_order');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getTotalTasksAttribute()
    {
        return $this->tasks()->whereNull('parent_id')->count();
    }

    public function getCompletedTasksAttribute()
    {
        return $this->tasks()->where('status', 3)->whereNull('parent_id')->count();
    }

    public function progress()
    {
        return (new ProjectProgressService())->getProjectListProgress($this);
    }
}
