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
use App\Contracts\CalendarEventRepositoryInterface;
use App\Contracts\FileRepositoryInterface;
use App\Contracts\FolderRepositoryInterface;
use App\Contracts\PaymentRepositoryInterface;
use App\Contracts\ProjectBucksRepositoryInterface;
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
use App\Models\CalendarEvent;
use App\Models\File;
use App\Models\Folder;
use App\Models\Payment;
use App\Models\ProjectBucks;
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
use App\Repositories\CalendarEventRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProjectBucksRepository;
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
            CalendarEventRepositoryInterface::class,
            fn() => new CalendarEventRepository(new CalendarEvent)
        );
        $this->app->bind(
            FileRepositoryInterface::class,
            fn() => new FileRepository(new File)
        );
        $this->app->bind(
            FolderRepositoryInterface::class,
            fn() => new FolderRepository(new Folder)
        );
        $this->app->bind(
            PaymentRepositoryInterface::class,
            fn() => new PaymentRepository(new Payment)
        );
        $this->app->bind(
            ProjectBucksRepositoryInterface::class,
            fn() => new ProjectBucksRepository(new ProjectBucks)
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
