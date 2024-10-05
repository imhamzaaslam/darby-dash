<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\TemplateRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Base;
use App\Models\Project;
use App\Models\Template;

class TemplateRepository  extends AbstractEloquentRepository implements TemplateRepositoryInterface
{
    /**
     * @var Template
     */
    protected Base $model;

    public function __construct(Template $model)
    {
        parent::__construct($model);
    }

    public function get(): Collection
    {
        return $this->model->get();
    }

    public function getTemplatesQuery(): Builder
    {
        return $this->model->whereNotNull('project_id');
    }

    public function getTemplate($id): Template
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $attributes, Project $project): Template
    {

        $template = $this->model->create([
            'template_name' => $attributes['template_name'],
            'project_id' => $project->id
        ]);

        $projectLists = $project->lists;

        foreach ($projectLists as $list) {
            $templateList = $template->templateLists()->create([
                'template_id' => $template->id,
                'name' => $list->name,
            ]);

            foreach ($list->tasks as $task) {
                $templateTask = $templateList->templateListTasks()->create([
                    'template_list_id' => $templateList->id,
                    'name' => $task->name,
                ]);

                if ($task->subtasks) {
                    foreach ($task->subtasks as $subtask) {
                        $templateList->templateListTasks()->create([
                            'template_list_id' => $templateList->id,
                            'name' => $subtask->name,
                            'parent_id' => $templateTask->id,
                        ]);
                    }
                }
            }
        }

        return $template;
    }

    public function createProjectListAndTask(Template $template, Project $project)
    {
        if ($template->templateLists->isNotEmpty()) {
            foreach ($template->templateLists as $templateList) {
                $projectList = $project->lists()->create([
                    'name' => $templateList->name,
                ]);

                if ($templateList->templateListTasks->isNotEmpty()) {
                    foreach ($templateList->templateListParentTasks as $templateListTask) {
                        $projectTask = $projectList->tasks()->create([
                            'name' => $templateListTask->name,
                            'project_id' => $project->id,
                            'list_id' => $projectList->id,
                        ]);

                        if ($templateListTask->subtasks) {
                            foreach ($templateListTask->subtasks as $subtask) {
                                $projectList->allTasks()->create([
                                    'name' => $subtask->name,
                                    'list_id' => $projectList->id,
                                    'project_id' => $project->id,
                                    'parent_id' => $projectTask->id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    public function hasTemplateLists(Template $template): bool
    {
        return $template->templateLists()->count() > 0;
    }

    public function delete(Template $template): bool
    {
        $this->deleteTemplateLists($template);

        // Delete the template itself
        return $template->delete();
    }

    private function deleteTemplateLists(Template $template)
    {
        $lists = $template->templateLists;
        foreach ($lists as $list) {
            $tasks = $list->templateListTasks;
            foreach ($tasks as $task) {
                $task->delete();
            }
            $list->delete();
        }
    }
}
