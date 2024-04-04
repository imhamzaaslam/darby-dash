<?php

namespace Tests\Feature\Repositories;

use App\Contracts\CountryRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Models\Platform;
use App\Models\Shop;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Carbon\Carbon;

class OrderRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected OrderRepositoryInterface $repository;
    protected ProductRepositoryInterface $productRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(OrderRepositoryInterface::class);
        $this->productRepository = app(ProductRepositoryInterface::class);
    }

    /** @test */
    public function it_can_create_an_order()
    {
        $user = $this->user('customer');
        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();
        $order = $this->repository->create($shop, $this->order("1042823870.json"));
        $fromDate = Carbon::parse("2019-04-30 00:00:00");
        $toDate = Carbon::parse("2019-04-30 23:59:59");

        //orders tables results test
        $this->assertNotNull($order);
        $this->assertEquals('1042823870', $order->uid);
        $this->assertEquals($shop->uuid, $order->shop->uuid);
        $this->assertEquals(5297, $order->total_price);
        $this->assertEquals(2, $order->products()->count());
        $this->assertEquals("NL", $order->country->code);
        $this->assertEquals(1, ($this->repository->getUserOrders($user, $fromDate, $toDate))->count());

        //orders stats results test
        $this->assertEquals(5297, $this->repository->getRevenue($user, $fromDate, $toDate));
        $this->assertEquals(
            2,
            $this->productRepository->getProductStatsOfCountry($user, $order->country_id, $fromDate, $toDate)['shipped']
        );

        $this->assertEquals(
            2,
            $this->productRepository->getProductStatsOfCountry($user, $order->country_id, $fromDate, $toDate)['difference']
        );

        $this->assertEquals(
            0,
            $this->productRepository->getProductStatsOfCountry($user, $order->country_id, $fromDate, $toDate)['cancelled']
        );
    }


    /** @test */
    public function it_can_update_an_order()
    {
        $user = $this->user('customer');
        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();
        $order = $this->repository->create($shop, $this->order("1042823870.json"));
        $fromDate = Carbon::parse("2019-05-03 00:00:00");
        $toDate = Carbon::parse("2019-05-03 23:59:59");

        $attributes = [
            "ordered_at" => "2019-05-3 13:51:00",
            "total_price" => 5300,
        ];

        $isUpdated = $this->repository->update($order, $attributes);

        //orders tables results test
        $this->assertNotNull($order);
        $this->assertEquals(1042823870, $order->uid);
        $this->assertEquals(5300, $order->total_price);
        $this->assertEquals(2, $order->products()->count());
        $this->assertEquals(1, ($this->repository->getUserOrders($user, $fromDate, $toDate))->count());
        $this->assertTrue($isUpdated);

        //orders stats results test
        $this->assertEquals(5300, $this->repository->getRevenue($user, $fromDate, $toDate));
    }

    /** @test */
    public function it_can_create_two_orders_for_a_customer()
    {
        $user = $this->user('customer');
        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();
        $order1 = $this->repository->create($shop, $this->order("1042823870.json"));
        $order2 = $this->repository->create($shop, $this->order("1042831430.json"));
        $fromDate = Carbon::parse("2019-04-01 00:00:00");
        $toDate = Carbon::parse("2019-04-30 23:59:59");
        $countryNetherlands = app(CountryRepositoryInterface::class)->getFirstBy('code', "NL");
        $countryBelgium = app(CountryRepositoryInterface::class)->getFirstBy('code', "BE");

        $productsShippedCount = 0;
        //counting all shipped products for both orders.
        $productStatsOfOrder1 = $this->productRepository->getProductStatsOfCountry($user, $order1->country_id, $fromDate, $toDate);
        $productsShippedCount += $productStatsOfOrder1['shipped'];
        $productStatsOfOrder2 = $this->productRepository->getProductStatsOfCountry($user, $order2->country_id, $fromDate, $toDate);
        $productsShippedCount += $productStatsOfOrder2['shipped'];

        //orders tables results test
        $this->assertEquals(2, $shop->orders->count());
        $this->assertEquals(2, $this->productRepository->getProductStatsOfCountry($user, $countryNetherlands->id, $fromDate, $toDate)['shipped']); // asserting there are 2 products shipped for NL orders.
        $this->assertEquals(3, $this->productRepository->getProductStatsOfCountry($user, $countryBelgium->id, $fromDate, $toDate)['shipped']); // asserting there are 2 products shipped for BE orders.
        $this->assertEquals((5297+5388), $this->repository->getRevenue($user, $fromDate, $toDate));
        $this->assertEquals(5, $productsShippedCount);
    }

    /** @test */
    public function it_can_delete_an_order()
    {
        $user = $this->user('customer');
        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();
        $order = $this->repository->create($shop, $this->order("1042823870.json"));
        $isDeleted = $this->repository->delete($order);
        $fromDate = Carbon::parse("2019-04-01 00:00:00");
        $toDate = Carbon::parse("2019-04-30 23:59:59");

        //orders tables results test
        $this->assertTrue($isDeleted);
        $this->assertEquals(0, $shop->orders->count());
        $this->assertEquals(0, ($this->repository->getUserOrders($user, $fromDate, $toDate))->count());
        $this->assertEquals(0, $this->repository->getRevenue($user, $fromDate, $toDate));
        $this->assertEquals([], $this->repository->getCountriesTurnover($user, $fromDate, $toDate));
    }

    private function order(string $fileName): array
    {
        $file = File::get("tests/stubs/bol_com/orders/$fileName");

        return json_decode($file, true);
    }

}
