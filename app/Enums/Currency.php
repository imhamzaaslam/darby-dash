<?php

namespace App\Enums;

enum Currency: string
{
    case BGN = 'Bulgarian Lev';
    case CHF = 'Swiss Franc';
    case CZK = 'Czech Koruna';
    case DKK = 'Danish Krone';
    case EUR = 'Euro';
    case GBP = 'British Pound';
    case HRK = 'Croatian Kuna';
    case HUF = 'Hungarian Forint';
    case NOK = 'Norwegian Krone';
    case PLN = 'Polish Złoty';
    case RON = 'Romanian Leu';
    case RSD = 'Serbian Dinar';
    case RUB = 'Russian Ruble';
    case SEK = 'Swedish Krona';
    case TRY = 'Turkish Lira';

    public static function fromName(string $currency): self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $currency) {
                return $case;
            }
        }

        return self::EUR;
    }
}
