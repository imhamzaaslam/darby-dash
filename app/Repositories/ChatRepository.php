<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ChatRepositoryInterface;
use App\Models\Base;
use App\Models\Chat;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;

class ChatRepository extends AbstractEloquentRepository implements ChatRepositoryInterface
{
    /**
     * @var Chat
     */
    protected Base $model;

    public function __construct(Chat $model)
    {
        parent::__construct($model);
    }

    public function getChatsByProject(Project $project): Collection
    {
        return $this->model->where('project_id', $project->id)->get();
    }

    public function getChatByUser(User $user, Project $project): Collection
    {
        return $this->model->where('project_id', $project->id)->where('user_id', $user->id)->get();
    }
}
