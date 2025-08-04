<?php

namespace App\Providers;

use App\Mail\Transport\ResendEmailServiceTransport;
use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['mail.manager']->extend('resend-transport', function () {
            return new ResendEmailServiceTransport();
        });
    }
}