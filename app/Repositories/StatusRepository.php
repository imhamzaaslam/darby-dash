<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\StatusRepositoryInterface;
use App\Models\Base;
use App\Models\Status;
use Illuminate\Support\Collection;

class StatusRepository extends AbstractEloquentRepository implements StatusRepositoryInterface
{
    /**
     * @var Status
     */
    protected Base $model;

    public function __construct(Status $model)
    {
        parent::__construct($model);
    }
}
