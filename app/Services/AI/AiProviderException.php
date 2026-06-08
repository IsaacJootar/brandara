<?php

namespace App\Services\Ai;

use RuntimeException;

class AiProviderException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $providerName,
        public readonly bool $isConfigError = false,
    ) {
        parent::__construct($message);
    }
}
