<?php

namespace Tests\Feature\Services;

use App\Enums\Platform as PlatformEnum;
use App\Exceptions\UnauthorizedTokenException;
use App\Models\AccessToken;
use App\Models\Credential;
use App\Models\Platform;
use App\Models\Shop;
use App\Services\BaseClient;
use App\Services\Bol\BolClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Tests\TestCase;

class BaseClientTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    protected BolClient $bolClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->bolClient = app(BolClient::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    /**
     * @test
     * @dataProvider provideResponse
     */
    public function it_creates_an_access_token(array $response): void
    {
        $platform = Platform::factory()->create([
            'state' => 'active',
            'client' => PlatformEnum::BOL_COM->value,
        ]);

        $shop = Shop::factory()->recycle($platform)->create();

        $credential = Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        Http::fake([
            'https://login.bol.com/token' => Http::response(json_encode($response)),
            "https://api.bol.com/retailer-demo/orders/*" => Http::response(['foo' => 'bar']),
        ]);

        foreach ($this->orders('3908410750704') as $orderId => $order) {
            $this->bolClient->getOrder($shop, $orderId);
        }

        $actual = $credential->accessToken;

        $this->assertNotNull($actual);
        $this->assertEquals($this->token(), $actual->access_token);
        $this->assertDatabaseCount('access_tokens', 1);
    }

    /**
     * @test
     * @dataProvider provideResponse
     */
    public function it_requests_a_new_access_token_only_when_the_previous_one_is_expired(array $response): void
    {
        // We set the time in the future, to properly test if the token is expired.
        Carbon::setTestNow(now()->addSeconds(200));

        $platform = Platform::factory()->create([
            'state' => 'active',
            'client' => PlatformEnum::BOL_COM->value,
        ]);

        $shop = Shop::factory()->recycle($platform)->create();

        $credential = Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        // We create an access token which has already been expired (10 seconds ago).
        $accessToken = AccessToken::factory()->recycle($credential)->create([
            'expires_at' => now()->subSeconds(10),
            'access_token' => 'test_initial_access_token',
        ]);

        Http::fake([
            'https://login.bol.com/token' => Http::response(json_encode($response)),
            'https://api.bol.com/retailer-demo/orders/*' => Http::response(['foo' => 'bar']),
        ]);

        $this->bolClient->getOrder($shop, '1380877284');

        $actual = $credential->accessToken;

        $this->assertNotSame($accessToken, $actual);
        $this->assertNull($accessToken->deleted_at);
        $this->assertEquals($this->token(), $actual->access_token);
        $this->assertDatabaseCount('access_tokens', 1);
    }

    /**
     * @test
     * @dataProvider provideResponse
     */
    public function it_cannot_perform_an_unauthenticated_request(array $response): void
    {
        Carbon::setTestNow(now()->addSeconds(300));
        $platform = Platform::factory()->create(['client' => PlatformEnum::BOL_COM->value]);

        $shop = Shop::factory()->recycle($platform)->create();

        $credential = Credential::factory()->recycle($shop)->create([
            'state' => 'active',
        ]);

        Http::fake([
            'https://login.bol.com/token' => Http::response(json_encode($response)),
            'https://api.bol.com/retailer-demo/orders/*' => Http::response(['foo' => 'bar']),
        ]);

        $this->expectException(UnauthorizedTokenException::class);
        $this->expectExceptionMessage('No or expired token, please authenticate first');

        $this->bolClient->getOrder($shop, '1380877284');
    }

    public static function provideResponse(): array
    {
        return [
            [
                [
                    'access_token' => self::token(),
                    'token_type' => "Bearer",
                    'expires_in' => 299,
                    'scope' => 'retailer',
                ]
            ]
        ];
    }

    private function orders(string $invoiceId): array
    {
        $file = File::get("tests/stubs/bol_com/invoices/{$invoiceId}/orders.json");

        $array = json_decode($file, true);
        $keys = [];

        foreach ($array as $order) {
            $keys[] = $order['orderId'];
        }

        return array_combine($keys, $array);
    }

    private static function token(): string
    {
        return 'test_access_token';
    }
}
