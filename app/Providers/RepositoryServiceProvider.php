<?php

namespace App\Providers;

use App\Contracts\ActivityLogRepositoryInterface;
use App\Contracts\ArticleCategoryRepositoryInterface;
use App\Contracts\ArticleRepositoryInterface;
use App\Contracts\CategoryRepositoryInterface;
use App\Contracts\CountryRepositoryInterface;
use App\Contracts\CredentialRepositoryInterface;
use App\Contracts\FileRepositoryInterface;
use App\Contracts\InvoiceRepositoryInterface;
use App\Contracts\JournalEntryRepositoryInterface;
use App\Contracts\JournalRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\PlatformRepositoryInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Contracts\ShopRepositoryInterface;
use App\Contracts\UserInfoRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\VatNumberRepositoryInterface;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\File;
use App\Models\Category;
use App\Models\Country;
use App\Models\Credential;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Platform;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\VatNumber;
use App\Repositories\ActivityLogRepository;
use App\Repositories\ArticleCategoryRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CountryRepository;
use App\Repositories\CredentialRepository;
use App\Repositories\FileRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\JournalEntryRepository;
use App\Repositories\JournalRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PlatformRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShopRepository;
use App\Repositories\UserInfoRepository;
use App\Repositories\UserRepository;
use App\Repositories\VatNumberRepository;
use Illuminate\Support\ServiceProvider;
use App\Models\ActivityLog;

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
            UserRepositoryInterface::class,
            fn() => new UserRepository(new User, app(UserInfoRepositoryInterface::class))
        );

        $this->app->bind(
            UserInfoRepositoryInterface::class,
            fn() => new UserInfoRepository(new UserInfo)
        );

        $this->app->bind(
            CredentialRepositoryInterface::class,
            fn() => new CredentialRepository(new Credential)
        );

        $this->app->bind(
            PlatformRepositoryInterface::class,
            fn() => new PlatformRepository(new Platform)
        );

        $this->app->bind(
            VatNumberRepositoryInterface::class,
            fn() => new VatNumberRepository(new VatNumber)
        );

        $this->app->bind(
            CountryRepositoryInterface::class,
            fn() => new CountryRepository(new Country)
        );

        $this->app->bind(
            InvoiceRepositoryInterface::class,
            fn() => new InvoiceRepository(new Invoice)
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            fn() => new ProductRepository(new Product)
        );

        $this->app->bind(
            OrderRepositoryInterface::class,
            fn() => new OrderRepository(new Order, app(ProductRepositoryInterface::class))
        );

        $this->app->bind(
            ArticleRepositoryInterface::class,
            fn() => new ArticleRepository(new Article)
        );

        $this->app->bind(
            ArticleCategoryRepositoryInterface::class,
            fn() => new ArticleCategoryRepository(new ArticleCategory)
        );

        $this->app->bind(
            JournalRepositoryInterface::class,
            fn() => new JournalRepository(new Journal)
        );

        $this->app->bind(
            JournalEntryRepositoryInterface::class,
            fn() => new JournalEntryRepository(new JournalEntry)
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            fn() => new CategoryRepository(new Category)
        );

        $this->app->bind(
            FileRepositoryInterface::class,
            fn() => new FileRepository(new File)
        );

        $this->app->bind(
            ActivityLogRepositoryInterface::class,
            fn() => new ActivityLogRepository(new ActivityLog)
        );

        $this->app->bind(
            ShopRepositoryInterface::class,
            fn() => new ShopRepository(new Shop, app(CredentialRepositoryInterface::class))
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
