<?php

namespace Tests\Feature\Listeners;

use App\Events\CredentialCreated;
use App\Listeners\FetchInitialInvoices;
use App\Listeners\FetchInitialOrders;
use App\Models\Credential;
use App\Models\Platform;
use App\Models\Shop;
use App\Services\Bol\BolClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Mockery\MockInterface;
use Tests\TestCase;

class FetchInitialOrdersTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @dataProvider orderProductProvider
     */
    public function it_can_create_initial_orders(string $orderId, int $productsCount, int $totalPrice, array $orderItems): void
    {
        $user = $this->user('customer', [
            'state' => 'active',
            'bookkeeping_started_at' => now()->subMonths(rand(1, 6))
        ]);

        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($user)->recycle($platform)->create();

        $credential = Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        $this->mock(BolClient::class, function (MockInterface $mock) use ($credential) {
            $mock->shouldReceive('getOrders')
                ->with($credential->shop)
                ->once()
                ->andReturn($this->fbbOrders());

            $mock->shouldReceive('getOrders')
                ->with($credential->shop, 'FBR')
                ->once()
                ->andReturn($this->fbrOrders());

            $mock->shouldReceive('getOrder')
                ->with($credential->shop, '1043965710')
                ->once()
                ->andReturn($this->order('1043965710'));

            $mock->shouldReceive('getOrder')
                ->with($credential->shop, '1042823870')
                ->once()
                ->andReturn($this->order('1042823870'));

            $mock->shouldReceive('getOrder')
                ->with($credential->shop, '1043946570')
                ->once()
                ->andReturn($this->order('1043946570'));

            $mock->shouldReceive('getOrder')
                ->with($credential->shop, '1042831430')
                ->once()
                ->andReturn($this->order('1042831430'));

            $mock->shouldReceive('getOrder')
                ->with($credential->shop, 'A4K8290LP0')
                ->once()
                ->andReturn($this->order('A4K8290LP0'));

            $mock->shouldReceive('getOrder')
                ->with($credential->shop, 'B3K8290LP0')
                ->once()
                ->andReturn($this->order('B3K8290LP0'));
        });

        /** @var FetchInitialInvoices $listener */
        $listener = resolve(FetchInitialOrders::class);
        $listener->handle(new CredentialCreated($credential));

        $orders = $shop->refresh()->orders;

        $this->assertCount(6, $orders);

        foreach ($orders as $order) {
            $this->assertEquals($platform->id, $order->shop->platform->id);
            $this->assertNotNull($order->country);
            $this->assertNotNull($order->uid);

            if ($order->uid === $orderId) {
                $this->assertCount($productsCount, $order->products);
                $this->assertEquals($totalPrice, $order->total_price);

                foreach ($order->products as $orderProduct) {
                    foreach ($orderItems as $orderItem) {
                        if ($orderProduct->pivot->uid === $orderItem['orderItemId']) {
                            $this->assertEquals($orderItem['quantity'], $orderProduct->pivot->quantity);
                            $this->assertEquals($orderItem['quantityShipped'], $orderProduct->pivot->quantity_shipped);
                            $this->assertEquals($orderItem['quantityCancelled'], $orderProduct->pivot->quantity_cancelled);
                            $this->assertEquals($orderItem['unitPrice'], $orderProduct->pivot->price);
                        }
                    }
                }
            }
        }
    }

    public static function orderProductProvider(): array
    {
        return [
            [
                '1043965710', // orderId
                1, // products count
                4490, // total price,
                [
                    [
                        'orderItemId' => '6107989317',
                        'productId' => '8717418510749',
                        'quantity' => 2,
                        'quantityShipped' => 2,
                        'quantityCancelled' => 0,
                        'unitPrice' => 2245
                    ],
                ],  // orderItemId, productId, quantity, quantityShipped, quantityCancelled, unitPrice
            ],
            [
                '1042823870', // orderId
                2, // products count
                5297, // total price
                [
                    [
                        'orderItemId' => '6107771545',
                        'productId' => '8785056370398',
                        'quantity' => 1,
                        'quantityShipped' => 1,
                        'quantityCancelled' => 0,
                        'unitPrice' => 1999,
                    ],
                    [
                        'orderItemId' => '6107771546',
                        'productId' => '8717418510749',
                        'quantity' => 1,
                        'quantityShipped' => 1,
                        'quantityCancelled' => 0,
                        'unitPrice' => 3298,
                    ],
                ], // orderItemId, productId, quantity, quantityShipped, quantityCancelled, unitPrice
            ],
            [
                '1043946570', // orderId
                1, // products count
                3300, // total price,
                [
                    [
                        'orderItemId' => '6042823871',
                        'productId' => '8717418510749',
                        'quantity' => 3,
                        'quantityShipped' => 3,
                        'quantityCancelled' => 0,
                        'unitPrice' => 1100,
                    ],
                ], // orderItemId, productId, quantity, quantityShipped, quantityCancelled, unitPrice

            ],
            [
                '1042831430', // orderId
                2, // products count
                5388, // total price,
                [
                    [
                        'orderItemId' => '6107331382',
                        'productId' => '8712626055143',
                        'quantity' => 1,
                        'quantityShipped' => 1,
                        'quantityCancelled' => 0,
                        'unitPrice' => 2298,
                    ],
                    [
                        'orderItemId' => '6107331383',
                        'productId' => '8717418510749',
                        'quantity' => 3,
                        'quantityShipped' => 2,
                        'quantityCancelled' => 0,
                        'unitPrice' => 1030,
                    ],
                ], // orderItemId, productId, quantity, quantityShipped, quantityCancelled, unitPrice
            ],
            [
                'A4K8290LP0', // orderId
                1, // products count
                3499, // total price,
                [
                    [
                        'orderItemId' => '2070906705',
                        'productId' => '8718846038683',
                        'quantity' => 1,
                        'quantityShipped' => 1,
                        'quantityCancelled' => 0,
                        'unitPrice' => 3499,
                    ],
                ], // orderItemId, productId, quantity, quantityShipped, quantityCancelled, unitPrice
            ],
            [
                'B3K8290LP0', // orderId
                1, // products count
                3499, // total price,
                [
                    [
                        'orderItemId' => '2070906706',
                        'productId' => '8718846038683',
                        'quantity' => 1,
                        'quantityShipped' => 1,
                        'quantityCancelled' => 0,
                        'unitPrice' => 3499,
                    ],
                ], // orderItemId, productId, quantity, quantityShipped, quantityCancelled, unitPrice
            ],
        ];
    }

    private function fbbOrders(): array
    {
        $file = File::get('tests/stubs/bol_com/orders_fbb.json');

        return json_decode($file, true);
    }

    private function fbrOrders(): array
    {
        $file = File::get('tests/stubs/bol_com/orders_fbr.json');

        return json_decode($file, true);
    }

    private function order(string $orderId): array
    {
        $file = File::get("tests/stubs/bol_com/orders/{$orderId}.json");

        return json_decode($file, true);
    }
}
