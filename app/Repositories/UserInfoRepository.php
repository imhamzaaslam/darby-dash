<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\UserInfoRepositoryInterface;
use App\Models\Base;
use App\Models\User;
use App\Models\UserInfo;

class UserInfoRepository extends AbstractEloquentRepository implements UserInfoRepositoryInterface
{
    /**
     * @var UserInfo
     */
    protected Base $model;

    public function __construct(UserInfo $model)
    {
        parent::__construct($model);
    }

    public function create(User $user, array $attributes): ?UserInfo
    {
        return $this->model->create([
            'user_id' => $user->id,
            ...$attributes,
        ]);
    }

    public function update(UserInfo $userInfo, array $attributes): bool
    {
        return $userInfo->fill($attributes)->save();
    }

    public function delete(UserInfo $userInfo): bool
    {
        return false;
    }

    public function saveFile($file, $path): string
    {
        $filename = time() . '-' . $file->getClientOriginalName();
        $file->move(resource_path($path), $filename);
        return $filename;
    }
}
