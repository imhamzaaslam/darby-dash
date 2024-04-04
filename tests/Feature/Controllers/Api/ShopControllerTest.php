<?php

namespace Tests\Feature\Controllers\Api;

use App\Contracts\ShopRepositoryInterface;
use App\Enums\State;
use App\Models\Credential;
use App\Models\Platform;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShopControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    protected ShopRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ShopRepositoryInterface::class);
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_get_the_shops_for_a_user(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        $platform = Platform::factory()->create();

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = User::factory()->create();
            $uuid = $customer->uuid;
        }

        Shop::factory()->count(10)->recycle($customer ?? $user)->recycle($platform)->create();

        $response = $this->getJson("api/v1/users/{$uuid}/shops");
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', 10));
        $response->assertOk()
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'uuid',
                            'user_uuid',
                            'platform_uuid',
                            'name',
                            'description',
                            'state',
                            'has_credentials',
                        ],
                    ]
                ]
            );
    }

    /** @test */
    public function it_cannot_get_the_shops_for_unauthorized_user(): void
    {
        $user = $this->user('customer');
        $unauthorized = $this->user('customer');
        $this->actingAs($unauthorized);

        $platform = Platform::factory()->create();
        $uuid = $user->uuid;

        Shop::factory()->count(10)->recycle($user)->recycle($platform)->create();

        $response = $this->getJson("api/v1/users/{$uuid}/shops");
        $response->assertStatus(403);
        $response->assertExactJson([
            'error' => 'Unauthorized.',
        ]);
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_create_a_shop(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        $platform = Platform::factory()->create();

        $payload = ['name' => $this->faker->company, 'platform' => $platform->name];

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = User::factory()->create();
            $uuid = $customer->uuid;
        }

        $response = $this->postJson("api/v1/users/{$uuid}/shops", $payload);

        if ($role === 'admin') {
            $shop = $customer->shops->first();
        } else {
            $shop = $user->shops->first();
        }

        $response->assertOk()
            ->assertExactJson(
                [
                    'data' => [
                        'uuid' => $shop->uuid,
                        'user_uuid' => $shop->user->uuid,
                        'platform_uuid' => $shop->platform->uuid,
                        'name' => $shop->name,
                        'description' => $shop->description,
                        'state' => $shop->state,
                        'has_credentials' => false,
                    ]
                ]
            );

        $this->assertNotNull($shop);
        $this->assertEquals($payload['name'], $shop->name);
        $this->assertTrue($shop->platform->is($platform));
        $this->assertEquals(State::ACTIVE->value, $shop->state);
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_update_a_shop(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        $platform = Platform::factory()->create();

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = User::factory()->create();
            $uuid = $customer->uuid;
        }

        $shop = Shop::factory()->recycle($platform)->recycle($customer ?? $user)->create();

        $payload = ['name' => $this->faker->company, 'description' => $this->faker->sentence];

        $response = $this->patchJson("api/v1/users/{$uuid}/shops/{$shop->uuid}", $payload);

        $response->assertOk()
            ->assertJsonStructure(
                [
                    'data' => [
                        'uuid',
                        'user_uuid',
                        'platform_uuid',
                        'name',
                        'description',
                        'state',
                    ]
                ]
            );

        if ($role === 'admin') {
            $shop = $customer->shops->first();
        } else {
            $shop = $user->shops->first();
        }

        $this->assertNotNull($shop);
        $this->assertEquals($payload['name'], $shop->name);
        $this->assertEquals($payload['description'], $shop->description);
        $this->assertEquals(State::ACTIVE->value, $shop->state);
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_delete_a_shop(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        $platform = Platform::factory()->create();

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = User::factory()->create();
            $uuid = $customer->uuid;
        }

        $shop = Shop::factory()->recycle($customer ?? $user)->recycle($platform)->create();
        $credential = Credential::factory()->recycle($shop)->create();

        $response = $this->deleteJson("api/v1/users/{$uuid}/shops/{$shop->uuid}");
        $response->assertStatus(204);

        $this->assertSoftDeleted('shops', [
            'uuid' => $shop->uuid,
            'user_id' => $shop->user_id,
            'platform_id' => $shop->platform_id,
            'name' => $shop->name,
        ]);

        $this->assertSoftDeleted('credentials', [
            'uuid' => $credential->uuid,
            'shop_id' => $credential->shop_id,
            'client_id' => $credential->client_id,
            'client_secret' => $credential->client_secret,
        ]);
    }

    public static function provideRoles(): array
    {
        return [
            'admin' => ['admin'],
            'customer' => ['customer'],
        ];
    }
}
