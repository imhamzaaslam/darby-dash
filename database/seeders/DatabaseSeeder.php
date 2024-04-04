<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            // PermissionsSeeder::class,
            RoleSeeder::class,
            PlatformSeeder::class,
            ShopSeeder::class,
            ArticleSeeder::class,
            ArticleCategorySeeder::class,
            ProductSeeder::class,
            InvoiceSeeder::class,
            JournalSeeder::class,
        ]);
    }
}
