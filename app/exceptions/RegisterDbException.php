<?php

namespace App\exceptions;

use RuntimeException;

class RegisterDbException extends RuntimeException
{
    private int $statusCode;
    private array $details;

    public function __construct(
        string      $message,
        int         $statusCode = 500,
        array       $details = [],
        ?\Throwable $previous = null
    )
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}