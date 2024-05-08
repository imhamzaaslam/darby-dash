<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ProjectTypeRepositoryInterface;
use App\Models\Base;
use App\Models\ProjectType;

class ProjectTypeRepository extends AbstractEloquentRepository implements ProjectTypeRepositoryInterface
{
    /**
     * @var ProjectType
     */
    protected Base $model;

    public function __construct(ProjectType $model)
    {
        parent::__construct($model);
    }
}
