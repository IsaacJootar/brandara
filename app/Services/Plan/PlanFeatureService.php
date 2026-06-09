<?php

namespace App\Services\Plan;

use App\Models\Workspace;

/**
 * Single entry point for all plan/tier checks.
 *
 * Phase 1 (now): reads from config/features.php
 * Phase 2 (Module 22): reads from plan_features DB table
 */
class PlanFeatureService
{
    public function planHas(string $plan, string $feature): bool
    {
        $gate = config("features.gates.{$feature}");

        if (! $gate) {
            return true;
        }

        return in_array($plan, $gate['plans'] ?? []);
    }

    public function workspaceHas(Workspace $workspace, string $feature): bool
    {
        return $this->planHas($workspace->plan, $feature);
    }

    /** @return array{plans: string[], label: string, description: string, upgrade_to: string}|null */
    public function gate(string $feature): ?array
    {
        return config("features.gates.{$feature}");
    }

    public function generationLimit(string $plan): int
    {
        return (int) config("features.generation_limits.{$plan}", 30);
    }

    public function brandLimit(string $plan): int
    {
        return (int) config("features.brand_limits.{$plan}", 1);
    }

    public function storageLimitMb(string $plan): int
    {
        return (int) config("features.storage_limits_mb.{$plan}", 500);
    }

    public function isGenerationLimitReached(Workspace $workspace): bool
    {
        $limit = $this->generationLimit($workspace->plan);

        if ($limit === 0) {
            return false;
        }

        return $workspace->ai_generations_used >= $limit;
    }

    public function isBrandLimitReached(Workspace $workspace): bool
    {
        $limit = $this->brandLimit($workspace->plan);

        if ($limit === 0) {
            return false;
        }

        return $workspace->brands()->count() >= $limit;
    }

    public function incrementGenerations(Workspace $workspace): void
    {
        if ($workspace->usage_reset_date === null || $workspace->usage_reset_date->lt(now()->startOfMonth())) {
            $workspace->ai_generations_used = 0;
            $workspace->usage_reset_date = now()->startOfMonth()->toDateString();
        }

        $workspace->increment('ai_generations_used');
    }

    public function planLabel(string $plan): string
    {
        return match ($plan) {
            'starter' => 'Basic',
            'pro' => 'Growth',
            'agency' => 'Agency',
            default => ucfirst($plan),
        };
    }

    public function upgradePlanLabel(string $feature): string
    {
        $upgradeTo = config("features.gates.{$feature}.upgrade_to", 'pro');

        return $this->planLabel($upgradeTo);
    }
}
