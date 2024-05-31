<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\CalendarFilterRepositoryInterface;
use App\Models\Base;
use App\Models\CalendarFilter;

class CalendarFilterRepository extends AbstractEloquentRepository implements CalendarFilterRepositoryInterface
{
    /**
     * @var CalendarFilter
     */
    protected Base $model;

    public function __construct(CalendarFilter $model)
    {
        parent::__construct($model);
    }
}
