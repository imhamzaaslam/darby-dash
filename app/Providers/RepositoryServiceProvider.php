<?php

namespace App\Providers;

use App\Contracts\RoleRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\ProjectTypeRepositoryInterface;
use App\Contracts\ProjectRepositoryInterface;
use App\Contracts\TaskRepositoryInterface;
use App\Contracts\ProjectListRepositoryInterface;
use App\Contracts\CalendarFilterRepositoryInterface;
use App\Contracts\StatusRepositoryInterface;
use App\Contracts\MileStoneRepositoryInterface;
use App\Contracts\FileRepositoryInterface;
use App\Contracts\FolderRepositoryInterface;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\ProjectType;
use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectList;
use App\Models\CalendarFilter;
use App\Models\Status;
use App\Models\MileStone;
use App\Models\File;
use App\Models\Folder;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserInfoRepository;
use App\Repositories\ProjectTypeRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\ProjectListRepository;
use App\Repositories\CalendarFilterRepository;
use App\Repositories\StatusRepository;
use App\Repositories\MileStoneRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
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

        $this->app->bind(
            TaskRepositoryInterface::class,
            fn() => new TaskRepository(new Task)
        );

        $this->app->bind(
            ProjectListRepositoryInterface::class,
            fn() => new ProjectListRepository(new ProjectList)
        );

        $this->app->bind(
            CalendarFilterRepositoryInterface::class,
            fn() => new CalendarFilterRepository(new CalendarFilter)
        );

        $this->app->bind(
            StatusRepositoryInterface::class,
            fn() => new StatusRepository(new Status)
        );

        $this->app->bind(
            MileStoneRepositoryInterface::class,
            fn() => new MileStoneRepository(new MileStone)
        );

        $this->app->bind(
            FileRepositoryInterface::class,
            fn() => new FileRepository(new File)
        );

        $this->app->bind(
            FolderRepositoryInterface::class,
            fn() => new FolderRepository(new Folder)
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
