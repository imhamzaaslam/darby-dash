<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;
use App\Enums\UserRole;

abstract class AbstractUserRepository implements AbstractUserRepositoryInterface
{
    public function __construct(protected User $model)
    {
    }

    public function getFirstBy(string $key, $value): ?User
    {
        return $this->model->where($key, $value)->first();
    }

    public function getByUuid(string $uuid): ?User
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function getAllRecordsQuery(): Builder
    {
        return $this->model->query()->where('company_id', auth()->user()->company_id)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', UserRole::SUPER_ADMIN->value);
            });
    }

    public function getFirstByOrFail(string $key, $value): ?User
    {
        return $this->model->where($key, $value)->firstOrFail();
    }

    public function getByUuidOrFail(string $uuid): ?User
    {
        return $this->model->where('uuid', $uuid)->firstOrFail();
    }

    public function getBy(string $key, $value, int $limit = 0): Collection
    {
        if ($limit !== 0) {
            return $this->model->where($key, $value)->take($limit)->get();
        }

        return $this->model->where($key, $value)->get();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function hasInfo(): Builder
    {
        return $this->model->has('info');
    }

    public function withRelations(string|array $relations): Collection
    {
        return $this->model->with($relations)->get();
    }

    public function saveFile(UploadedFile $avatar, string $path): string
    {
        return $avatar->store($path, 'public');
    }
}
