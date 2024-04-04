<?php

namespace Tests\Feature\Services\Bol;

use App\Enums\Platform as PlatformEnum;
use App\Exceptions\InvalidTokenException;
use App\Models\Credential;
use App\Models\Platform;
use App\Models\Shop;
use App\Services\Bol\AuthClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthClientTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function it_gets_an_access_token()
    {
        $platform = Platform::factory()->create(['client' => PlatformEnum::BOL_COM->value]);
        $shop = Shop::factory()->recycle($platform)->create();
        $credentials = Credential::factory()->recycle($shop)->create();
        $credentialString = base64_encode(sprintf('%s:%s', $credentials->client_id, $credentials->client_secret));

        $array = $this->response();

        Http::fake([
            'https://login.bol.com/token' => Http::response(json_encode($array)),
        ]);

        $authClient = new AuthClient(credential: $credentials);
        $authClient->authenticate();

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => "Basic {$credentialString}",
        ];

        Http::assertSent(function (Request $request) use ($headers) {
            return $request->hasHeaders($headers) &&
                $request->url() == 'https://login.bol.com/token' &&
                $request['grant_type'] === 'client_credentials';
        });

        Http::assertSentCount(1);

        $token = $authClient->getToken();

        $this->assertNotEmpty($token);
        $this->assertEquals($array['access_token'], $token['access_token']);
        $this->assertEquals($array['token_type'], $token['token_type']);
        $this->assertEquals($array['expires_in'], $token['expires_in']);
        $this->assertEquals($array['scope'], $token['scope']);
        $this->assertEquals(
            now()->addSeconds($array['expires_in'])->format('d-m-Y H:i:s'),
            Carbon::createFromTimestamp($token['expires_at'])->format('d-m-Y H:i:s')
        );
    }

    private function response(): array
    {
        return [
            'access_token' => $this->faker->uuid,
            'token_type' => "Bearer",
            'expires_in' => 299,
            'scope' => 'retailer'
        ];
    }

    /**
     * @test
     * @dataProvider responseProvider
     */
    public function it_fails_validation_on_an_invalid_response(array $body, string $message)
    {
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage($message);

        $platform = Platform::factory()->create(['client' => PlatformEnum::BOL_COM->value]);
        $shop = Shop::factory()->recycle($platform)->create();
        $credentials = Credential::factory()->recycle($shop)->create();

        $authClient = new AuthClient(credential: $credentials);
        $authClient->validateToken($body);
    }

    public static function responseProvider(): array
    {
        return [
            [
                [
                    'access_token' => '',
                    'expires_in' => 299,
                    'token_type' => "Bearer",
                    'scope' => 'retailer'
                ],
                'Missing access_token'
            ],
            [
                [
                    'access_token' => 'access_token',
                    'expires_in' => '',
                    'token_type' => "Bearer",
                    'scope' => 'retailer'
                ],
                'Missing expires_in',
            ],
            [
                [
                    'access_token' => 'access_token',
                    'expires_in' => 299,
                    'token_type' => 'not bearer',
                    'scope' => 'retailer',
                ],
                sprintf('Unexpected token_type \'%s\', expected \'Bearer\'', 'not bearer')
            ],
            [
                [
                    'access_token' => 'access_token',
                    'expires_in' => 299,
                    'token_type' => 'bearer',
                    'scope' => 'advertising',
                ],
                sprintf('Unexpected scope \'%s\', expected \'RETAILER\'', 'advertising')
            ]
        ];
    }
}
