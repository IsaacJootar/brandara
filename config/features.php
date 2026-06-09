<?php

/*
|--------------------------------------------------------------------------
| Brandara — Feature Registry
|--------------------------------------------------------------------------
|
| Single source of truth for which features are available on each plan.
| Used by PlanFeatureService and the <x-tier-gate> Blade component.
|
| Plans (in order): starter → pro → agency
|
| When Module 22 (Admin Panel) is built, this config will be replaced by
| a database-driven plan_features table. PlanFeatureService abstracts that
| switch — no Blade views or Livewire components need to change.
|
| Generation limits:
|   starter → 30/month
|   pro     → unlimited
|   agency  → unlimited
|
| Brand limits:
|   starter → 1
|   pro     → 3
|   agency  → unlimited (0 = unlimited)
|
| Storage limits (MB):
|   starter → 500
|   pro     → 2048
|   agency  → 10240
|
*/

return [

    // ── Generation limits ─────────────────────────────────────────────────
    'generation_limits' => [
        'starter' => 30,
        'pro'     => 0, // 0 = unlimited
        'agency'  => 0,
    ],

    // ── Brand limits ──────────────────────────────────────────────────────
    'brand_limits' => [
        'starter' => 1,
        'pro'     => 3,
        'agency'  => 0, // 0 = unlimited
    ],

    // ── Storage limits (MB) ───────────────────────────────────────────────
    'storage_limits_mb' => [
        'starter' => 500,
        'pro'     => 2048,
        'agency'  => 10240,
    ],

    // ── Feature gates ─────────────────────────────────────────────────────
    // Each key is a feature slug. 'plans' lists which plans have access.
    'gates' => [

        // All platforms (Instagram, WhatsApp, TikTok, Threads publishing)
        // Basic only gets Facebook, LinkedIn, X
        'all_platforms' => [
            'plans'       => ['pro', 'agency'],
            'label'       => 'All 7 platforms',
            'description' => 'Publish to Instagram, WhatsApp, TikTok and Threads.',
            'upgrade_to'  => 'pro',
        ],

        // Engagement & lead tracker
        'lead_tracker' => [
            'plans'       => ['pro', 'agency'],
            'label'       => 'Engagement & lead tracker',
            'description' => 'See who engaged with your posts, tag them as leads, and follow up.',
            'upgrade_to'  => 'pro',
        ],

        // Results / analytics dashboard
        'analytics' => [
            'plans'       => ['pro', 'agency'],
            'label'       => 'Analytics dashboard',
            'description' => 'Track engagement, reach, and best posting times across all platforms.',
            'upgrade_to'  => 'pro',
        ],

        // Trend monitoring
        'trends' => [
            'plans'       => ['pro', 'agency'],
            'label'       => 'Trend monitoring',
            'description' => 'See trending topics and content signals in your industry to stay ahead.',
            'upgrade_to'  => 'pro',
        ],

        // AI Visibility & Trends module
        'ai_visibility' => [
            'plans'       => ['pro', 'agency'],
            'label'       => 'AI Visibility reports',
            'description' => 'See where your brand appears in ChatGPT, Gemini, and Perplexity answers.',
            'upgrade_to'  => 'pro',
        ],

        // Multiple brands
        'multiple_brands' => [
            'plans'       => ['pro', 'agency'],
            'label'       => 'Multiple brands',
            'description' => 'Manage up to 3 brands under one account.',
            'upgrade_to'  => 'pro',
        ],

        // Unlimited brands
        'unlimited_brands' => [
            'plans'       => ['agency'],
            'label'       => 'Unlimited brands',
            'description' => 'Manage unlimited client brands from one workspace.',
            'upgrade_to'  => 'agency',
        ],

        // Client workspaces
        'client_workspaces' => [
            'plans'       => ['agency'],
            'label'       => 'Client workspaces',
            'description' => 'Fully isolated workspace per client brand.',
            'upgrade_to'  => 'agency',
        ],

        // Approval workflow
        'approvals' => [
            'plans'       => ['agency'],
            'label'       => 'Content review & approvals',
            'description' => 'Send posts for client review before publishing.',
            'upgrade_to'  => 'agency',
        ],

        // White-label reports
        'white_label' => [
            'plans'       => ['agency'],
            'label'       => 'White-label reports',
            'description' => 'Send branded reports to clients with your agency name.',
            'upgrade_to'  => 'agency',
        ],

        // Engagement automation (auto-like / auto-comment)
        'engagement_automation' => [
            'plans'       => ['pro', 'agency'],
            'label'       => 'Engagement automation',
            'description' => 'Auto-like and contextual comment rules to grow your reach.',
            'upgrade_to'  => 'pro',
        ],
    ],

];
