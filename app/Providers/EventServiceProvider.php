<?php

namespace App\Providers;

use App\Events\OssRegistrationDateChanged;
use App\Events\CredentialCreated;
use App\Events\InvoiceFetched;
use App\Events\ProductCategoryChanged;
use App\Listeners\ActivateUser;
use App\Listeners\BroadcastNotification;
use App\Listeners\CreateJournal;
use App\Listeners\FetchInitialInvoices;
use App\Listeners\FetchInitialOrders;
use App\Listeners\SendFetchInitialDataNotification;
use App\Listeners\UpdateJournal;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PasswordReset::class => [
            ActivateUser::class,
        ],
        CredentialCreated::class => [
            SendFetchInitialDataNotification::class,
            FetchInitialInvoices::class,
            FetchInitialOrders::class,
        ],
        InvoiceFetched::class => [
            CreateJournal::class,
        ],
        OssRegistrationDateChanged::class => [
            UpdateJournal::class,
        ],
        ProductCategoryChanged::class => [
            UpdateJournal::class,
        ],
        NotificationSent::class => [
            BroadcastNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
