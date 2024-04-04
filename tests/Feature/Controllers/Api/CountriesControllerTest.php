<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class CountriesControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function it_returns_the_taxable_countries(): void
    {
        $countries = Country::where('is_taxable', 1)->get();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $this->getJson('api/v1/countries')
            ->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonCount($countries->count(), 'data')
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'id',
                            'created_at',
                            'updated_at',
                            'name',
                            'code',
                            'is_taxable',
                        ]
                    ]
                ]
            );
    }
}
