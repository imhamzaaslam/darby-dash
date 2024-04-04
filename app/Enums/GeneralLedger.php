<?php

namespace App\Enums;

use App\Traits\EnhancesEnum;

enum GeneralLedger: string
{
    use EnhancesEnum;

    case ACCRUED_CHARGES = '16980';
    case ACCRUED_INCOME = '13690';
    case ADVERTISING_COSTS = '44100';
    case COMPENSATIONS = '80050';
    case OTHER_SALES_COSTS = '44990';
    case OUTSOURCED_WORK = '60300';
    case PACKAGING_COSTS = '45000';
    case PRE_TAX = '18800';
    case ROUNDING_DIFFERENCES = '45900';
    case SUSPENSE_ACCOUNT_BOL_COM = '23105';
    case TURNOVER_BE = '80001';
    case TURNOVER_NL = '80000';
    case VAT_OSS = '18620';
    case VAT_REDUCED = '18100';
    case VAT_STANDARD = '18000';

    public function description(): string
    {
        return match ($this) {
            self::ACCRUED_INCOME => 'Accrued income',
            self::ACCRUED_CHARGES => 'Accrued charges',
            self::ADVERTISING_COSTS => 'Advertising costs',
            self::COMPENSATIONS => 'Compensations and fees',
            self::OTHER_SALES_COSTS => 'Other sales costs',
            self::OUTSOURCED_WORK => 'Outsourced work',
            self::PACKAGING_COSTS => 'Postal costs',
            self::PRE_TAX => 'To receive VAT',
            self::ROUNDING_DIFFERENCES => 'Rounding differences',
            self::SUSPENSE_ACCOUNT_BOL_COM => 'Suspense account - Bol.com',
            self::TURNOVER_BE => 'Turnover Belgium',
            self::TURNOVER_NL => 'Turnover Netherlands',
            self::VAT_OSS => 'To pay VAT to an EU country in case of OSS registration',
            self::VAT_REDUCED => 'To pay NL VAT reduced rate',
            self::VAT_STANDARD => 'To pay NL VAT standard rate',
        };
    }

    public function type(): string
    {
        return match ($this) {
            self::OTHER_SALES_COSTS,
            self::OUTSOURCED_WORK,
            self::PACKAGING_COSTS,
            self::ADVERTISING_COSTS,
            self::COMPENSATIONS,
            self::ACCRUED_INCOME => 'debit',
            self::TURNOVER_NL,
            self::TURNOVER_BE,
            self::VAT_STANDARD,
            self::VAT_REDUCED,
            self::VAT_OSS,
            self::ACCRUED_CHARGES => 'credit',
            default => ''
        };
    }

    public static function debit(): array
    {
        return [
            self::OTHER_SALES_COSTS->value,
            self::OUTSOURCED_WORK->value,
            self::PACKAGING_COSTS->value,
            self::ADVERTISING_COSTS->value,
            self::PRE_TAX->value,
            self::COMPENSATIONS->value,
            self::ACCRUED_INCOME->value,
        ];
    }

    public static function credit(): array
    {
        return [
            self::TURNOVER_NL->value,
            self::TURNOVER_BE->value,
            self::VAT_STANDARD->value,
            self::VAT_REDUCED->value,
            self::VAT_OSS->value,
            self::ACCRUED_CHARGES->value,
        ];
    }
}
