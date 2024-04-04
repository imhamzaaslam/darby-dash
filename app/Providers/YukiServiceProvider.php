<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Yuki\YukiClient;

class YukiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(YukiClient::class, function () {
            return new YukiClient(
                url: config('services.yuki.url')
            );
        });
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
