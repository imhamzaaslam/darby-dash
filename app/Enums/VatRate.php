<?php

namespace App\Enums;

enum VatRate: string
{
    case STANDARD = 'standard_rate';
    case REDUCED = 'reduced_rate';
    case REDUCED_ALT = 'reduced_rate_alt';
    case SUPER_REDUCED = 'super_reduced_rate';
    case ZERO = 'zero_rate';
    case OUT_OF_SCOPE = 'OS';

    public static function getName(string $rate): self
    {
        return match ($rate) {
            'S' => self::STANDARD,
            'R' => self::REDUCED,
            'R2' => self::REDUCED_ALT,
            'SR' => self::SUPER_REDUCED,
            'Z' => self::ZERO,
            'OS' => self::OUT_OF_SCOPE
        };
    }
}
