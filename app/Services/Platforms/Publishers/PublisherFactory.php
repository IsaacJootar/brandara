<?php

namespace App\Services\Platforms\Publishers;

class PublisherFactory
{
    /**
     * Resolve a publisher for the given platform.
     *
     * When real OAuth apps are configured (config/services.php), this returns
     * the live publisher. Otherwise falls back to FakePublisher so the pipeline
     * is fully testable in development.
     */
    public function for(string $platform): PlatformPublisher
    {
        if (! config('services.publishing.live', false)) {
            return new FakePublisher;
        }

        return match ($platform) {
            // Live publishers will be wired up here as OAuth dev apps go live.
            // 'linkedin'  => new LinkedInPublisher(),
            // 'twitter'   => new TwitterPublisher(),
            default => new FakePublisher,
        };
    }
}
