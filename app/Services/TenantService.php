<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenantService
{
    public function setTenant($companyName)
    {
        $tenantIdentifier = strtolower(str_replace(' ', '_', $companyName));
        $tenant = Tenant::where('id', $tenantIdentifier)->first();

        if (!$tenant) {
            return null;
        }

        $this->setConnection($tenant->tenancy_db_name);

        return $tenant;
    }

    /**
     * Add a record to a tenant's database.
     *
     * @param string $tenantId
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function create(string $tenantId, Model $model, array $data): Model
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
    public function update(string $tenantId, Model $model, array $criteria, array $data): bool
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
    public function remove(string $tenantId, Model $model, array $criteria): bool
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
    public function getAll(string $tenantId, Model $model, array $criteria = []): \Illuminate\Database\Eloquent\Collection
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
    public function show(string $tenantId, Model $model, array $criteria): ?Model
    {
        return Tenancy::run($tenantId, function () use ($model, $criteria) {
            return $model->where($criteria)->first();
        });
    }

    private function setConnection($dbName)
    {
        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $dbName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => 'InnoDB',
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    public function resetTenant()
    {
        Config::set('database.connections.tenant', []);
    }
}
