<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    /**
     * @test
     */
    public function it_gets_the_user()
    {
        $ossRegisteredAt = now();
        $payload = ['state' => 'active', 'yuki_access_key' => fake()->uuid, 'oss_registered_at' => $ossRegisteredAt];
        $user = $this->user('customer', $payload);

        $this->actingAs($user, 'sanctum');

        $this->getJson('api/v1/me')
            ->assertStatus(ResponseAlias::HTTP_OK)
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $user->id,
                        'uuid' => $user->uuid,
                        'created_at' => $user->created_at->format('d-m-Y'),
                        'updated_at' => $user->updated_at->format('d-m-Y'),
                        'email_verified_at' => $user->email_verified_at->format('d-m-Y'),
                        'name_first' => $user->name_first,
                        'name_last' => $user->name_last,
                        'email' => $user->email,
                        'info' => null,
                        'roles' => $user->getRoleNames(),
                        'meta' => [],
                        'files' => [],
                        'oss_registered_at' => $ossRegisteredAt->toIso8601String(),
                        'state' => $payload['state'],
                        'bookkeeping_started_at' => now()->format('d-m-Y'),
                        'yuki_access_key' => $payload['yuki_access_key'],
                        'avatar' => null,
                    ]
                ]
            );
    }
}
