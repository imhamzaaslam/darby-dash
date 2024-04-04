<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Platform;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $platform = Platform::whereClient('Bol.com')->first();
        $months = get_past_months(now())->toArray();
        $shops = Shop::all();

        $shops->each(function (Shop $shop) use ($platform, $months) {
            collect($months)->each(function (Carbon $month) use ($shop, $platform) {
                Invoice::factory()->count(1)
                    ->recycle($platform, $shop)
                    ->create(['month' => $month->month, 'year' => $month->year])
                    ->each(function (Invoice $invoice) use ($platform, $shop) {
                        $this->createOrders($invoice, $platform, $shop);
                    });
            });
        });
    }

    private function createOrders(Invoice $invoice, Platform $platform, Shop $shop): void
    {
        Order::factory()->count(rand(2,4))
            ->recycle($invoice, $platform, $shop)
            ->create()
            ->each(function (Order $order) {
                $randomProducts = Product::inRandomOrder()->take(rand(2,4))->get();
                $order->products()->attach($randomProducts);
            });
    }
}
