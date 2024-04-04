<?php

namespace Helpers;

use App\Enums\VatRate;
use App\Exceptions\InvalidVatRateException;
use App\Facades\VatFacade as Vat;
use Tests\TestCase;

class VatTest extends TestCase
{
    /**
     * @test
     * @dataProvider excludingVatProvider
     */
    public function it_can_calculate_the_price_excluding_vat(int $priceIncludingVat, VatRate $rate, string $country, int $expected)
    {
        $this->assertEquals($expected, round(Vat::priceExcludingVat($priceIncludingVat, $rate, $country)));
    }

    public static function excludingVatProvider(): array
    {
        return [
            [10000, VatRate::STANDARD, 'NL', 8264],
            [7260, VatRate::REDUCED, 'NL', 6661],
            [130000, VatRate::STANDARD, 'BE', 107438],
            [20000, VatRate::REDUCED, 'BE', 18868],
        ];
    }

    /**
     * @test
     * @dataProvider includingVatProvider
     */
    public function it_can_calculate_the_price_including_vat(int $priceExcludingVat, VatRate $rate, string $country, int $expected)
    {
        $this->assertEquals($expected, round(Vat::priceIncludingVat($priceExcludingVat, $rate, $country)));
    }

    public static function includingVatProvider(): array
    {
        return [
            [10000, VatRate::STANDARD, 'NL', 12100],
            [7260, VatRate::REDUCED, 'NL', 7913],
            [130000, VatRate::STANDARD, 'BE', 157300],
            [20000, VatRate::REDUCED, 'BE', 21200],
        ];
    }

    /**
     * @test
     * @dataProvider vatProvider
     */
    public function it_can_calculate_the_vat(int $priceExcludingVat, VatRate $rate, string $country, int $expected)
    {
        $this->assertEquals($expected, round(Vat::vatAmount($priceExcludingVat, $rate, $country)));
    }

    public static function vatProvider(): array
    {
        return [
            [10000, VatRate::STANDARD, 'NL', 2100],
            [7260, VatRate::REDUCED, 'NL', 653],
            [130000, VatRate::STANDARD, 'BE', 27300],
            [20000, VatRate::REDUCED, 'BE', 1200],
        ];
    }

    /**
     * @test
     * @dataProvider vatFromTotalAmountProvider
     */
    public function it_can_calculate_the_vat_from_the_total_amount(int $priceIncludingVat, VatRate $rate, string $country, int $expected)
    {
        $this->assertEquals($expected, round(Vat::vatFromTotalAmount($priceIncludingVat, $rate, $country)));
    }

    public static function vatFromTotalAmountProvider(): array
    {
        return [
            [12100, VatRate::STANDARD, 'NL', 2100],
            [7260, VatRate::REDUCED, 'NL', 599],
            [130000, VatRate::STANDARD, 'BE', 22562],
            [20000, VatRate::REDUCED, 'BE', 1132],
        ];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_cannot_calculate_if_vat_rate_is_unavailable(int $price, VatRate $rate, string $country)
    {
        $this->expectException(InvalidVatRateException::class);

        Vat::priceIncludingVat($price, $rate, $country);
    }

    public static function invalidProvider(): array
    {
        return [
            [12100, VatRate::REDUCED_ALT, 'NL'],
            [130000, VatRate::REDUCED_ALT, 'BE'],
        ];
    }
}
