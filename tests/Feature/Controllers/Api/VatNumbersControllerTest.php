<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Country;
use App\Models\User;
use App\Models\VatNumber;
use Danielebarbaro\LaravelVatEuValidator\VatValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery\MockInterface;
use Tests\TestCase;

class VatNumbersControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_get_the_vat_numbers_for_a_user(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        $uuid = null;
        $customer = null;
        if ($role === 'admin') {
            $customer = User::factory()->create();
            $uuid = $customer->uuid;
        }

        VatNumber::factory(10)->create(['user_id' => $customer?->id ?? $user->id]);

        $uri = $role === 'admin' ?
            "api/v1/users/{$uuid}/vat-numbers" :
            "api/v1/users/{$user->uuid}/vat-numbers";

        $response = $this->getJson($uri);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', 10));
        $response->assertOk()
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'uuid',
                            'country',
                            'registered_at',
                            'number',
                            'frequency',
                        ],
                    ]
                ]
            );
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_create_a_vat_number(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        $nl = Country::whereCode('NL');

        $payload = [
            'code' => $nl->code,
            'number' => $number = 'NL003562385B18',
        ];

        $uuid = $user->uuid;
        $customer = null;
        if ($role === 'admin') {
            $customer = User::factory()->create();
            $uuid = $customer->uuid;
        }

        $uri = $role === 'admin' ? "api/v1/users/{$uuid}/vat-numbers" : "api/v1/users/{$uuid}/vat-numbers";

        $this->mock(VatValidator::class, function (MockInterface $mock) use ($number) {
            $mock->shouldReceive('validate')
                ->with($number)
                ->once()
                ->andReturnTrue();

            $mock->shouldReceive('validateExistence')
                ->with($number)
                ->once()
                ->andReturnTrue();
        });

        $response = $this->postJson($uri, $payload);

        $response->assertOk()
            ->assertJsonStructure(
                [
                    'data' => [
                        'uuid',
                        'country',
                        'registered_at',
                        'number',
                        'frequency',
                    ]
                ]
            );

        if ($role === 'admin') {
            $vatNumber = $customer->vatNumbers->first();
        } else {
            $vatNumber = $user->vatNumbers->first();
        }

        $this->assertNotNull($vatNumber);
        $this->assertEquals('NL003562385B18', $vatNumber->number);
        $this->assertEquals($nl->code, $vatNumber->country->code);
    }

    /**
     * @test
     * @dataProvider provideRoles
     */
    public function it_can_update_a_vat_number(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        $uuid = $user->uuid;
        $customer = null;

        if ($role === 'admin') {
            $customer = User::factory()->create();
            $uuid = $customer->uuid;
        }

        $nl = Country::whereCode('NL');

        $vatNumber = VatNumber::factory()->create(['user_id' => $customer?->id ?? $user->id, 'country_id' => $nl->id]);

        $payload = [
            'code' => $nl->code,
            'number' => 'NL003562385B18',
        ];

        $uri = "api/v1/users/{$uuid}/vat-numbers/{$vatNumber->uuid}";

        $response = $this->patchJson($uri, $payload);

        $response->assertOk()
            ->assertJsonStructure(
                [
                    'data' => [
                        'uuid',
                        'country',
                        'registered_at',
                        'number',
                        'frequency',
                    ]
                ]
            );

        if ($role === 'admin') {
            $vatNumber = $customer->vatNumbers->first();
        } else {
            $vatNumber = $user->vatNumbers->first();
        }

        $this->assertNotNull($vatNumber);
        $this->assertCount(1, $customer?->vatNumbers ?? $user->vatNumbers);
        $this->assertEquals('NL003562385B18', $vatNumber->number);
        $this->assertEquals($nl->code, $vatNumber->country->code);
    }

    /**
     * @test
     */
    public function it_cannot_create_a_credential_for_another_user_as_a_customer(): void
    {
        $user = $this->user('customer');
        $this->actingAs($user);

        $customer = User::factory()->create();

        $payload = [
            'code' => 'NL',
            'number' => 'NL003562385B18'
        ];

        $response = $this->postJson("api/v1/users/{$customer->uuid}/vat-numbers", $payload);
        $response->assertStatus(403);
    }

    public static function provideRoles(): array
    {
        return [
            ['admin'],
            ['customer'],
        ];
    }
}
