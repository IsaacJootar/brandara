<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use Illuminate\Database\Seeder;

class AdminSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Feature gates (JSON — mirrors config/features.php gates) ──────
            ['key' => 'feature_gates', 'value' => json_encode([
                'all_platforms' => ['plans' => ['pro', 'agency'], 'label' => 'All 7 platforms', 'description' => 'Publish to Instagram, WhatsApp, TikTok and Threads.', 'upgrade_to' => 'pro'],
                'lead_tracker' => ['plans' => ['pro', 'agency'], 'label' => 'Engagement & lead tracker', 'description' => 'See who engaged with your posts, tag them as leads, and follow up.', 'upgrade_to' => 'pro'],
                'analytics' => ['plans' => ['pro', 'agency'], 'label' => 'Analytics dashboard', 'description' => 'Track engagement, reach, and best posting times across all platforms.', 'upgrade_to' => 'pro'],
                'trends' => ['plans' => ['pro', 'agency'], 'label' => 'Trend monitoring', 'description' => 'See trending topics and content signals in your industry to stay ahead.', 'upgrade_to' => 'pro'],
                'ai_visibility' => ['plans' => ['pro', 'agency'], 'label' => 'AI Visibility reports', 'description' => 'See where your brand appears in ChatGPT, Gemini, and Perplexity answers.', 'upgrade_to' => 'pro'],
                'multiple_brands' => ['plans' => ['pro', 'agency'], 'label' => 'Multiple brands', 'description' => 'Manage up to 3 brands under one account.', 'upgrade_to' => 'pro'],
                'unlimited_brands' => ['plans' => ['agency'], 'label' => 'Unlimited brands', 'description' => 'Manage unlimited client brands from one workspace.', 'upgrade_to' => 'agency'],
                'client_workspaces' => ['plans' => ['agency'], 'label' => 'Client workspaces', 'description' => 'Fully isolated workspace per client brand.', 'upgrade_to' => 'agency'],
                'approvals' => ['plans' => ['agency'], 'label' => 'Content review & approvals', 'description' => 'Send posts for client review before publishing.', 'upgrade_to' => 'agency'],
                'white_label' => ['plans' => ['agency'], 'label' => 'White-label reports', 'description' => 'Send branded reports to clients with your agency name.', 'upgrade_to' => 'agency'],
                'engagement_automation' => ['plans' => ['pro', 'agency'], 'label' => 'Engagement automation', 'description' => 'Auto-like and contextual comment rules to grow your reach.', 'upgrade_to' => 'pro'],
            ]), 'group' => 'features'],

            // ── Limits (JSON) ────────────────────────────────────────────────
            ['key' => 'generation_limits', 'value' => json_encode(['starter' => 30, 'pro' => 0, 'agency' => 0]), 'group' => 'features'],
            ['key' => 'brand_limits', 'value' => json_encode(['starter' => 1, 'pro' => 3, 'agency' => 0]), 'group' => 'features'],
            ['key' => 'storage_limits_mb', 'value' => json_encode(['starter' => 500, 'pro' => 2048, 'agency' => 10240]), 'group' => 'features'],

            // ── AI settings ──────────────────────────────────────────────────
            ['key' => 'ai_default_provider', 'value' => 'claude', 'group' => 'ai'],
            ['key' => 'ai_presence_claude_enabled', 'value' => '1', 'group' => 'ai'],
            ['key' => 'ai_presence_chatgpt_enabled', 'value' => '1', 'group' => 'ai'],
            ['key' => 'ai_presence_gemini_enabled', 'value' => '1', 'group' => 'ai'],
            ['key' => 'ai_presence_perplexity_enabled', 'value' => '0', 'group' => 'ai'],

            // ── General ──────────────────────────────────────────────────────
            ['key' => 'platform_name', 'value' => 'Brandara', 'group' => 'general'],
            ['key' => 'support_email', 'value' => 'hello@brandara.com', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            AdminSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
