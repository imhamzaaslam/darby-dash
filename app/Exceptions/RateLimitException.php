<?php

namespace App\Exceptions;

use App\Enums\RateLimitType;
use Exception;
use Throwable;

class RateLimitException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable|null $previous = null,
        private readonly array $headers,
        private ?RateLimitType $limitType = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function setLimitType(RateLimitType $limitType): void
    {
        $this->limitType = $limitType;
    }

    public function limitType(): ?RateLimitType
    {
        return $this->limitType;
    }
}
