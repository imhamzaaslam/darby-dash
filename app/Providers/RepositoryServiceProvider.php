<?php

namespace App\Providers;

use App\Contracts\RoleRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\ProjectTypeRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\ProjectType;
use App\Models\Project;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserInfoRepository;
use App\Repositories\ProjectTypeRepository;
use App\Repositories\ProjectRepository;
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

        $this->app->bind(
            ProjectTypeRepositoryInterface::class,
            fn() => new ProjectTypeRepository(new ProjectType)
        );

        $this->app->bind(
            ProjectRepositoryInterface::class,
            fn() => new ProjectRepository(new Project)
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
