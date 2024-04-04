<?php

namespace Tests\Feature\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Platform;
use App\Models\Product;
use App\Models\ProductUser;
use Illuminate\Http\UploadedFile;
use App\Http\Resources\ProductResource;
use Illuminate\Testing\Fluent\AssertableJson;
use Config;

class ProductsControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Config::set('pagination.per_page', 10);
    }

    /**
     * @test
    */
    public function it_shows_success_when_csv_file_uploaded_in_queue()
    {
        $filePath = 'tests/stubs/products/sample-bol-import.csv';

        Platform::factory()->create();
        $customer = $this->user('customer');
        $this->actingAs($customer, 'sanctum');

        $uploadedFile = new UploadedFile($filePath, "sample-csv",  'text/csv',null,  true);
        $endpoint = '/api/v1/users/' . $customer->uuid . '/products/import-products-csv';

        $response = $this->call('POST', $endpoint, [], [], ['file' => $uploadedFile], ['Accept' => 'application/json']);

        $response->assertStatus(200);
        $response->assertExactJson([
            'success' => true,
            'message' => "CSV added to queue for import. We will email you when your products imported.",
        ]);

        $this->assertCount(4, $customer->products()->get());
    }

    /**
     * @test
    */
    public function it_returns_paginated_products_for_admin()
    {
        $customer = $this->user();
        $this->actingAs($customer, 'sanctum');

        $products = Product::factory()->count(30)->create();

        $response = $this->get('/api/v1/products');
        $response->assertStatus(200);
        $response->assertJsonCount(config('pagination.per_page'), 'data');
        $response->assertJsonPath('meta.total', 30);
        $response->assertJsonStructure([
            'data' => [
                '*' => [],
            ],
            'links' => [],
            'meta' => [],
        ]);

        $response = $this->get('/api/v1/products?page=2');
        $response->assertStatus(200);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->get('/api/v1/products?page=3');
        $response->assertStatus(200);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->get('/api/v1/products?page=4');
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    /**
     * @test
    */
    public function it_returns_paginated_products_of_a_user_for_admin()
    {
        $customer = $this->user('customer');
        $admin = $this->user();
        $this->actingAs($admin, 'sanctum');

        $products = Product::factory()->count(30)->create();
        $products->each(fn (Product $product) => $product->users()->attach($customer));

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products');
        $response->assertStatus(200);
        $this->assertEquals(config('pagination.per_page'), config('pagination.per_page'));
        $response->assertJsonCount(config('pagination.per_page'), 'data');
        $response->assertJsonPath('meta.total', 30);
        $response->assertJsonStructure([
            'data' => [
                '*' => [],
            ],
            'links' => [],
            'meta' => [],
        ]);

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products?page=2');
        $response->assertStatus(200);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products?page=3');
        $response->assertStatus(200);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products?page=4');
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    /**
     * @test
    */
    public function it_returns_filtered_products_for_admin()
    {
        $admin = $this->user();
        $this->actingAs($admin, 'sanctum');

        Product::factory()->create(['title' => 'Product 1']);
        Product::factory()->create(['title' => 'Product 2']);
        Product::factory()->create(['title' => 'Product 3']);

        $response = $this->get('/api/v1/products?keyword=Product%201');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['title' => 'Product 1']);

        $response = $this->get('/api/v1/products?keyword=Product&orderBy=title&order=desc');
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonFragment(['title' => 'Product 3']);

        $response = $this->get('/api/v1/products?keyword=Product&orderBy=title&order=asc');
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonFragment(['title' => 'Product 1']);

        $response = $this->get('/api/v1/products?keyword=Product&per_page=2');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['title' => 'Product 1']);

        $response = $this->get('/api/v1/products?keyword=Product&per_page=2&page=2');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['title' => 'Product 3']);
    }

    /**
     * @test
    */
    public function it_returns_filtered_products_of_a_user_for_admin()
    {
        $customer = $this->user('customer');
        $admin = $this->user();
        $this->actingAs($admin, 'sanctum');

        Product::factory()->create(['title' => 'Product 1'])->users()->attach($customer);
        Product::factory()->create(['title' => 'Product 2'])->users()->attach($customer);
        Product::factory()->create(['title' => 'Product 3'])->users()->attach($customer);

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products?keyword=Product%201');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['title' => 'Product 1']);

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products?keyword=Product&orderBy=title&order=desc');
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonFragment(['title' => 'Product 3']);

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products?keyword=Product&orderBy=title&order=asc');
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonFragment(['title' => 'Product 1']);

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products?keyword=Product&per_page=2');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['title' => 'Product 1']);

        $response = $this->get('/api/v1/users/' . $customer->uuid . '/products?keyword=Product&per_page=2&page=2');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['title' => 'Product 3']);
    }
}
