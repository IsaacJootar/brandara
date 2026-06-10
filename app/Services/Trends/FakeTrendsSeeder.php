<?php

namespace App\Services\Trends;

use App\Models\Brand;
use App\Models\TrendSignal;

/**
 * Seeds realistic fake trend data for development and demo.
 * Activated via: php artisan trends:seed-fake {brand_slug}
 *
 * When real platform APIs / Google Trends / X API are approved,
 * replace with LiveTrendsFetcher that writes to the same table with source='api'.
 */
class FakeTrendsSeeder
{
    /** @var array<string, array<string>> */
    private array $industrySignals = [
        ['title' => 'AI tools for small business productivity', 'platform' => 'linkedin', 'tags' => ['#AItools', '#SmallBusiness', '#Productivity'], 'strength' => 92],
        ['title' => 'Founder-led content is outperforming brand accounts', 'platform' => 'linkedin', 'tags' => ['#FounderContent', '#PersonalBranding', '#B2B'], 'strength' => 89],
        ['title' => 'Short-form video is dominating B2B discovery', 'platform' => 'tiktok', 'tags' => ['#B2BMarketing', '#ShortVideo', '#TikTokBusiness'], 'strength' => 87],
        ['title' => 'Nigeria tech ecosystem growth stories getting high engagement', 'platform' => 'twitter', 'tags' => ['#NigeriaTech', '#StartupNG', '#AfricanTech'], 'strength' => 85],
        ['title' => 'LinkedIn newsletters gaining subscribers 3x faster than 2023', 'platform' => 'linkedin', 'tags' => ['#LinkedInNewsletter', '#ContentStrategy'], 'strength' => 82],
        ['title' => 'Voice notes in WhatsApp replacing long emails for B2B', 'platform' => 'all', 'tags' => ['#WhatsAppBusiness', '#B2BCommunication'], 'strength' => 78],
        ['title' => 'Personal brand > company brand for trust in Africa', 'platform' => 'all', 'tags' => ['#PersonalBranding', '#AfricanBusiness', '#Trust'], 'strength' => 76],
        ['title' => 'Case studies with real numbers outperform motivational posts', 'platform' => 'linkedin', 'tags' => ['#CaseStudy', '#SocialProof', '#B2BContent'], 'strength' => 74],
        ['title' => 'Instagram carousel posts hitting 4x organic reach vs single images', 'platform' => 'instagram', 'tags' => ['#InstagramCarousel', '#OrganicReach'], 'strength' => 71],
        ['title' => 'Threads growing fast as X alternative for African creators', 'platform' => 'threads', 'tags' => ['#Threads', '#TwitterAlternative', '#AfricaCreators'], 'strength' => 68],
    ];

    /** @var array<string, array<string>> */
    private array $formatSignals = [
        ['title' => 'LinkedIn text posts with 1–3 line hook', 'platform' => 'linkedin', 'tags' => ['hook', 'text-post'], 'strength' => 94, 'description' => 'Short punchy opener — "I turned down $50k." — followed by story. Outperforms articles by 3x reach.'],
        ['title' => 'Behind-the-scenes reels (30–45 seconds)', 'platform' => 'instagram', 'tags' => ['reels', 'bts', 'short-video'], 'strength' => 91, 'description' => 'Raw, unedited glimpses of your work process. Authenticity beats production quality every time.'],
        ['title' => 'Carousel: "X mistakes I made so you don\'t have to"', 'platform' => 'linkedin', 'tags' => ['carousel', 'mistakes', 'lessons'], 'strength' => 88, 'description' => 'Lesson-based carousels with a clear mistake on slide 1 and the fix on the last slide. High save rate.'],
        ['title' => 'TikTok "day in my life" as a [profession]', 'platform' => 'tiktok', 'tags' => ['dayinlife', 'authenticity'], 'strength' => 85, 'description' => 'Informal walkthrough of a typical workday. Comment engagement is 4x higher than polished content.'],
        ['title' => 'Twitter/X threads with a controversial take', 'platform' => 'twitter', 'tags' => ['thread', 'hot-take', 'engagement'], 'strength' => 83, 'description' => 'Start with an opinion most people disagree with. Back it up with data. Retweets spike 5x vs neutral threads.'],
        ['title' => 'WhatsApp broadcast with voice note intro', 'platform' => 'all', 'tags' => ['voice-note', 'broadcast', 'warmth'], 'strength' => 80, 'description' => 'Send a 30-second voice note before the text message. Open rates jump from 40% to 85%.'],
        ['title' => 'Instagram quote cards with brand colours', 'platform' => 'instagram', 'tags' => ['quote-card', 'brand', 'shareability'], 'strength' => 77, 'description' => 'Simple quote on a bold background. Easy to share to Stories. High saves for future reference.'],
        ['title' => 'LinkedIn "what I wish I knew" posts', 'platform' => 'linkedin', 'tags' => ['lessons', 'wisdom', 'relatability'], 'strength' => 75, 'description' => 'Regret-framed advice post. Strong emotional hook. Consistent top performer for B2B founders.'],
        ['title' => 'Threads rapid-fire opinion list', 'platform' => 'threads', 'tags' => ['opinions', 'list', 'quick-read'], 'strength' => 71, 'description' => '10 unpopular opinions in 10 short lines. Fast to consume, easy to screenshot and share.'],
        ['title' => 'Facebook group post with a direct question', 'platform' => 'facebook', 'tags' => ['community', 'question', 'comments'], 'strength' => 68, 'description' => 'Ask a specific question that your audience has a strong opinion on. Comment volume drives organic reach.'],
    ];

