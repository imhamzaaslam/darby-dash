<?php

namespace App\Contracts;

use App\Models\User;
use App\Models\UserInfo;

interface UserInfoRepositoryInterface
{
    /**
     * @param User $user
     * @param array $attributes
     *
     * @return UserInfo|null
     */
    public function create(User $user, array $attributes): ?UserInfo;

    /**
     * @param UserInfo $userInfo
     * @param array $attributes
     *
     * @return bool
     */
    public function update(UserInfo $userInfo, array $attributes): bool;

    /**
     * @param UserInfo $userInfo
     *
     * @return bool
     */
    public function delete(UserInfo $userInfo): bool;
}
