<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\TemplateRepositoryInterface;
use App\Models\Base;
use App\Models\Project;
use App\Models\Template;
use App\Models\TemplateList;
use App\Models\TemplateListTask;

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
                $templateList->templateListTasks()->create([
                    'template_list_id' => $templateList->id,
                    'name' => $task->name,
                ]);
            }
        }

        return $template;
    }
}
