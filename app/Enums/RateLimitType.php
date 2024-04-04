<?php

namespace App\Enums;

/**
 * The following rate limits are from the Bol.com documentation:
 * https://api.bol.com/retailer/public/Retailer-API/ratelimits.html
 *
 */
enum RateLimitType: string
{
    case INVOICE = 'bol-com-invoice-rate-limit';
    case INVOICES = 'bol-com-invoices-rate-limit';
    case ORDER = 'bol-com-order-rate-limit';
    case ORDERS = 'bol-com-orders-rate-limit';
    case SPECIFICATION = 'bol-com-specification-rate-limit';

    public static function defaultResetTime(): int
    {
        return 60;
    }

    public function limitTimeInSeconds(): int
    {
        return match ($this) {
            self::ORDER => 1,
            self::INVOICE, self::INVOICES, self::SPECIFICATION, self::ORDERS => 60,
        };
    }

    public function limitAmount(): int
    {
        return match ($this) {
            self::ORDER, self::ORDERS => 25,
            self::INVOICE, self::INVOICES => 24,
            self::SPECIFICATION => 9,
        };
    }

    public function limitInterval(): float|int|string
    {
        return match ($this) {
            self::ORDER => now()->addSeconds((($this->limitTimeInSeconds() * 2) / $this->limitAmount()))->timestamp,
            default => now()->addSeconds(($this->limitTimeInSeconds() / $this->limitAmount()))->timestamp
        };
    }
}
