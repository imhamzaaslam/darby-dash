<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\AbstractEloquentRepository;
use App\Contracts\CompanyRepositoryInterface;
use App\Models\Base;
use App\Models\Company;

class CompanyRepository extends AbstractEloquentRepository implements CompanyRepositoryInterface
{
    /**
     * @var Company
     */
    protected Base $model;

    public function __construct(Company $model)
    {
        parent::__construct($model);
    }

    public function create(array $attributes): Company
    {
        $company = $this->model->create($attributes);

        return $company;
    }

    public function update(Company $company, string $name): bool
    {
        $company = $company->update([
            'name' => $name,
        ]);

        return $company;
    }

    public function delete(Company $company): bool
    {
        return $company->delete();
    }
}
