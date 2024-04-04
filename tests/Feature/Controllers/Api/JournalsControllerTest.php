<?php

namespace Tests\Feature\Controllers\Api;

use App\Enums\JournalStatus;
use App\Enums\JournalType;
use App\Models\Journal;
use App\Models\Platform;
use App\Models\Invoice;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class JournalsControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_return_journals_count_by_status(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        Journal::factory(10)->create([
            'status' => JournalStatus::APPROVED->value,
        ]);
        Journal::factory(10)->create([
            'status' => JournalStatus::BOOKED->value,
        ]);

        $response = $this->getJson('api/v1/admin/journals/count?statuses=' . JournalStatus::APPROVED->value);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertExactJson([10]);

        $response = $this->getJson('api/v1/admin/journals/count?statuses=' . JournalStatus::BOOKED->value . ',' . JournalStatus::APPROVED->value . ',' . JournalStatus::PENDING->value . ',' . JournalStatus::DISAPPROVED->value . ',' . JournalStatus::FAILED->value . ',' . JournalStatus::MANUAL->value);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where(JournalStatus::APPROVED->value, 10)
                ->where(JournalStatus::BOOKED->value, 10)
                ->where(JournalStatus::PENDING->value, 0)
                ->where(JournalStatus::DISAPPROVED->value, 0)
                ->where(JournalStatus::FAILED->value, 0)
                ->where(JournalStatus::MANUAL->value, 0)
        );
    }

    /** @test */
    public function it_can_show_the_paginated_journals(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        $journals = Journal::factory(20)->create([
            'status' => JournalStatus::APPROVED->value,
        ]);
        $journals = Journal::factory(10)->create([
            'status' => JournalStatus::BOOKED->value,
        ]);

        $response = $this->getJson('api/v1/admin/journals');
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(["message" => "The status field is required."]);

        $response = $this->getJson('api/v1/admin/journals?status=created');
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(["message" => "The selected status is invalid."]);

        $response = $this->getJson('api/v1/admin/journals?status=' . JournalStatus::APPROVED->value);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');
        $response->assertJsonPath('meta.total', 20);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'uuid',
                    'platform',
                    'invoice',
                    'updated_at',
                    'period',
                    'status',
                    'entries',
                    'difference',
                ],
            ],
            'links' => [],
            'meta' => [],
        ]);

        $response = $this->getJson('api/v1/admin/journals?status=' . JournalStatus::APPROVED->value . '&page=2');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->getJson('api/v1/admin/journals?status=' . JournalStatus::APPROVED->value . '&page=3');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson('api/v1/admin/journals?status=' . JournalStatus::BOOKED->value);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');
        $response->assertJsonPath('meta.total', 10);
    }

    /** @test */
    public function it_can_show_the_paginated_journals_of_user_for_admin(): void
    {
        $auth = $this->user();
        $this->actingAs($auth);

        $user = $this->user('customer');
        $user->info()->create(['company' => 'Company1']);

        $shop = Shop::factory()->recycle($user)->create();

        Journal::factory(30)->recycle($shop)->create(['status' => JournalStatus::APPROVED->value]);

        $response = $this->getJson('api/v1/admin/journals/' . $user->uuid);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');
        $response->assertJsonPath('meta.total', 30);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'uuid',
                    'platform',
                    'invoice',
                    'updated_at',
                    'period',
                    'status',
                    'entries',
                    'difference',
                ],
            ],
            'links' => [],
            'meta' => [],
        ]);

        $response = $this->getJson('api/v1/admin/journals/' . $user->uuid . '?page=2');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(config('pagination.per_page'), 'data');

        $response = $this->getJson('api/v1/admin/journals/' . $user->uuid . '?page=4');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    /**
     * @test
     * @dataProvider provideFilteringData
     */
    public function it_can_show_filtered_journals($filters, $expectedCount, $expectedPaths): void
    {
        $auth = $this->user();
        $this->actingAs($auth);

        $user = User::factory()->create();
        UserInfo::factory()->recycle($user)->create(['company' => 'Company1']);
        $platform1 = Platform::factory()->create(['name' => 'Platform1']);

        $shop = Shop::factory()->recycle($user, $platform1)->create();

        $invoice1 = Invoice::factory()->recycle($shop)->create(['uid' => '123456789', 'issued_at' => '2023-02-01']);

        $journal1 = Journal::factory()->recycle($shop, $invoice1)->create(['status' => JournalStatus::APPROVED->value]);

        $user2 = User::factory()->create();
        UserInfo::factory()->recycle($user2)->create(['company' => 'Company2']);
        $platform2 = Platform::factory()->create(['name' => 'Platform2']);

        $shop2 = Shop::factory()->recycle($user2, $platform2)->create();

        $invoice2 = Invoice::factory()->recycle($shop2)->create(['uid' => '987654321', 'issued_at' => '2023-03-01']);

        $journal2 = Journal::factory()->recycle($shop2, $invoice2)->create(['status' => JournalStatus::APPROVED->value]);

        $response = $this->getJson('api/v1/admin/journals?' . http_build_query($filters));
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount($expectedCount, 'data');

        foreach ($expectedPaths as $path => $expectedValue) {
            $response->assertJsonPath($path, $expectedValue);
        }
    }

    public static function provideFilteringData(): array
    {
        return [
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => 'Company',
                ],
                'expectedCount' => 2,
                'expectedPaths' => [
                    'data.0.shop.user.info.company' => 'Company1',
                    'data.1.shop.user.info.company' => 'Company2',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => 'Company1',
                ],
                'expectedCount' => 1,
                'expectedPaths' => [
                    'data.0.user.info.company' => 'Company1',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => 'Platform2',
                ],
                'expectedCount' => 1,
                'expectedPaths' => [
                    'data.0.platform' => 'Platform2',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => '987654321',
                ],
                'expectedCount' => 1,
                'expectedPaths' => [
                    'data.0.invoice.uid' => '987654321',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => JournalType::MAIN->value,
                ],
                'expectedCount' => 2,
                'expectedPaths' => [
                    'data.0.type' => JournalType::MAIN->value,
                    'data.1.type' => JournalType::MAIN->value,
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => '01/01/2023 - 31/01/2023',
                ],
                'expectedCount' => 1,
                'expectedPaths' => [
                    'data.0.period' => '01/01/2023 - 31/01/2023',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => '01/02/2023 - 28/02/2023',
                ],
                'expectedCount' => 1,
                'expectedPaths' => [
                    'data.0.period' => '01/02/2023 - 28/02/2023',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => '01/04/2023 - 30/04/2023',
                ],
                'expectedCount' => 0,
                'expectedPaths' => [],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => 'Company',
                    'orderBy' => 'company',
                    'order' => 'asc',
                ],
                'expectedCount' => 2,
                'expectedPaths' => [
                    'data.0.shop.user.info.company' => 'Company1',
                    'data.1.shop.user.info.company' => 'Company2',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => 'Company',
                    'orderBy' => 'company',
                    'order' => 'desc',
                ],
                'expectedCount' => 2,
                'expectedPaths' => [
                    'data.0.shop.user.info.company' => 'Company2',
                    'data.1.shop.user.info.company' => 'Company1',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => 'Platform',
                    'orderBy' => 'platform',
                    'order' => 'asc',
                ],
                'expectedCount' => 2,
                'expectedPaths' => [
                    'data.0.platform' => 'Platform1',
                    'data.1.platform' => 'Platform2',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'keyword' => 'Platform',
                    'orderBy' => 'platform',
                    'order' => 'desc',
                ],
                'expectedCount' => 2,
                'expectedPaths' => [
                    'data.0.platform' => 'Platform2',
                    'data.1.platform' => 'Platform1',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'orderBy' => 'invoice',
                    'order' => 'asc',
                ],
                'expectedCount' => 2,
                'expectedPaths' => [
                    'data.0.invoice.uid' => '123456789',
                    'data.1.invoice.uid' => '987654321',
                ],
            ],
            [
                'filters' => [
                    'status' => JournalStatus::APPROVED->value,
                    'orderBy' => 'invoice',
                    'order' => 'desc',
                ],
                'expectedCount' => 2,
                'expectedPaths' => [
                    'data.0.invoice.uid' => '987654321',
                    'data.1.invoice.uid' => '123456789',
                ],
            ],
        ];
    }
}
