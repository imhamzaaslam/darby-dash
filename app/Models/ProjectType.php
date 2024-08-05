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
        if(auth()->user()->hasRole('Super Admin')){
            return $this->hasMany(Project::class);
        }else{
            return $this->hasMany(Project::class)->whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }
    }

    public function tasks()
    {
        if(auth()->user()->hasRole('Super Admin')){
            return $this->hasManyThrough(Task::class, Project::class)->whereNull('parent_id');
        }else{
            return $this->hasManyThrough(Task::class, Project::class)->whereNull('parent_id')->whereHas('project', function ($query) {
                $query->whereHas('members', function ($query) {
                    $query->where('user_id', auth()->id());
                });
            });
        }
    }

    public function members()
    {
        if(auth()->user()->hasRole('Super Admin')){
            return $this->hasManyThrough(ProjectMember::class, Project::class);
        }else{
            return $this->hasManyThrough(ProjectMember::class, Project::class)->whereHas('project', function ($query) {
                $query->whereHas('members', function ($query) {
                    $query->where('user_id', auth()->id());
                });
            });
        }
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
}