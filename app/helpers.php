<?php

if (!function_exists('make_integer')) {
    /**
     * Transforms a float value into an integer.
     *
     * @param float $amount
     * @return int
     */
    function make_integer(float $amount): int
    {
        return (int) round(($amount * 100));
    }
}

if (!function_exists('make_float')) {
    /**
     * Transforms a integer value into a float/double.
     *
     * @param int $integer
     * @return float
     */
    function make_float(int $integer): float
    {
        return (float) ($integer / 100);
    }
}

if (!function_exists('name_of_month')) {
    /**
     * Transforms a float value into an integer.
     *
     * @param float $monthNum
     * @return string
     */
    function name_of_month(float $monthNum): string
    {
        return date("F", mktime(0, 0, 0, $monthNum, 10));
    }
}

if (!function_exists('get_past_months')) {
    /**
     * If $from parameter is passed then it will return months before that date otherwise it returns n months from current date.
     * @param \Carbon\Carbon $untilDate
     * @param int $previousMonths
     * @return \Carbon\CarbonPeriod
     */
    function get_past_months(\Carbon\Carbon $untilDate = null, int $previousMonths = 6): \Carbon\CarbonPeriod
    {
        $endDate = $untilDate ? $untilDate->copy()->startOfMonth() : now()->subMonths(1)->startOfMonth();
        $startDate = $untilDate ? $untilDate->copy()->subMonths($previousMonths)->startOfMonth() : now()->subMonths($previousMonths)->startOfMonth();
        return \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
    }
}
