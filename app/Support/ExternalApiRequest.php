<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Throwable;

class ExternalApiRequest
{
    public function make(): PendingRequest
    {
        return Http::connectTimeout(5)
            ->timeout(15)
            ->retry(
                [200, 500],
                when: fn (Throwable $exception): bool => $exception instanceof ConnectionException
                    || ($exception instanceof RequestException && $exception->response->serverError()),
                throw: false,
            );
    }
}
