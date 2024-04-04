<?php

namespace Tests\Feature\Controllers\Api;

use App\Enums\FileType;
use App\Models\Platform;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class FileControllerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;


    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        // Delete all files within a directory
        File::cleanDirectory(storage_path('app/testing/users'));
        File::cleanDirectory(storage_path('app/testing/platforms'));
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider providerUploadEntities
     */
    public function it_can_upload_file_as_admin(array $providerData): void
    {
        $entity = $providerData['entity'];
        $role = $providerData['role'];
        $statusCode = $providerData['statusCode'];
        $filePath = 'tests/stubs/file_uploads/testOne.jpg';
        $loggedInUser = $this->user($role);
        $this->actingAs($loggedInUser, 'sanctum');
        if ($entity === 'users') {
            $model = $loggedInUser;
        } else if ($entity === 'platforms') {
            $model = Platform::factory()->create();
        }
        $uploadedFile = new UploadedFile($filePath, "sample.jpg", 'image/jpeg', null,  true);
        $endpoint = "/api/v1/{$entity}/{$model->uuid}/files";
        $response = $this->postJson($endpoint, ['file' => $uploadedFile]);
        $response->assertStatus($statusCode);

        if ($response->getStatusCode() === 200) {
            $imageFile = $model->files->where('type', FileType::AVATAR->value)->first();
            $this->assertNotNull($imageFile);
            $filePath = str_replace('/storage', '', $imageFile->path);
            $this->assertTrue(Storage::disk('testing')->exists($filePath));
            $this->assertEquals('sample.jpg', $imageFile->name);
            if ($entity === 'platforms') {
                $this->assertCount(1, $model->files()->get());
            }
            if ($entity === 'users') {
                $this->assertCount(1, $model->files()->get());
            }
            $response->assertJsonStructure([
                'data' => [
                    'uuid',
                    'name',
                    'path',
                    'url',
                    'size',
                    'mime_type',
                    'fileable_type',
                    'fileable_id',
                ]
            ])->assertOk();
        }
    }

    /** @test */
    public function test_customer_should_not_be_able_to_upload_file_for_other_user(): void
    {
        $filePath = 'tests/stubs/file_uploads/testOne.jpg';
        $customer = $this->user('customer');
        $this->actingAs($customer, 'sanctum');
        $customerTwo = $this->user('customer');
        $uploadedFile = new UploadedFile($filePath, "sample.jpg", 'image/jpeg', null,  true);
        $endpoint = "/api/v1/users/{$customerTwo->uuid}/files";
        $response = $this->postJson($endpoint, ['file' => $uploadedFile]);
        $response->assertStatus(401);
    }

    /** @test */
    public function it_shows_error_if_invalid_file_type_uploaded(): void
    {
        $filePath = 'tests/stubs/file_uploads/sample-bol-import.csv';
        $customer = $this->user('customer');
        $this->actingAs($customer, 'sanctum');
        $uploadedFile = new UploadedFile($filePath, "sample-bol-import.csv", 'text/csv', null,  true);
        $endpoint = "/api/v1/users/{$customer->uuid}/files";
        $response = $this->postJson($endpoint, ['file' => $uploadedFile]);
        $response->assertStatus(422);
    }

    /**
     * @test
     * @dataProvider providerUploadEntities
     */
    public function it_can_update_a_file_as_an_admin(array $providerData): void
    {
        $entity = $providerData['entity'];
        $role = $providerData['role'];
        $statusCode = $providerData['statusCode'];
        $filePath = 'tests/stubs/file_uploads/testOne.jpg';
        $loggedInUser = $this->user($role);
        $this->actingAs($loggedInUser, 'sanctum');
        if ($entity === 'users') {
            $model = $loggedInUser;
        } else if ($entity === 'platforms') {
            $model = Platform::factory()->create();
        }
        $uploadedFile = new UploadedFile($filePath, "sample.jpg", 'image/jpeg', null,  true);
        $endpoint = "/api/v1/{$entity}/{$model->uuid}/files";
        $response = $this->postJson($endpoint, ['file' => $uploadedFile]);
        $response->assertStatus($statusCode);

        if ($response->getStatusCode() === 200) {
            $imageFile = $model->files->where('uuid', $response['data']['uuid'])->first();
            $this->assertNotNull($imageFile);
            $filePath = str_replace('/storage', '', $imageFile->path);
            $this->assertTrue(Storage::disk('testing')->exists($filePath));
            $this->assertEquals('sample.jpg', $imageFile->name);
            $this->assertCount(1, $model->files()->get());
            $response->assertJsonStructure([
                'data' => [
                    'uuid',
                    'name',
                    'path',
                    'url',
                    'size',
                    'mime_type',
                    'fileable_type',
                    'fileable_id',
                ]
            ])->assertOk();

            $newFilePath = 'tests/stubs/file_uploads/testTwo.jpg';
            $newUploadedFile = new UploadedFile($newFilePath, "sample.jpg", 'image/jpeg', null,  true);
            $updateEndpoint = "/api/v1/{$entity}/{$model->uuid}/files/{$imageFile->uuid}";
            $updateResponse = $this->patchJson($updateEndpoint, ['file' => $newUploadedFile]);
            $updateResponse->assertStatus($statusCode);

            if ($updateResponse->getStatusCode() === 200) {
                $updatedImageFile = $model->files->where('uuid', $updateResponse['data']['uuid'])->first();
                $this->assertNotNull($updatedImageFile);
                $filePath = str_replace('/storage', '', $updatedImageFile->path);
                $this->assertTrue(Storage::disk('testing')->exists($filePath));
                $this->assertEquals('sample.jpg', $updatedImageFile->name);
                $this->assertCount(1, $model->files()->get());
                $response->assertJsonStructure([
                    'data' => [
                        'uuid',
                        'name',
                        'path',
                        'url',
                        'size',
                        'mime_type',
                        'fileable_type',
                        'fileable_id',
                    ]
                ])->assertOk();
            }
        }
    }

    /** @test */
    public function it_cannot_update_a_file_with_an_invalid_file_type(): void
    {
        $filePath = 'tests/stubs/file_uploads/testOne.jpg';
        $customer = $this->user('customer');
        $this->actingAs($customer, 'sanctum');
        $uploadedFile = new UploadedFile($filePath, "sample.jpg", 'image/jpeg', null,  true);
        $endpoint = "/api/v1/users/{$customer->uuid}/files";
        $response = $this->postJson($endpoint, ['file' => $uploadedFile]);
        $response->assertStatus(200);

        $imageFile = $customer->files->where('uuid', $response['data']['uuid'])->first();
        $this->assertNotNull($imageFile);
        $filePath = str_replace('/storage', '', $imageFile->path);
        $this->assertTrue(Storage::disk('testing')->exists($filePath));
        $this->assertEquals('sample.jpg', $imageFile->name);
        $this->assertCount(1, $customer->files()->get());

        $newFilePath = 'tests/stubs/file_uploads/sample-bol-import.csv';
        $newUploadedFile = new UploadedFile($newFilePath, "sample-bol-import.csv", 'text/csv', null,  true);
        $updateEndpoint = "/api/v1/users/{$customer->uuid}/files/{$imageFile->uuid}";
        $updateResponse = $this->patchJson($updateEndpoint, ['file' => $newUploadedFile]);
        $updateResponse->assertStatus(422);
    }

    /**
     * This is a data provider function
     *
     * @return array
     **/
    public static function providerUploadEntities(): array
    {
        return [
            [
                [
                    'entity' => 'users',
                    'role' => 'admin',
                    'statusCode' => 200
                ]
            ],
            [
                [
                    'entity' => 'users',
                    'role' => 'customer',
                    'statusCode' => 200
                ]
            ],
            [
                [
                    'entity' => 'platforms',
                    'role' => 'admin',
                    'statusCode' => 200
                ]
            ],
            [
                [
                    'entity' => 'platforms',
                    'role' => 'customer',
                    'statusCode' => 401
                ]
            ],
        ];
    }
}
