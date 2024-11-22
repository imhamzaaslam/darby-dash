<?php

namespace App\Services;

use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Database\Eloquent\Model;

class TenantService
{
    /**
     * Add a record to a tenant's database.
     *
     * @param string $tenantId
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function addRecord(string $tenantId, Model $model, array $data): Model
    {
        return Tenancy::run($tenantId, function () use ($model, $data) {
            return $model->create($data);
        });
    }

    /**
     * Update a record in a tenant's database.
     *
     * @param string $tenantId
     * @param Model $model
     * @param array $criteria
     * @param array $data
     * @return bool
     */
    public function updateRecord(string $tenantId, Model $model, array $criteria, array $data): bool
    {
        return Tenancy::run($tenantId, function () use ($model, $criteria, $data) {
            return $model->where($criteria)->update($data) > 0;
        });
    }

    /**
     * Remove a record from a tenant's database.
     *
     * @param string $tenantId
     * @param Model $model
     * @param array $criteria
     * @return bool
     */
    public function removeRecord(string $tenantId, Model $model, array $criteria): bool
    {
        return Tenancy::run($tenantId, function () use ($model, $criteria) {
            return $model->where($criteria)->delete() > 0;
        });
    }

    /**
     * Fetch records from a tenant's database.
     *
     * @param string $tenantId
     * @param Model $model
     * @param array $criteria
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fetchRecords(string $tenantId, Model $model, array $criteria = []): \Illuminate\Database\Eloquent\Collection
    {
        return Tenancy::run($tenantId, function () use ($model, $criteria) {
            return $model->where($criteria)->get();
        });
    }

    /**
     * Fetch a single record from a tenant's database.
     *
     * @param string $tenantId
     * @param Model $model
     * @param array $criteria
     * @return Model|null
     */
    public function fetchSingleRecord(string $tenantId, Model $model, array $criteria): ?Model
    {
        return Tenancy::run($tenantId, function () use ($model, $criteria) {
            return $model->where($criteria)->first();
        });
    }
}
