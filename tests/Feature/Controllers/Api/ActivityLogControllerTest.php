<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\ActivityLog;
use Tests\TestCase;

class ActivityLogControllerTest extends TestCase
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
    public function it_fetches_all_logs_if_params_are_not_given(): void
    {
        $admin = $this->user();
        $this->actingAs($admin);

        $product = Product::factory()->create();

        activity('info')->causedBy($admin)->performedOn($product)->log("Product has been created.");

        $endpoint = '/api/v1/admin/logs';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'log_name',
                    'description',
                    'subject_type',
                    'subject_id',
                    'causer_type',
                    'causer_id',
                    'batch_uuid',
                    'created_at',
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_fetches_logs_by_entity()
    {
        $admin = $this->user();
        $this->actingAs($admin, 'sanctum');
        Product::factory(5)->create(); //system should create 5 logs for Product entity.
        User::factory(2)->create(); //system should create 2 logs for User entity.
        $endpoint = '/api/v1/admin/logs?keyword=Product';
        $response = $this->getJson($endpoint);
        $response->assertStatus(200)->assertJsonCount(5, 'data')
                ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'log_name',
                        'description',
                        'subject_type',
                        'subject_id',
                        'causer_type',
                        'causer_id',
                        'batch_uuid',
                        'created_at',
                    ],
                ],
            ]);
    }

    /**
     * @test
     */
    public function logs_should_not_be_accessible_by_customer()
    {
        $customer = $this->user('customer');
        $this->actingAs($customer);
        activity('info')->log("user created");
        $endpoint = '/api/v1/admin/logs';
        $response = $this->getJson($endpoint);
        $response->assertStatus(403);
    }

    /**
     * @test
    */
    public function it_fetches_logs_by_current_date()
    {
        $admin = $this->user();
        $this->actingAs($admin, 'sanctum');
        Product::factory(2)->create();
        $endpoint = '/api/v1/admin/logs?date='.now()->format("Y-m-d");
        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data')->assertJsonStructure([
            'data' => [
                '*' => [
                    'log_name',
                    'description',
                    'subject_type',
                    'subject_id',
                    'causer_type',
                    'causer_id',
                    'batch_uuid',
                    'created_at',
                ],
            ],
        ]);
    }

    /**
     * @test
     * @dataProvider logsProvider
    */
    public function it_fetches_logs_by_previous_date($logsData)
    {
        $admin = $this->user();
        $this->actingAs($admin, 'sanctum');
        foreach ($logsData as $logData) {
            $activityLog = new ActivityLog;
            $activityLog->subject_type = $logData['subject_type'];
            $activityLog->log_name = $logData['log_name'];
            $activityLog->created_at = $logData['created_at'];
            $activityLog->save();
        }
        $endpoint = '/api/v1/admin/logs?keyword=2023-06-06';
        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data')->assertJsonStructure([
            'data' => [
                '*' => [
                    'log_name',
                    'description',
                    'subject_type',
                    'subject_id',
                    'causer_type',
                    'causer_id',
                    'batch_uuid',
                    'created_at',
                ],
            ],
        ]);
    }

    public static function logsProvider(): array
    {
        $logsData = [
                [
                'subject_type' => 'App\Models\Product',
                'log_name' => 'default',
                'created_at' => '2023-06-05',
                ],
                [
                    'subject_type' => 'App\Models\Product',
                    'log_name' => 'default',
                    'created_at' => '2023-06-06',
                ],
                [
                    'subject_type' => 'App\Models\User',
                    'log_name' => 'error',
                    'created_at' => '2023-06-06',
            ]
        ];

        return [
            [
                $logsData
            ]
        ];
    }

}
