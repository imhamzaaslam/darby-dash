<?php

namespace Tests\Feature\Controllers\Api;

use App\Events\OssRegistrationDateChanged;
use App\Notifications\SendSetPasswordNotification;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class UsersControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
     * @dataProvider roleProvider
     */
    public function it_can_create_a_user_as_an_admin(string $role)
    {
        Notification::fake();
        $user = $this->user($role);
        $this->actingAs($user, 'sanctum');

        if ($role === 'admin') {
            if (!Role::where('name', 'customer')->first()) {
                Role::create(['name' => 'customer', 'guard_name' => 'web']);
            }
        }

        $email = $this->faker->email;

        $payload = [
            'name_first' => $this->faker->firstName,
            'name_last' => $this->faker->lastName,
            'company' => $this->faker->company,
            'coc_number' => "23062639",
            'email' => $email,
            'role' => $this->faker->randomElement(['customer'])
        ];

        Http::fake([
            'https://api.kvk.nl/api/*' => Http::response(['totaal' => '2'], 200, [
                'apikey' => "test_api_key"
            ])
        ]);

        $response = $this->postJson('api/v1/admin/users', $payload);

        if ($user->hasRole('customer')) {
            $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
            Notification::assertNothingSent();
        }

        if ($user->hasRole('admin')) {
            $response->assertStatus(ResponseAlias::HTTP_CREATED)
                ->assertJsonStructure(
                    [
                        'data' => [
                            'id',
                            'uuid',
                            'name_first',
                            'name_last',
                            'email',
                            'info',
                            'roles',
                        ]
                    ]
                );

            $createdUser = User::where('email', $email)->first();

            $this->assertDatabaseHas('user_infos', [
                'user_id' => $createdUser->id,
                'company' => $payload['company'],
                'coc_number' => $payload['coc_number']
            ]);

            Notification::assertSentTo(
                [$createdUser],
                SendSetPasswordNotification::class
            );
        }
    }

    /** @test */
    public function test_it_returns_avatar_if_exists()
    {
        $admin = $this->user('admin');
        $this->actingAs($admin);

        $user = User::factory()->create();
        $filePath = 'tests/stubs/file_uploads/testOne.jpg';
        $uploadedFile = new UploadedFile($filePath, "sample.jpg", 'image/jpeg', null,  true);
        $endpoint = "/api/v1/users/{$user->uuid}/files";
        $uploadResponse = $this->postJson($endpoint, ['file' => $uploadedFile]);
        $uploadResponse->assertStatus(ResponseAlias::HTTP_OK);

        $response = $this->getJson("api/v1/admin/users/{$user->uuid}");
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'avatar'
            ]
        ]);
        $this->assertNotNull($response->json('data.avatar'));
        $avatarPath = str_replace('/storage', '', $response->json('data.avatar.path'));
        $this->assertTrue(Storage::disk('testing')->exists($avatarPath));
        $this->assertEquals('sample.jpg', $response->json('data.avatar.name'));
    }

    /** @test */
    public function it_responds_with_a_bad_request_when_creating_a_user()
    {
        $customer = $this->user();
        $this->actingAs($customer, 'sanctum');

        $payload = [
            'name_first' => null,
            'name_last' => $this->faker->lastName,
            'company' => $this->faker->company,
            'coc_number' => "23062639",
            'email' => null,
            'role' => null,
        ];

        Http::fake([
            'https://api.kvk.nl/api/*' => Http::response(['totaal' => '2'], 200, [
                'apikey' => "test_api_key"
            ])
        ]);
        $this->postJson('api/v1/admin/users', $payload)
            ->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson(
                [
                    'errors' => [
                        'name_first' => [
                            trans('validation.required', ['attribute' => 'name first'])
                        ],
                        'email' => [
                            trans('validation.required', ['attribute' => 'email'])
                        ],
                        'role' => [
                            trans('validation.required', ['attribute' => 'role'])
                        ]
                    ],
                    'message' => 'The name first field is required. (and 2 more errors)'
                ]
            );
    }

    /** @test */
    public function it_cannot_get_a_user_as_a_customer()
    {
        $user = $this->user('customer');
        $this->actingAs($user, 'sanctum');

        $randomUser = User::factory()->create();

        $response = $this->getJson("api/v1/admin/users/$randomUser->uuid");
        $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_can_get_all_paginated_users_as_an_admin()
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum');

        User::factory()->count(30)->create()->each(fn (User $user) => $user->info()->save(UserInfo::factory()->create()));

        $response = $this->getJson('api/v1/admin/users');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');
        $response->assertJsonPath('meta.total', 30);
        $response->assertJsonStructure([
            'data' => [
                '*' => [],
            ],
            'links' => [],
            'meta' => [],
        ]);

        $response = $this->getJson('api/v1/admin/users?page=2');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->getJson('api/v1/admin/users?page=3');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->getJson('api/v1/admin/users?page=4');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    /**  @test */
    public function it_can_get_all_filtered_users_as_an_admin()
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum');

        Role::create(['name' => 'customer', 'guard_name' => 'web']);
        User::factory()->create([
            'name_first' => 'John',
            'name_last' => 'Doe',
            'email' => 'john@gmail.com',
            'state' => 'active'
        ])->assignRole('customer')->info()->save(UserInfo::factory()->create([
            'company' => 'John Doe Company',
            'coc_number' => '123456789',
            'communication_language' => 'en'
        ]));

        User::factory()->create([
            'name_first' => 'Jane',
            'name_last' => 'Doe',
            'email' => 'jane@gmail.com',
            'state' => 'inactive'
        ])->assignRole('customer')->info()->save(UserInfo::factory()->create([
            'company' => 'Jane Doe Company',
            'coc_number' => '987654321',
            'communication_language' => 'nl'
        ]));

        $response = $this->getJson('api/v1/admin/users?keyword=John');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name_first', 'John');

        $response = $this->getJson('api/v1/admin/users?keyword=Company');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(2, 'data');

        $response = $this->getJson('api/v1/admin/users?keyword=123456789');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.info.coc_number', '123456789');

        $response = $this->getJson('api/v1/admin/users?keyword=inactive');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.state', 'inactive');

        $response = $this->getJson('api/v1/admin/users?keyword=customer');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(2, 'data');

        $response = $this->getJson('api/v1/admin/users?orderBy=name_first&order=asc');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.name_first', 'Jane');

        $response = $this->getJson('api/v1/admin/users?orderBy=name_first&order=desc');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.name_first', 'John');

        $response = $this->getJson('api/v1/admin/users?per_page=1');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');

        $response = $this->getJson('api/v1/admin/users?per_page=1&page=2');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name_first', 'Jane');
    }

    /**
     * @test
     * @dataProvider roleProvider
     */
    public function it_can_update_a_user(string $role): void
    {
        Event::fake();
        $auth = $this->user($role);
        $this->actingAs($auth);

        $uuid = $auth->uuid;
        if ($role === 'customer') {
            $auth->info()->save(UserInfo::factory()->create());
        }

        $user = null;
        if ($role === 'admin') {
            $user = $this->user('customer');
            $user->info()->save(UserInfo::factory()->create());
            $uuid = $user->uuid;
        }

        $email = $this->faker->email;

        $payload = [
            'email' => $email,
            'oss_registered_at' => '2023-01-01',
            'communication_language' => 'en'
        ];

        $uri = $role === 'admin' ? "api/v1/admin/users/{$uuid}" : "api/v1/users/{$uuid}";

        $response = $this->patchJson($uri, $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);

        $updated = $role === 'admin' ?  $user->refresh() : $auth->refresh();

        $this->assertEquals($email, $updated->email);
        $this->assertEquals('2023-01-01', $updated->oss_registered_at->format('Y-m-d'));
        $this->assertEquals('en', $updated->info->communication_language);

        Event::assertDispatched(fn (OssRegistrationDateChanged $event) => $event->model->is($updated));
    }

    public static function roleProvider(): array
    {
        return [
            ['admin'],
            ['customer']
        ];
    }
}
