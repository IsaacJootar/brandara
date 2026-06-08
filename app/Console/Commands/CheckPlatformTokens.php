<?php

namespace App\Console\Commands;

use App\Models\PlatformConnection;
use App\Services\NotificationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('platforms:check-tokens')]
#[Description('Detect expired platform tokens and notify workspace users.')]
class CheckPlatformTokens extends Command
{
    public function handle(NotificationService $notifications): int
    {
        // Mark tokens past expiry as expired
        $expired = PlatformConnection::where('status', 'connected')
            ->whereNotNull('token_expires_at')
            ->where('token_expires_at', '<', now())
            ->get();

        foreach ($expired as $connection) {
            $connection->update(['status' => 'expired']);
            $notifications->tokenExpired($connection);
            $this->line("Expired: {$connection->platform} for brand {$connection->brand_id}");
        }

        $this->info("Processed {$expired->count()} expired token(s).");

        return self::SUCCESS;
    }
}
