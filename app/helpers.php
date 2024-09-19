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

if (!function_exists('convertToMinutes')) {
    function convertToMinutes(string $estTime): int
    {
        preg_match('/(\d+)h (\d+)m/', $estTime, $matches);
        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];

        $totalMinutes = ($hours * 60) + $minutes;

        return $totalMinutes;
    }
}

if (!function_exists('convertToHoursAndMinutes')) {
    function convertToHoursAndMinutes(int $totalMinutes): string
    {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        $formattedTime = sprintf("%02dh %02dm", $hours, $minutes);

        return $formattedTime;
    }
}

if (!function_exists('convertMinutesToDays')) {
    function convertMinutesToDays(int $totalMinutes): float
    {
        $minutesInADay = 8 * 60;
        $days = $totalMinutes / $minutesInADay;
        $minutesLeft = $totalMinutes % $minutesInADay;
        $hours = floor($minutesLeft / 60);
        if($hours > 4) {
            $days += 1;
        }

        return $days;
    }
}

if(!function_exists('formatTimeInDaysHoursMinutes')) {
    function formatTimeInDaysHoursMinutes(int $totalMinutes): string
    {
        if ($totalMinutes == 0) {
            return '00h 00m';
        }

        $minutesPerHour = 60;
        $hoursPerDay = 8;

        $days = floor($totalMinutes / ($hoursPerDay * $minutesPerHour));
        $hours = floor(($totalMinutes % ($hoursPerDay * $minutesPerHour)) / $minutesPerHour);
        $minutes = $totalMinutes % $minutesPerHour;

        $formattedTime = '';
        if ($days > 0) {
            if($hours == 0 && $minutes == 0) {
                $formattedTime .= $days == 1 ? sprintf("%d day", $days) : sprintf("%d days", $days);
            } else {
                $formattedTime .= sprintf("%02dd", $days);
                $formattedTime .= $hours > 0 ? sprintf(" %02dh", $hours) : '';
                $formattedTime .= $minutes > 0 ? sprintf(" %02dm", $minutes) : '';
            }
        } else {
            $formattedTime = sprintf("%02dh %02dm", $hours, $minutes);
        }

        return $formattedTime;
    }
}

if (!function_exists('convertToDefaultFormat')) {
    function convertToDefaultFormat(string $input): string
    {
        $parts = explode(' ', $input);

        if (count($parts) === 1) {
            $hours = str_pad($input, 2, '0', STR_PAD_LEFT);
            $minutes = '00';
        } elseif (count($parts) === 2) {
            $hours = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $minutes = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
        } else {
            // Handle other cases as needed
            $hours = '00';
            $minutes = '00';
        }

        return $hours."h ".$minutes."m";
    }
}

if (!function_exists('format_date')) {
    function format_date(string $date): string
    {
        return date('m/d/Y h:i a', strtotime($date));
    }
}

if (!function_exists('isAdminOrManager')) {
    function isAdminOrManager(): bool
    {
        return auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Project Manager');
    }
}

if (!function_exists('formatToTwoDecimalPlaces')) {
    function formatToTwoDecimalPlaces($value)
    {
        $floatValue = floatval($value);
        return number_format($floatValue, 2, '.', '');
    }
}
