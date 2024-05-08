<?php

namespace App\Providers;

use App\Contracts\RoleRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\UserInfoRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserInfoRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            RoleRepositoryInterface::class,
            fn() => new RoleRepository(new Role)
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            fn() => new UserRepository(new User, app(UserInfoRepositoryInterface::class))
        );

        $this->app->bind(
            UserInfoRepositoryInterface::class,
            fn() => new UserInfoRepository(new UserInfo)
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
