<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;

interface AbstractUserRepositoryInterface
{
    public function getFirstBy(string $key, $value): ?Model;

    public function getByUuid(string $uuid): ?Model;

    public function getAllRecordsQuery(): Builder;

    public function getFirstByOrFail(string $key, $value): ?Model;

    public function getByUuidOrFail(string $uuid): ?Model;

    public function getBy(string $key, $value): ?Collection;

    public function hasInfo(): Builder;

    public function withRelations(string|array $relations): Collection;

    public function saveFile(UploadedFile $avatar, string $path): string;
}
