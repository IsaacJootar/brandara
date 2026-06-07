<?php

namespace App\Services\Platforms\Publishers;

class PublishResult
{
    public function __construct(
        public bool $success,
        public ?string $liveUrl = null,
        public ?string $errorCode = null,    // token_expired | rate_limited | media_rejected | network_timeout | unknown
        public ?string $errorMessage = null, // plain English
    ) {}

    public static function ok(string $liveUrl): self
    {
        return new self(success: true, liveUrl: $liveUrl);
    }

    public static function fail(string $code, string $message): self
    {
        return new self(success: false, errorCode: $code, errorMessage: $message);
    }
}
