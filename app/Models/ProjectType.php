<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Base;

class ProjectType extends Base
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'display_order',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Project::class)->whereNull('parent_id');
    }

    public function members()
    {
        return $this->hasManyThrough(ProjectMember::class, Project::class);
    }

    public function getAllFiles()
    {
        $files = collect();

        $projects = $this->projects;
        foreach ($projects as $project) {
            $files = $files->merge($project->files);
            $folders = $project->folders;
            foreach ($folders as $folder) {
                $files = $files->merge($folder->files);
            }

            $tasks = $project->tasks;
            foreach ($tasks as $task) {
                $files = $files->merge($task->files);
            }
        }

        return $files;
    }

    // return $this->morphMany(File::class, 'fileable');
}