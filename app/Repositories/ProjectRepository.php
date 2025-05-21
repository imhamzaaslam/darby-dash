<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ProjectRepositoryInterface;
use App\Models\Base;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectList;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class ProjectRepository extends AbstractEloquentRepository implements ProjectRepositoryInterface
{
    /**
     * @var Project
     */
    protected Base $model;

    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function getProjectMembersQuery(Project $project): Builder
    {
        $projectMembers = ProjectMember::where('project_id', $project->id)->pluck('user_id');

        return User::whereIn('id', $projectMembers);
    }

    public function getUserProjectsQuery(User $user): Builder
    {
        $projectIds = ProjectMember::where('user_id', $user->id)->pluck('project_id');

        return Project::whereIn('id', $projectIds);
    }

    public function create(array $attributes): Project
    {
        if (isset($attributes['project_logo']) && $attributes['project_logo'] instanceof UploadedFile) {
            $logoPath = $this->saveProjectLogo($attributes['project_logo'], 'images/project_logos');
            $attributes['project_logo'] = $logoPath;
        }
        
        return $this->model->create($attributes);
    }

    public function storeProjectMembers(Project $project, array $member_ids): void
    {
        foreach ($member_ids as $member_id) {
            $role_id = User::find($member_id)->roles->first()->id;
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $member_id,
                'role_id' => $role_id
            ]);
        }
    }

    public function createBacklogList(Project $project): void
    {
        $lists = [
            ['name' => 'Project Kickoff', 'display_order' => 1],
        ];

        foreach ($lists as $list) {
            ProjectList::create([
                'project_id' => $project->id,
                'name' => $list['name'],
                'display_order' => $list['display_order'],
                'is_deletable' => 0
            ]);
        }
    }

    public function update(Project $project, array $attributes): bool
    {
        if (isset($attributes['project_logo']) && $attributes['project_logo'] instanceof UploadedFile) {
            $logoPath = $this->saveProjectLogo($attributes['project_logo'], 'images/project_logos');
            $attributes['project_logo'] = $logoPath;
        }
        
        return $project->fill($attributes)->save();
    }

    public function delete(Project $project): bool
    {
        // Delete related entities
        $this->deleteFolders($project);
        $this->deleteProjectFiles($project);
        $this->detachMembers($project);
        $this->deleteLists($project);
        $this->deleteMilestones($project);
        $this->deleteCalendarEvents($project);

        // Delete the project itself
        return $project->delete();
    }

    private function deleteFolders(Project $project)
    {
        $folders = $project->folders;
        foreach ($folders as $folder) {
            $folder->files()->delete();
            $folder->delete();
        }
    }

    private function deleteProjectFiles(Project $project)
    {
        $project->files()->delete();
    }

    private function detachMembers(Project $project)
    {
        $project->members()->detach();
    }

    private function deleteLists(Project $project)
    {
        $lists = $project->lists;
        foreach ($lists as $list) {
            $tasks = $list->allTasks;
            foreach ($tasks as $task) {
                $task->files()->delete();
                $task->delete();
            }
            $list->delete();
        }
    }

    private function deleteMilestones(Project $project)
    {
        $project->milestones()->delete();
    }

    private function deleteCalendarEvents(Project $project)
    {
        $calendarEvents = $project->calendarEvents;
        foreach ($calendarEvents as $calendarEvent) {
            $calendarEvent->guests()->detach();
            $calendarEvent->delete();
        }
    }

    public function updateProjectMembers(Project $project, array $member_ids): void
    {
        ProjectMember::where('project_id', $project->id)->delete();
        $this->storeProjectMembers($project, $member_ids);
    }

    public function deleteProjectMember(Project $project, User $user): void
    {
        ProjectMember::where(['project_id' => $project->id, 'user_id' => $user->id])->delete();
    }
    
    public function saveProjectLogo($file, $path): string
    {
        $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

        $filename = time() . '-' . $originalName;
        
        $destination = public_path($path);

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return $filename;
    }
}
