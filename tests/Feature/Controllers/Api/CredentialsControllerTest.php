<?php

namespace Tests\Feature\Controllers\Api;

use App\Events\CredentialCreated;
use App\Listeners\FetchInitialInvoices;
use App\Listeners\FetchInitialOrders;
use App\Listeners\SendFetchInitialDataNotification;
use App\Models\Credential;
use App\Models\Platform;
use App\Models\Shop;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class CredentialsControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     * @dataProvider provideRoles
     * @param string $role
     * @param bool $authorized
     * @return void
     */
    public function it_can_get_the_credential_for_a_shop(string $role, bool $authorized): void
    {
        Event::fake();

        $user = $this->user($role);

        $this->actingAs($authorized ? $user : $this->user('customer'));

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = $this->user('customer');
            $uuid = $customer->uuid;
        }

        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($customer ?? $user, $platform)->create();

        $credential = Credential::factory()->recycle($shop)->create();

        $endpoint = "api/v1/users/{$uuid}/shops/{$shop->uuid}/credential";

        $response = $this->getJson($endpoint);

        if (!$authorized) {
            $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN)
                ->assertExactJson(['error' => 'This action is unauthorized.']);

            return;
        }

        $response->assertStatus(ResponseAlias::HTTP_OK)
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $credential->id,
                        'uuid' => $credential->uuid,
                        'shop_uuid' => $shop->uuid,
                        'created_at' => $credential->created_at,
                        'updated_at' => $credential->updated_at,
                        'name' => $credential->name,
                        'client_id' => $credential->client_id,
                        'client_secret' => $credential->client_secret,
                        'state' => $credential->state,
                    ]
                ]
            );
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_create_a_credential(string $role, bool $authorized): void
    {
        Event::fake();

        $user = $this->user($role);

        $this->actingAs($authorized ? $user : $this->user('customer'));

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = $this->user('customer');
            $uuid = $customer->uuid;
        }

        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($customer ?? $user, $platform)->create();

        $payload = [
            'name' => fake()->company,
            'client_id' => fake()->uuid,
            'client_secret' => fake()->uuid,
        ];

        $endpoint = "api/v1/users/{$uuid}/shops/{$shop->uuid}/credential";

        $response = $this->postJson($endpoint, $payload);

        if (!$authorized) {
            $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN)
                ->assertExactJson(['error' => 'This action is unauthorized.']);

            return;
        }

        $response->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'uuid',
                        'created_at',
                        'updated_at',
                        'name',
                        'client_id',
                        'client_secret',
                        'state',
                    ]
                ]
            );

        $this->assertDatabaseHas('credentials', [
            'shop_id' => $shop->id,
            'name' => $payload['name'],
            'client_id' => $payload['client_id'],
            'client_secret' => $payload['client_secret'],
        ]);

        Event::assertDispatched(
            fn (CredentialCreated $event) => $event->credential->is($shop->credential) && $event->credential->shop->uuid === $shop->uuid
        );

        Event::assertListening(CredentialCreated::class, FetchInitialInvoices::class);
        Event::assertListening(CredentialCreated::class, FetchInitialOrders::class);
        Event::assertListening(CredentialCreated::class, SendFetchInitialDataNotification::class);
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_update_a_credential(string $role, bool $authorized): void
    {
        Event::fake();

        $user = $this->user($role);

        $this->actingAs($authorized ? $user : $this->user('customer'));

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = $this->user('customer');
            $uuid = $customer->uuid;
        }

        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($customer ?? $user, $platform)->create();
        $credential = Credential::factory()->recycle($shop)->create();

        $payload = [
            'name' => fake()->company,
            'client_id' => fake()->uuid,
            'client_secret' => fake()->uuid,
        ];

        $endpoint = "api/v1/users/{$uuid}/shops/{$shop->uuid}/credential";

        $response = $this->patchJson($endpoint, $payload);

        if (!$authorized) {
            $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN)
                ->assertExactJson(['error' => 'This action is unauthorized.']);

            return;
        }

        $response->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'uuid',
                        'created_at',
                        'updated_at',
                        'name',
                        'client_id',
                        'client_secret',
                        'state',
                    ]
                ]
            );

        $credential = $shop->credential;

        $this->assertEquals($payload['name'], $credential->name);
        $this->assertEquals($payload['client_id'], $credential->client_id);
        $this->assertEquals($payload['client_secret'], $credential->client_secret);
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_delete_a_credential(string $role, bool $authorized): void
    {
        Event::fake();

        $user = $this->user($role);

        $this->actingAs($authorized ? $user : $this->user('customer'));

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = $this->user('customer');
            $uuid = $customer->uuid;
        }

        $platform = Platform::factory()->create();
        $shop = Shop::factory()->recycle($customer ?? $user, $platform)->create();
        $credential = Credential::factory()->recycle($shop)->create();

        $this->assertDatabaseHas('credentials', $data = [
            'shop_id' => $shop->id,
            'client_id' => $credential->client_id,
            'client_secret' => $credential->client_secret,
            'name' => $credential->name,
        ]);

        $endpoint = "api/v1/users/{$uuid}/shops/{$shop->uuid}/credential";

        $response = $this->deleteJson($endpoint);

        if (!$authorized) {
            $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN)
                ->assertExactJson(['error' => 'This action is unauthorized.']);

            return;
        }

        $response->assertStatus(ResponseAlias::HTTP_NO_CONTENT);

        $credential = $shop->credential;
        $this->assertNull($credential);
        $this->assertSoftDeleted('credentials', $data);
    }

    public static function provideRoles(): array
    {
        return [
            ['customer', true],
            ['admin', true],
            ['customer', false],
        ];
    }
}
