<?php

namespace App\Traits;

trait EnhancesEnum
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }

    public static function random(): string
    {
        $random = array_rand(self::values());
        return self::values()[$random];
    }

    public static function randomName(): self|null
    {
        $random = array_rand(self::names());
        return self::tryFrom(self::values()[$random]);
    }
}
