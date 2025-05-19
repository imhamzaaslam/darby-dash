<?php

namespace App\Repositories;

use App\Contracts\AbstractUserRepository;
use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Events\OssRegistrationDateChanged;
use App\Events\UserCreated;
// use App\Notifications\SendSetPasswordNotification;
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

        // $user->notify(new SendSetPasswordNotification());

        if (isset($infoAttributes['avatar'])) {
            $avatarPath = $this->saveFile($infoAttributes['avatar'], 'images/avatars');
            $infoAttributes['avatar'] = $avatarPath;
        }
        
        if (isset($infoAttributes['company_logo'])) {
            $companyLogoPath = $this->saveCompanyLogo($infoAttributes['company_logo'], 'images/company_logos');
            $infoAttributes['company_logo'] = $companyLogoPath;
        }

        $this->userInfoRepository->create($user, $infoAttributes);

        event(new UserCreated($user));

        return $user;
    }

    public function update(string $role, User $user, array $attributes): bool
    {
        $updated = $user->fill($attributes)->save();

        if ($role !== $user->getRoleNames()->first()) {
            $user->syncRoles($role);
        }

        return $updated;

    }

    public function delete(User $user): bool
    {
        return $user->delete();
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

    public function getByRole(string $role): Collection
    {
        return $this->model
        ->where('company_id', auth()->user()->company_id)
        ->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->get();
    }

    public function updatePassword(User $user, string $password): bool
    {
        return $user->fill(['password' => bcrypt($password)])->save();
    }

    public function update2FA(User $user, bool $isEnable): bool
    {
        return $user->fill([ 'is_2fa' => $isEnable ])->save();
    }
    
    public function saveCompanyLogo($file, $path): string
    {
        $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

        $filename = time() . '-' . $originalName;
        
        $destination = public_path($path);

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return $filename;
    }
}
