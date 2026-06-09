<?php

namespace App\Services\Ai;

use App\Models\Brand;
use App\Services\Plan\PlanFeatureService;

/**
 * Shared trait for all AI generation services.
 * Checks the monthly generation limit before calling Claude
 * and increments the counter after a successful generation.
 */
trait ChecksGenerationLimit
{
    private function enforceLimit(Brand $brand): void
    {
        $workspace = $brand->workspace;
        $plan = app(PlanFeatureService::class);

        if ($plan->isGenerationLimitReached($workspace)) {
            $limit = $plan->generationLimit($workspace->plan);
            throw new \RuntimeException(
                "You've used all {$limit} content generations for this month. Upgrade to Growth for unlimited generations."
            );
        }
    }

    private function incrementUsage(Brand $brand): void
    {
        app(PlanFeatureService::class)->incrementGenerations($brand->workspace);
    }
}
