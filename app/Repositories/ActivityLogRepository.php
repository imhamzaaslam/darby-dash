<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ActivityLogRepositoryInterface;
use App\Models\Activity;
use Illuminate\Support\Collection;

class ActivityLogRepository extends AbstractEloquentRepository implements ActivityLogRepositoryInterface
{
    protected \App\Models\Base $model;
    
    public function __construct(Activity $model)
    {
        parent::__construct($model);
    }

    public function get(string $uuid): Collection
    {
        return $this->model->where('batch_uuid', $uuid)->orderBy('created_at', 'desc')->get();
    }
}
