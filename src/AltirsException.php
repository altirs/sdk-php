<?php

declare(strict_types=1);

namespace Altirs;

use RuntimeException;

class AltirsException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly string $requestId,
    ) {
        parent::__construct($message, $status);
    }
}
