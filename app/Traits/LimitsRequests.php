<?php

namespace App\Traits;

use App\Enums\RateLimitType;
use App\Exceptions\RateLimitException;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait LimitsRequests
{
    public function limit(RateLimitException $e, RateLimitType $limitType): void
    {
        $secondsRemaining = $this->secondsRemaining($e);

        if (!$secondsRemaining) {
            $secondsRemaining = RateLimitType::defaultResetTime();
        }

        activity('error')->log("Limiter hit! Waiting for {$secondsRemaining} seconds... in " . __CLASS__ . " on line " . __LINE__);
        Log::error("Limiter hit! Waiting for {$secondsRemaining} seconds... in " . __CLASS__ . " on line " . __LINE__);

        Cache::put(
            $limitType->value,
            now()->addSeconds($secondsRemaining)->timestamp,
            $secondsRemaining
        );
    }

    public function secondsRemaining(RateLimitException $e)
    {
        return $e->headers()['Retry-After'] ?? $e->headers()['x-ratelimit-reset'];
    }

    public function getLimitAmount(RateLimitType $limitType): int
    {
        return $limitType->limitAmount();
    }

    public function getLimitTimeInSeconds(RateLimitType $limitType): int
    {
        return $limitType->limitTimeInSeconds();
    }

    public function requestInterval(RateLimitType $limitType): float|int|string
    {
        // For the invoices for example, we are allowed to perform 24 requests every 60 seconds.
        // This function calculates the interval between each request.
        // This will return 2.5, meaning we will release the job back into the queue
        // after every external request for 2.5 seconds.
        return $limitType->limitInterval();
    }

    public function wait(User $user, RateLimitType $limitType): void
    {
        activity()->log(sprintf(
            '%s: releasing back into the queue for %s for user %s on line %s',
            __CLASS__,
            $this->requestInterval($limitType),
            $user->uuid,
            __LINE__ 
        ));
        Log::info(sprintf(
            '%s: releasing back into the queue for %s for user %s on line %s',
            __CLASS__,
            $this->requestInterval($limitType),
            $user->uuid,
            __LINE__ 
        ));
    }
}
