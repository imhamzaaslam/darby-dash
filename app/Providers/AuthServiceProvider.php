<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Credential;
use App\Models\File;
use App\Models\Journal;
use App\Models\Platform;
use App\Models\Product;
use App\Models\User;
use App\Models\VatNumber;
use App\Policies\CredentialPolicy;
use App\Policies\FilePolicy;
use App\Policies\JournalPolicy;

use App\Policies\NotificationPolicy;
use App\Policies\PlatformPolicy;
use App\Policies\ProductsPolicy;
use App\Policies\UserPolicy;
use App\Policies\VatNumberPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\DatabaseNotification;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        Credential::class => CredentialPolicy::class,
        VatNumber::class => VatNumberPolicy::class,
        Product::class => ProductsPolicy::class,
        Journal::class => JournalPolicy::class,
        Platform::class => PlatformPolicy::class,
        File::class => FilePolicy::class,
        DatabaseNotification::class => NotificationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.url') . '/reset-password?token=' . $token;
        });
        //
    }
}
