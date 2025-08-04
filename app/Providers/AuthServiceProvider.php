<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\File;
use App\Models\User;
use App\Models\Project;
use App\Policies\FilePolicy;

use App\Policies\UserPolicy;
use App\Policies\ProjectPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
        File::class => FilePolicy::class,
        Project::class => ProjectPolicy::class,
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
            return config('app.url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->getEmailForPasswordReset());
        }); 
    }
}
