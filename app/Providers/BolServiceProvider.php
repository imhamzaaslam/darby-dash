<?php

namespace App\Providers;

use App\Enums\Platform;
use Illuminate\Support\ServiceProvider;
use App\Services\Bol\BolClient;
use App\Services\Bol\AuthClient;
use App\Models\Credential;
use App\Models\User;
use App\Models\AccessToken;

class BolServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(BolClient::class, function () {
            return new BolClient();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
