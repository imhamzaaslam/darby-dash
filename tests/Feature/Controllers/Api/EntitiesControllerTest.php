<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntitiesControllerTest extends TestCase
{
    use WithFaker;
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
    */
    public function it_fetches_entities()
    {
        $admin = $this->user();
        $this->actingAs($admin, 'sanctum');
        $entitiesEndpoint = '/api/v1/admin/entities';
        $response = $this->getJson($entitiesEndpoint);

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'entities',
            ],
        ]);;
    }

}
