<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Credential;
use App\Models\Shop;
use App\Models\User;
use App\Models\Platform;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class PlatformsControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    /**
     * @test
     */
    public function it_return_unauthorized_when_trying_to_get_platforms_without_logging_in()
    {
        $this->getJson('api/v1/platforms')
            ->assertStatus(ResponseAlias::HTTP_UNAUTHORIZED)
            ->assertJson([
                'error' => 'unauthenticated',
                'message' => 'Unauthenticated.'
            ]);
    }

    /**
     * @test
     */
    public function it_can_get_all_paginated_platforms_for_admin()
    {
        $admin = $this->user('admin');
        $this->actingAs($admin, 'sanctum');

        Credential::factory(30)->create();

        $response = $this->getJson('api/v1/platforms');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');
        $response->assertJsonPath('meta.total', 30);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'uuid',
                    'created_at',
                    'updated_at',
                    'name',
                    'state',
                    'stripped_name'
                ]
            ],
            'links' => [],
            'meta' => [],
        ]);

        $response = $this->getJson('api/v1/platforms?page=2');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->getJson('api/v1/platforms?page=4');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    /**
     * @test
     */
    public function it_can_return_the_filtered_platforms_for_admin()
    {
        $this->markTestIncomplete('Needs refactoring.');
        $admin = $this->user();
        $this->actingAs($admin);

        $platform1 = Platform::factory()->create([
            'name' => 'platform1',
            'state' => 'active'
        ]);
        $platform2 = Platform::factory()->create([
            'name' => 'platform2',
            'state' => 'active'
        ]);
        $platform3 = Platform::factory()->create([
            'name' => 'platform3',
            'state' => 'inactive'
        ]);

        Credential::factory()->create([
            'platform_id' => $platform1->id
        ]);

        Credential::factory()->create([
            'platform_id' => $platform2->id
        ]);

        Credential::factory()->create([
            'platform_id' => $platform3->id
        ]);

        $response = $this->getJson('api/v1/platforms?keyword=platform1');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'platform1']);

        $response = $this->getJson('api/v1/platforms?keyword=platform');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(3, 'data');

        $response = $this->getJson('api/v1/platforms?keyword=platform&per_page=2&page=2');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'platform3']);

        $response = $this->getJson('api/v1/platforms?keyword=active');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(3, 'data');

        $response = $this->getJson('api/v1/platforms?keyword=inactive');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function it_can_get_all_paginated_platforms_for_customer()
    {
        $user = $this->user('customer');
        $this->actingAs($user, 'sanctum');

        Credential::factory(30)->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJson('api/v1/platforms');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');
        $response->assertJsonPath('meta.total', 30);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'uuid',
                    'created_at',
                    'updated_at',
                    'name',
                    'state',
                    'stripped_name'
                ]
            ],
            'links' => [],
            'meta' => [],
        ]);

        $response = $this->getJson('api/v1/platforms?page=2');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->getJson('api/v1/platforms?page=4');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

        /**
     * @test
     */
    public function it_return_filtered_platforms_for_customer()
    {
        $this->markTestIncomplete('Needs refactoring.');

        $user = $this->user('customer');
        $this->actingAs($user, 'sanctum');

        $platform1 = Platform::factory()->create([
            'name' => 'platform1',
            'state' => 'active'
        ]);
        $platform2 = Platform::factory()->create([
            'name' => 'platform2',
            'state' => 'active'
        ]);
        $platform3 = Platform::factory()->create([
            'name' => 'platform3',
            'state' => 'inactive'
        ]);

        Credential::factory()->create([
            'user_id' => $user->id,
            'platform_id' => $platform1->id
        ]);

        Credential::factory()->create([
            'user_id' => $user->id,
            'platform_id' => $platform2->id
        ]);

        Credential::factory()->create([
            'user_id' => $user->id,
            'platform_id' => $platform3->id
        ]);

        $response = $this->getJson('api/v1/platforms?keyword=platform1');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'platform1']);

        $response = $this->getJson('api/v1/platforms?keyword=platform');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(3, 'data');

        $response = $this->getJson('api/v1/platforms?keyword=platform&per_page=2&page=2');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'platform3']);

        $response = $this->getJson('api/v1/platforms?keyword=active');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(3, 'data');

        $response = $this->getJson('api/v1/platforms?keyword=inactive');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function it_cannot_create_a_platform_as_a_customer()
    {
        $user = $this->user('customer');
        $this->actingAs($user, 'sanctum');

        $payload = [
            'name' => $this->faker->company
        ];

        $this->postJson('api/v1/platforms', $payload)
            ->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function it_can_create_a_platform()
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum');

        $payload = [
            'name' => $this->faker->company
        ];

        $this->postJson('api/v1/platforms', $payload)
            ->assertStatus(ResponseAlias::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'uuid',
                        'created_at',
                        'updated_at',
                        'name',
                        'state',
                        'stripped_name'
                    ]
                ]);

        $this->assertDatabaseHas('platforms', $payload);
    }

    /**
     * @test
     */
    public function test_that_admin_is_able_to_retrieve_a_list_of_platforms_of_a_customer()
    {
        $this->markTestIncomplete('Needs refactoring.');

        $admin = $this->user();

        $customer = $this->user('customer');

        $shops = Shop::factory()->count(3)->recycle($customer)->create();

        Credential::factory(3)->recycle($shops)->create();

        $response = $this->actingAs($admin)->getJson("api/v1/users/{$customer->uuid}/platforms");
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonPath('meta.total', 3);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'uuid',
                    'created_at',
                    'updated_at',
                    'name',
                    'state',
                    'stripped_name'
                ]
            ],
            'links' => [],
            'meta' => [],
        ]);
    }
}
