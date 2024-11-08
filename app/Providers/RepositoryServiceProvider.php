<?php

namespace App\Providers;

use App\Contracts\RoleRepositoryInterface;
use App\Contracts\ActivityLogRepositoryInterface;
use App\Contracts\CompanyRepositoryInterface;
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
use App\Contracts\ChatRepositoryInterface;
use App\Contracts\TemplateRepositoryInterface;
use App\Contracts\TemplateListRepositoryInterface;
use App\Contracts\TemplateTaskRepositoryInterface;
use App\Contracts\ProjectServiceRepositoryInterface;
use App\Contracts\NotificationRepositoryInterface;
use App\Contracts\SettingRepositoryInterface;
use App\Models\Activity;
use App\Models\Role;
use App\Models\Company;
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
use App\Models\Chat;
use App\Models\Template;
use App\Models\TemplateList;
use App\Models\TemplateListTask;
use App\Models\ProjectService;
use App\Models\Notification;
use App\Models\Settings_meta;
use App\Repositories\ActivityLogRepository;
use App\Repositories\RoleRepository;
use App\Repositories\CompanyRepository;
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
use App\Repositories\ChatRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\TemplateListRepository;
use App\Repositories\TemplateTaskRepository;
use App\Repositories\ProjectServiceRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SettingRepository;
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
            ActivityLogRepositoryInterface::class,
            fn() => new ActivityLogRepository(new Activity)
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            fn() => new RoleRepository(new Role)
        );

        $this->app->bind(
            CompanyRepositoryInterface::class,
            fn() => new CompanyRepository(new Company)
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
        $this->app->bind(
            ChatRepositoryInterface::class,
            fn() => new ChatRepository(new Chat)
        );
        $this->app->bind(
            TemplateRepositoryInterface::class,
            fn() => new TemplateRepository(new Template)
        );
        $this->app->bind(
            TemplateListRepositoryInterface::class,
            fn() => new TemplateListRepository(new TemplateList)
        );
        $this->app->bind(
            TemplateTaskRepositoryInterface::class,
            fn() => new TemplateTaskRepository(new TemplateListTask)
        );
        $this->app->bind(
            ProjectServiceRepositoryInterface::class,
            fn() => new ProjectServiceRepository(new ProjectService)
        );
        $this->app->bind(
            NotificationRepositoryInterface::class,
            fn() => new NotificationRepository(new Notification)
        );
        $this->app->bind(
            SettingRepositoryInterface::class,
            fn() => new SettingRepository(new Settings_meta)
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
