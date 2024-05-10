<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\RoleRepositoryInterface;
use App\Models\Base;
use App\Models\Role;
use Illuminate\Support\Collection;

class RoleRepository extends AbstractEloquentRepository implements RoleRepositoryInterface
{
    /**
     * @var Role
     */
    protected Base $model;

    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function get(): Collection
    {
        return $this->model->where('name', '!=', 'admin')->get();
    }
}
