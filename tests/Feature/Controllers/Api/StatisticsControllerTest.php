<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatisticsControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * @test
    */
    public function it_shows_success_on_sales_statistics_endpoint()
    {
        $customer = $this->user('customer');
        $this->actingAs($customer, 'sanctum');
        $endpoint = "/api/v1/users/{$customer->uuid}/sales/statistics";
        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
    }

    /**
     * @test
    */
    public function it_shows_success_on_vat_statistics_endpoint()
    {
        $customer = $this->user('customer');
        $this->actingAs($customer, 'sanctum');
        $endpoint = "/api/v1/users/{$customer->uuid}/sales/vat-statistics";
        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
    }

}

