<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\ActivityLogRepositoryInterface;
use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;

class ActivityLogRepository extends AbstractEloquentRepository implements ActivityLogRepositoryInterface
{
    protected \App\Models\Base $model;
    
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }

    public function get(?string $keyword, ?string $orderBy, ?string $order): Paginator
    {
        return $this->model
            ->when(!empty($keyword), function ($query) use ($keyword) {
                return $query->filtered($keyword);
            })
            ->when((!empty($orderBy) && !empty($order)), function ($query) use ($orderBy, $order) {
                return $query->ordered($orderBy, $order);
            })
            ->paginate($per_page ?? config('pagination.per_page', 10));
    }
}