    /** @var array<string, array<string>> */
    private array $competitorSignals = [
        ['title' => 'Competitor posting 2x daily on LinkedIn — engagement holding strong', 'platform' => 'linkedin', 'tags' => ['posting-frequency', 'competitor'], 'strength' => 88],
        ['title' => '#PersonalBrandingNigeria gaining 300+ weekly posts', 'platform' => 'twitter', 'tags' => ['#PersonalBrandingNigeria', '#trending'], 'strength' => 85],
        ['title' => 'Agency accounts shifting from company voice to founder voice', 'platform' => 'linkedin', 'tags' => ['founder-voice', 'agency-trend'], 'strength' => 82],
        ['title' => '#AfricanFounder hashtag reach up 40% this month', 'platform' => 'all', 'tags' => ['#AfricanFounder', '#trending', '#growth'], 'strength' => 79],
        ['title' => 'Competitor launched LinkedIn newsletter — growing fast', 'platform' => 'linkedin', 'tags' => ['newsletter', 'competitor-move'], 'strength' => 76],
        ['title' => '#B2BNigeria discussions peaking — 500+ posts this week', 'platform' => 'twitter', 'tags' => ['#B2BNigeria', '#conversation'], 'strength' => 74],
        ['title' => 'Video content from competitors getting 3x more comments than text', 'platform' => 'instagram', 'tags' => ['video', 'competitor', 'format-shift'], 'strength' => 72],
        ['title' => '#LagosBusiness trending — opportunity to join the conversation', 'platform' => 'twitter', 'tags' => ['#LagosBusiness', '#opportunity'], 'strength' => 69],
        ['title' => 'Top consultants in your niche posting testimonials daily', 'platform' => 'linkedin', 'tags' => ['testimonials', 'social-proof'], 'strength' => 67],
        ['title' => '#TechAfrica growing 25% month-on-month engagement', 'platform' => 'all', 'tags' => ['#TechAfrica', '#growth'], 'strength' => 64],
    ];

    public function seed(Brand $brand): int
    {
        // Clear old fake signals before reseeding
        TrendSignal::where('brand_id', $brand->id)
            ->where('source', 'fake')
            ->delete();

        $created = 0;

        foreach ($this->industrySignals as $signal) {
            TrendSignal::create([
                'brand_id' => $brand->id,
                'category' => 'industry',
                'platform' => $signal['platform'],
                'title' => $signal['title'],
                'description' => $signal['description'] ?? null,
                'strength' => $signal['strength'],
                'tags' => $signal['tags'],
                'source' => 'fake',
                'fetched_at' => now(),
            ]);
            $created++;
        }

        foreach ($this->formatSignals as $signal) {
            TrendSignal::create([
                'brand_id' => $brand->id,
                'category' => 'format',
                'platform' => $signal['platform'],
                'title' => $signal['title'],
                'description' => $signal['description'] ?? null,
                'strength' => $signal['strength'],
                'tags' => $signal['tags'],
                'source' => 'fake',
                'fetched_at' => now(),
            ]);
            $created++;
        }

        foreach ($this->competitorSignals as $signal) {
            TrendSignal::create([
                'brand_id' => $brand->id,
                'category' => 'competitor',
                'platform' => $signal['platform'],
                'title' => $signal['title'],
                'description' => $signal['description'] ?? null,
                'strength' => $signal['strength'],
                'tags' => $signal['tags'],
                'source' => 'fake',
                'fetched_at' => now(),
            ]);
            $created++;
        }

        return $created;
    }
}
