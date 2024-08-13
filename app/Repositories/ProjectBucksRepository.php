<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ProjectBucksRepositoryInterface;
use App\Models\Base;
use App\Models\Project;
use App\Models\ProjectBucks;
use App\Models\Role;
use Illuminate\Support\Collection;

class ProjectBucksRepository extends AbstractEloquentRepository implements ProjectBucksRepositoryInterface
{
    /**
     * @var ProjectBucks
     */
    protected Base $model;

    public function __construct(ProjectBucks $model)
    {
        parent::__construct($model);
    }

    public function index(Project $project): array
    {
        $roles = Role::all();
        $shares = [];
        foreach ($roles as $role) {
            $projectBuck = $project->projectBucks->where('role_id', $role->id)->first();
            $shares[] = [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'bucks_share' => $projectBuck ? $projectBuck->shares : 0,
                'bucks_share_type' => $project->bucks_share_type,
            ];
        }
        return $shares;
    }
}
