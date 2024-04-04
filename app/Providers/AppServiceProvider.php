<?php

namespace App\Providers;

use App\Contracts\JournalEntryRepositoryInterface;
use App\Contracts\JournalRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Helpers\Vat;
use App\Services\Bol\BolClient;
use App\Services\Yuki\JournalService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('vat', fn () => new Vat(ratesPath: base_path() . '/rates.json'));

        $this->app->bind(
            JournalService::class,
        fn () => new JournalService(
            app(JournalRepositoryInterface::class),
            app(JournalEntryRepositoryInterface::class),
            app(OrderRepositoryInterface::class),
            app(ProductRepositoryInterface::class),
            app(BolClient::class)
        ));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
