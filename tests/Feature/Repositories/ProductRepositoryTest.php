<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Events\ProductCategoryChanged;
use App\Listeners\UpdateJournal;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\QueryException;
use InvalidArgumentException;

class ProductRepositoryTest  extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    protected ProductRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ProductRepositoryInterface::class);
    }

    /**
     * @test
     * @dataProvider provideProductData
     */
    public function it_can_create_a_product(?string $ean, ?string $asin, string $title, string $sku ,float $price): void
    {
        $shop = Shop::factory()->create();

        if (is_null($ean) && is_null($asin)) {
            $this->expectException(InvalidArgumentException::class);
        }

        $product = $this->repository->create(
            $shop->platform,
            $ean,
            $asin,
            $shop,
            $sku,
            $title,
            $price
        );

        $this->assertNotNull($product);
        $this->assertEquals($ean, $product->ean);
        $this->assertEquals($title, $product->title);
        $this->assertEquals(1, $product->users->count());
        $this->assertEquals(1, $product->shops->count());

        $this->assertEquals($shop->user->id, $product->users->first()->pivot->user_id);
        $this->assertEquals($shop->id, $product->shops->first()->pivot->shop_id);

        $this->assertEquals($product->id, $product->users->first()->pivot->product_id);
        $this->assertEquals($product->id, $product->shops->first()->pivot->product_id);

        $this->assertEquals($sku, $product->users->first()->pivot->sku);
        $this->assertEquals($price, $product->users->first()->pivot->purchase_price);

        $this->assertEquals($sku, $product->shops->first()->pivot->sku);
        $this->assertEquals($price, $product->shops->first()->pivot->purchase_price);
    }


    /**
     * @test
     * @dataProvider provideProductData
     */
    public function it_can_update_a_product(?string $ean, ?string $asin, string $title, string $sku ,float $price): void
    {
        Event::fake();
        $category = Category::factory()->create();
        $shop = Shop::factory()->create();

        if (is_null($ean) && is_null($asin)) {
            $this->expectException(InvalidArgumentException::class);
        }

        $product = $this->repository->create(
            $shop->platform,
            $ean,
            $asin,
            $shop,
            $sku,
            $title,
            $price
        );

        $productUpdated = $this->repository->update($product,[
            'category_id' => $category->id
        ]);

        Event::assertDispatched(ProductCategoryChanged::class, function ($event) use ($product)  {
            return $event->model->id === $product->id;
        });

        Event::assertListening(ProductCategoryChanged::class, UpdateJournal::class);
        $this->assertNotNull($product);
        $this->assertTrue($productUpdated);
        $this->assertEquals($product->category_id, $category->id);
    }


    /**
     * @test
     * @dataProvider provideProductData
     */
    public function it_cannot_not_update_the_product_with_an_invalid_category(?string $ean, ?string $asin, string $title, string $sku ,float $price): void
    {
        Event::fake();
        $shop = Shop::factory()->create();

        if (is_null($ean) && is_null($asin)) {
            $this->expectException(InvalidArgumentException::class);
        }

        $product = $this->repository->create(
            $shop->platform,
            $ean,
            $asin,
            $shop,
            $sku,
            $title,
            $price
        );

        $this->expectException(QueryException::class);

        $productUpdated = $this->repository->update($product, [
            'category_id' => 'test'
        ]);

        Event::assertNotDispatched(ProductCategoryChanged::class);
        $this->assertNotNull($product);
        $this->assertFalse($productUpdated);
    }

    /**
     * @test
     * @dataProvider provideProductData
     */
    public function it_can_delete_a_product(?string $ean, ?string $asin, string $title, string $sku ,float $price): void
    {
        $shop = Shop::factory()->create();

        if (is_null($ean) && is_null($asin)) {
            $this->expectException(InvalidArgumentException::class);
        }

        $product = $this->repository->create(
            $shop->platform,
            $ean,
            $asin,
            $shop,
            $sku,
            $title,
            $price
        );

        $isDeleted = $product->delete();
        $this->assertNull($this->repository->showForUser($shop->user, $product->uuid));
        $this->assertTrue($isDeleted);
        $this->assertSoftDeleted('products', [
            'uuid' => $product->uuid,
            'platform_id' => $product->platform_id,
            'category_id' => $product->category_id,
            'ean' => $product->ean,
            'asin' => $product->asin,
            'title' => $product->title,
        ]);
    }

    public static function provideProductData(): array
    {
        return [
            [
                '1234567890',
                null,
                'test product 1',
                '111222333',
                14.99,
            ],
            [
                null,
                '9876543210',
                'test product 2',
                '111222444',
                44.99,
            ],
            [
                '1234567890',
                '9876543210',
                'test product 3',
                '1112225555',
                85.99,
            ],
            [
                null,
                null,
                'test product 3',
                '111222666',
                85.99,
            ],
        ];
    }
}
