<?php

namespace App\Repositories;

use App\Contracts\AbstractUserRepository;
use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Events\OssRegistrationDateChanged;
use App\Notifications\SendSetPasswordNotification;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Enums\State;
use Illuminate\Support\Collection;

class UserRepository extends AbstractUserRepository implements UserRepositoryInterface
{
    protected User $model;

    protected UserInfoRepositoryInterface $userInfoRepository;

    public function __construct(User $model, UserInfoRepositoryInterface $userInfoRepository)
    {
        parent::__construct($model);
        $this->userInfoRepository = $userInfoRepository;
    }

    public function create(string $role, array $attributes, array $infoAttributes): User
    {
        $user = $this->model->create($attributes);

        $user->assignRole($role);

        $user->notify(new SendSetPasswordNotification());

        $this->userInfoRepository->create($user, $infoAttributes);

        return $user;
    }

    public function update(User $user, array $attributes): bool
    {
        $updated = $user->fill($attributes)->save();

        if ($user->oss_registered_at !== $user->getOriginal('oss_registered_at')) {
            OssRegistrationDateChanged::dispatch($user);
        }

        return $updated;

    }

    public function delete(User $user): bool
    {
        return false;
    }

    public function activate(User $user): bool
    {
        $user->fill(['state' => 'active'])->save();

        if (!$user->emailIsVerified()) {
            return $this->verifyEmail($user);
        }

        return true;
    }

    public function deactivate(User $user): bool
    {
        return $user->fill(['state' => 'inactive'])->save();
    }

    private function verifyEmail(User $user): bool
    {
        return $user->fill(['email_verified_at' => Carbon::now()])->save();
    }

    public function getActiveCustomers(): Collection
    {
        return $this->model->whereState(State::ACTIVE->value)->role('customer')->get();
    }

    public function updateMeta(User $user, array $metaAttributes = []): bool
    {
        $user->setMeta($metaAttributes);
        $user->save();

        return true;
    }

    public function unsetMeta(User $user, string $key): bool
    {
        $user->unsetMeta($key);
        return $user->save();
    }

    public function getAdmins(): Collection
    {
        return $this->model->role('admin')->get();
    }
}
