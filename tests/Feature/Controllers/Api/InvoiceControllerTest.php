<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @dataProvider provideRoles
     * @param string $role
     * @return void
     */
    public function it_can_get_all_invoices(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        Invoice::factory()->count(100)->create();

        $response = $this->getJson("api/v1/invoices");

        if ($role === 'customer') {
            $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
            return;
        }

        $response->assertOk()
            ->assertJsonCount(config('pagination.per_page'), 'data')
            ->assertJsonPath('meta.total', 100)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'uuid',
                        'user_id',
                        'platform_id',
                        'type',
                        'uid',
                        'year',
                        'month',
                        'amount',
                        'vat',
                        'total_amount',
                        'payload',
                        'issued_at',
                    ],
                ],
                'links' => [],
                'meta' => [],
            ]);
    }

    /**
     * @test
     * @dataProvider provideRoles
     * @param string $role
     * @return void
     */
    public function it_can_get_an_invoice(string $role): void
    {
        $user = $this->user($role);
        $this->actingAs($user);

        $invoice = Invoice::factory()->create();

        $response = $this->getJson("api/v1/invoices/$invoice->uuid");

        if ($role === 'customer') {
            $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
            return;
        }

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'uuid',
                    'user_id',
                    'platform_id',
                    'type',
                    'uid',
                    'year',
                    'month',
                    'amount',
                    'vat',
                    'total_amount',
                    'payload',
                    'issued_at',
                ],
            ]);
    }

    public static function provideRoles(): array
    {
        return [
            ['admin'], ['customer'],
        ];
    }
}
