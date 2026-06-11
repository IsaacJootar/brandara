<?php

namespace App\Services\Plan;

use App\Models\AdminSetting;
use App\Models\Workspace;

/**
 * Single entry point for all plan/tier checks.
 *
 * Phase 1: reads from config/features.php (fallback)
 * Phase 2 (now): reads from admin_settings DB table first, falls back to config
 *
 * Admin panel edits the DB — takes effect immediately for all users.
 */
class PlanFeatureService
{
    public function planHas(string $plan, string $feature): bool
    {
        $gate = $this->gate($feature);

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
        // Try DB first
        $dbGates = AdminSetting::getJson('feature_gates');
        if (! empty($dbGates) && isset($dbGates[$feature])) {
            return $dbGates[$feature];
        }

        // Fallback to config
        return config("features.gates.{$feature}");
    }

    public function generationLimit(string $plan): int
    {
        $dbLimits = AdminSetting::getJson('generation_limits');
        if (! empty($dbLimits) && isset($dbLimits[$plan])) {
            return (int) $dbLimits[$plan];
        }

        return (int) config("features.generation_limits.{$plan}", 30);
    }

    public function brandLimit(string $plan): int
    {
        $dbLimits = AdminSetting::getJson('brand_limits');
        if (! empty($dbLimits) && isset($dbLimits[$plan])) {
            return (int) $dbLimits[$plan];
        }

        return (int) config("features.brand_limits.{$plan}", 1);
    }

    public function storageLimitMb(string $plan): int
    {
        $dbLimits = AdminSetting::getJson('storage_limits_mb');
        if (! empty($dbLimits) && isset($dbLimits[$plan])) {
            return (int) $dbLimits[$plan];
        }

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
        $gate = $this->gate($feature);
        $upgradeTo = $gate['upgrade_to'] ?? 'pro';

        return $this->planLabel($upgradeTo);
    }

    /**
     * Get all feature gates (for admin panel display).
     */
    public function allGates(): array
    {
        $dbGates = AdminSetting::getJson('feature_gates');

        if (! empty($dbGates)) {
            return $dbGates;
        }

        return config('features.gates', []);
    }
}
