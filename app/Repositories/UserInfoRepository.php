<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\UserInfoRepositoryInterface;
use App\Models\Base;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\UploadedFile;

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
        if (isset($attributes['company_logo']) && $attributes['company_logo'] instanceof UploadedFile) {
            $avatarPath = $this->saveCompanyLogo($attributes['company_logo'], 'images/company_logos');
            $attributes['company_logo'] = $avatarPath;
        }
        
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
