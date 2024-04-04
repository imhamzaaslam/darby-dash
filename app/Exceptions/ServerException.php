<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ServerException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable|null $previous = null,
        private readonly array $options
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function options(): array
    {
        return $this->options;
    }
}
