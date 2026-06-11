<?php

namespace App\Services\AiVisibility;

use App\Models\AiVisibilityCheck;
use App\Models\Brand;
use Illuminate\Support\Facades\Http;

/**
 * Scans a brand's website and runs all readiness checks.
 *
 * Tier 1 — automated, low-cost (HTTP fetch + HTML parse)
 * Tier 2 — deeper automated checks (headers, robots.txt parse)
 * Tier 3 — manual (user confirms in the UI)
 *
 * Architecture mirrors Gona Africa's AiReadinessService but adapted for
 * self-serve brand owners. When a paid crawler or Lighthouse integration
 * is available, replace the fetch layer — scoring logic stays unchanged.
 */
class WebsiteScannerService
{
    private string $html = '';

    private string $robotsTxt = '';

    private ?string $sitemapUrl = null;

    private array $headers = [];

    /** Run all automated checks for a brand and persist results. */
    public function scan(Brand $brand, string $websiteUrl): AiVisibilityCheck
    {
        $url = $this->normaliseUrl($websiteUrl);

        // Fetch page data
        $this->html = $this->fetchPage($url);
        $this->headers = $this->fetchHeaders($url);
        $this->robotsTxt = $this->fetchRobotsTxt($url);
        $this->sitemapUrl = $this->discoverSitemap($url);

        $results = $this->runTier1($url, $brand);
        $results = array_merge($results, $this->runTier2($url));

        $manual = $this->initialManualChecks($brand);

        $score = $this->calculateScore($results, $manual);

        return AiVisibilityCheck::updateOrCreate(
            ['brand_id' => $brand->id],
            [
                'website_url' => $url,
                'results' => $results,
                'manual_checks' => $manual,
                'score' => $score['score'],
                'tier1_passed' => $score['tier1'],
                'tier2_passed' => $score['tier2'],
                'tier3_passed' => $score['tier3'],
                'scanned_at' => now(),
            ]
        );
    }

    /** Update a single manual check and recalculate score. */
    public function updateManual(AiVisibilityCheck $check, string $key, bool $passed): AiVisibilityCheck
    {
        $manual = $check->manual_checks ?? [];
        $manual[$key] = $passed ? 'pass' : 'fail';

        $score = $this->calculateScore($check->results ?? [], $manual);

        $check->update([
            'manual_checks' => $manual,
            'score' => $score['score'],
            'tier3_passed' => $score['tier3'],
        ]);

        return $check->fresh();
    }

    // ── Tier 1 — Always-on automated checks ──────────────────────────────────

    private function runTier1(string $url, Brand $brand): array
    {
        return [
            'has_https' => $this->checkHttps($url),
            'site_loads' => ! empty($this->html) ? 'pass' : 'fail',
            'has_title_tag' => $this->checkTitleTag(),
            'has_meta_description' => $this->checkMetaDescription(),
            'has_canonical_tag' => $this->checkCanonicalTag(),
            'has_json_ld_schema' => $this->checkJsonLd(),
            'has_faq_schema' => $this->checkFaqSchema(),
            'has_about_page' => $this->checkLinkedPage(['about', 'who we are', 'about us']),
            'has_contact_page' => $this->checkLinkedPage(['contact', 'get in touch', 'reach us']),
            'has_contact_details_on_site' => $this->checkContactDetails(),
            'mentions_city' => $this->checkMentionsKeyword($brand->workspace?->country ?? ''),
            'mentions_industry' => $this->checkMentionsIndustry($brand),
            'has_robots_txt' => ! empty($this->robotsTxt) ? 'pass' : 'fail',
            'has_xml_sitemap' => $this->sitemapUrl ? 'pass' : 'fail',
            'has_sameas_links' => $this->checkSameAsLinks(),
        ];
    }

    // ── Tier 2 — Advanced automated checks ───────────────────────────────────

    private function runTier2(string $url): array
    {
        return [
            'page_indexable' => $this->checkIndexable(),
            'ai_bots_allowed' => $this->checkAiBotsAllowed(),
            'canonical_matches_url' => $this->checkCanonicalMatchesUrl($url),
            'has_mobile_viewport' => $this->checkMobileViewport(),
            'has_local_business_schema' => $this->checkLocalBusinessSchema(),
        ];
    }

    // ── Tier 3 — Manual checks (initial state: pending) ───────────────────────

    private function initialManualChecks(Brand $brand): array
    {
        // Check if brand already has a saved check to preserve existing manual state
        $existing = AiVisibilityCheck::where('brand_id', $brand->id)->first();
        if ($existing) {
            return $existing->manual_checks ?? $this->blankManualChecks();
        }

        return $this->blankManualChecks();
    }

    private function blankManualChecks(): array
    {
        return [
            'has_google_business_profile' => 'pending',
            'nap_consistent' => 'pending',
            'has_ten_plus_reviews' => 'pending',
            'has_three_plus_listings' => 'pending',
            'social_profiles_linked' => 'pending',
        ];
    }

    // ── Scoring ───────────────────────────────────────────────────────────────

    private function calculateScore(array $results, array $manual): array
    {
        $tier1Keys = ['has_https', 'site_loads', 'has_title_tag', 'has_meta_description',
            'has_canonical_tag', 'has_json_ld_schema', 'has_faq_schema', 'has_about_page',
            'has_contact_page', 'has_contact_details_on_site', 'mentions_city',
            'mentions_industry', 'has_robots_txt', 'has_xml_sitemap', 'has_sameas_links'];

        $tier2Keys = ['page_indexable', 'ai_bots_allowed', 'canonical_matches_url',
            'has_mobile_viewport', 'has_local_business_schema'];

        $tier3Keys = array_keys($this->blankManualChecks());

        $tier1 = count(array_filter($tier1Keys, fn ($k) => ($results[$k] ?? '') === 'pass'));
        $tier2 = count(array_filter($tier2Keys, fn ($k) => ($results[$k] ?? '') === 'pass'));
        $tier3 = count(array_filter($tier3Keys, fn ($k) => ($manual[$k] ?? '') === 'pass'));

        $total = count($tier1Keys) + count($tier2Keys) + count($tier3Keys);
        $passed = $tier1 + $tier2 + $tier3;
        $score = $total > 0 ? (int) round($passed / $total * 100) : 0;

        return compact('score', 'tier1', 'tier2', 'tier3');
    }

    // ── HTTP helpers ──────────────────────────────────────────────────────────

    private function fetchPage(string $url): string
    {
        try {
            $response = Http::withUserAgent('BrandaraBot/1.0')
                ->timeout(10)
                ->get($url);

            return $response->ok() ? $response->body() : '';
        } catch (\Throwable) {
            return '';
        }
    }

    private function fetchHeaders(string $url): array
    {
        try {
            return Http::withUserAgent('BrandaraBot/1.0')->timeout(10)->head($url)->headers();
        } catch (\Throwable) {
            return [];
        }
    }

    private function fetchRobotsTxt(string $url): string
    {
        try {
            $base = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);
            $response = Http::withUserAgent('BrandaraBot/1.0')->timeout(8)->get($base.'/robots.txt');

            return $response->ok() ? $response->body() : '';
        } catch (\Throwable) {
            return '';
        }
    }

    private function discoverSitemap(string $url): ?string
    {
        // Check robots.txt for Sitemap directive
        if ($this->robotsTxt && preg_match('/^Sitemap:\s*(.+)$/im', $this->robotsTxt, $m)) {
            return trim($m[1]);
        }

        // Try default /sitemap.xml
        try {
            $base = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST);
            $res = Http::withUserAgent('BrandaraBot/1.0')->timeout(8)->get($base.'/sitemap.xml');
            if ($res->ok()) {
                return $base.'/sitemap.xml';
            }
        } catch (\Throwable) {
        }

        return null;
    }

    private function normaliseUrl(string $url): string
    {
        $url = trim($url);
        if (! str_starts_with($url, 'http')) {
            $url = 'https://'.$url;
        }

        return rtrim($url, '/');
    }

    // ── Check helpers ─────────────────────────────────────────────────────────

    private function checkHttps(string $url): string
    {
        return str_starts_with($url, 'https://') ? 'pass' : 'fail';
    }

    private function checkTitleTag(): string
    {
        return preg_match('/<title[^>]*>(.+?)<\/title>/si', $this->html) ? 'pass' : 'fail';
    }

    private function checkMetaDescription(): string
    {
        return preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\'].+?["\']/si', $this->html) ? 'pass' : 'fail';
    }

    private function checkCanonicalTag(): string
    {
        return preg_match('/<link[^>]+rel=["\']canonical["\'][^>]+href=["\'].+?["\']/si', $this->html) ? 'pass' : 'fail';
    }

    private function checkJsonLd(): string
    {
        return str_contains($this->html, 'application/ld+json') ? 'pass' : 'fail';
    }

    private function checkFaqSchema(): string
    {
        return (str_contains($this->html, 'FAQPage') || str_contains($this->html, '"faq"')) ? 'pass' : 'fail';
    }

    private function checkLinkedPage(array $keywords): string
    {
        $lower = strtolower($this->html);
        foreach ($keywords as $kw) {
            if (str_contains($lower, $kw)) {
                return 'pass';
            }
        }

        return 'fail';
    }

    private function checkContactDetails(): string
    {
        $lower = strtolower($this->html);
        $patterns = ['mailto:', 'tel:', '@', '+234', '+233', '+254', '+27'];
        foreach ($patterns as $p) {
            if (str_contains($lower, $p)) {
                return 'pass';
            }
        }
        // Email pattern
        if (preg_match('/[\w.+-]+@[\w-]+\.\w+/', $this->html)) {
            return 'pass';
        }

        return 'fail';
    }

    private function checkMentionsKeyword(string $country): string
    {
        $cityKeywords = match ($country) {
            'NG' => ['lagos', 'abuja', 'port harcourt', 'nigeria', 'nigerian'],
            'GH' => ['accra', 'kumasi', 'ghana', 'ghanaian'],
            'KE' => ['nairobi', 'mombasa', 'kenya', 'kenyan'],
            'ZA' => ['johannesburg', 'cape town', 'south africa'],
            default => [],
        };

        if (empty($cityKeywords)) {
            return 'pending';
        }

        $lower = strtolower($this->html);
        foreach ($cityKeywords as $kw) {
            if (str_contains($lower, $kw)) {
                return 'pass';
            }
        }

        return 'fail';
    }

    private function checkMentionsIndustry(Brand $brand): string
    {
        if (! $brand->description && ! $brand->target_audience) {
            return 'pending';
        }

        // Extract industry words from brand description
        $keywords = array_filter(
            explode(' ', strtolower($brand->description ?? $brand->tagline ?? '')),
            fn ($w) => strlen($w) > 4
        );

        $lower = strtolower($this->html);
        foreach (array_slice($keywords, 0, 5) as $kw) {
            if (str_contains($lower, $kw)) {
                return 'pass';
            }
        }

        return 'fail';
    }

    private function checkSameAsLinks(): string
    {
        $profiles = ['linkedin.com', 'twitter.com', 'x.com', 'instagram.com',
            'facebook.com', 'sameAs', 'wikipedia.org'];
        $lower = strtolower($this->html);
        foreach ($profiles as $p) {
            if (str_contains($lower, strtolower($p))) {
                return 'pass';
            }
        }

        return 'fail';
    }

    private function checkIndexable(): string
    {
        if (str_contains(strtolower($this->html), 'noindex')) {
            return 'fail';
        }
        $robotsHeader = strtolower($this->headers['X-Robots-Tag'][0] ?? '');
        if (str_contains($robotsHeader, 'noindex')) {
            return 'fail';
        }

        return 'pass';
    }

    private function checkAiBotsAllowed(): string
    {
        if (empty($this->robotsTxt)) {
            return 'pending';
        }

        $blockedBots = ['GPTBot', 'Google-Extended', 'ClaudeBot', 'PerplexityBot', 'anthropic-ai'];
        $lower = strtolower($this->robotsTxt);

        foreach ($blockedBots as $bot) {
            if (str_contains($lower, strtolower($bot))) {
                // Check if it's a Disallow after a User-agent match
                if (preg_match('/user-agent:\s*'.preg_quote(strtolower($bot), '/').'\s*\n\s*disallow:\s*\//i', $lower)) {
                    return 'fail';
                }
            }
        }

        return 'pass';
    }

    private function checkCanonicalMatchesUrl(string $url): string
    {
        if (preg_match('/<link[^>]+rel=["\']canonical["\'][^>]+href=["\'](.+?)["\']/si', $this->html, $m)) {
            $canonical = rtrim(trim($m[1]), '/');
            $primary = rtrim($url, '/');

            return str_contains($canonical, parse_url($primary, PHP_URL_HOST)) ? 'pass' : 'fail';
        }

        return 'fail';
    }

    private function checkMobileViewport(): string
    {
        return preg_match('/<meta[^>]+name=["\']viewport["\'][^>]+content/si', $this->html) ? 'pass' : 'fail';
    }

    private function checkLocalBusinessSchema(): string
    {
        $types = ['LocalBusiness', 'Restaurant', 'Hospital', 'LegalService',
            'FinancialService', 'HealthAndBeautyBusiness', 'ProfessionalService'];
        foreach ($types as $type) {
            if (str_contains($this->html, '"@type": "'.$type) || str_contains($this->html, '"@type":"'.$type)) {
                return 'pass';
            }
        }

        return 'fail';
    }

    /** Human-readable check definitions for the UI. */
    public function checkDefinitions(): array
    {
        return [
            // Tier 1
            'has_https' => ['label' => 'Website uses HTTPS', 'tier' => 1, 'priority' => 'high', 'why' => 'Secure pages build trust and reduce the chance that AI systems surface outdated or insecure URLs.', 'fix' => 'Install or renew an SSL certificate and force the site to load over HTTPS.'],
            'site_loads' => ['label' => 'Website loads without errors', 'tier' => 1, 'priority' => 'high', 'why' => 'If the homepage does not load reliably, other signals become meaningless.', 'fix' => 'Fix hosting, DNS, or application errors so the homepage returns successfully.'],
            'has_title_tag' => ['label' => 'Homepage has a title tag', 'tier' => 1, 'priority' => 'medium', 'why' => 'Title tags help AI systems classify what the page is about.', 'fix' => 'Add a descriptive title tag that includes your business category and location.'],
            'has_meta_description' => ['label' => 'Homepage has a meta description', 'tier' => 1, 'priority' => 'medium', 'why' => 'Meta descriptions improve machine-readable context.', 'fix' => 'Write a homepage meta description that clearly states your offer, category, and location.'],
            'has_canonical_tag' => ['label' => 'Homepage has a canonical tag', 'tier' => 1, 'priority' => 'medium', 'why' => 'Canonical tags reduce duplicate URL confusion for AI indexing pipelines.', 'fix' => 'Add a self-referencing canonical tag to your homepage.'],
            'has_json_ld_schema' => ['label' => 'Homepage includes JSON-LD schema', 'tier' => 1, 'priority' => 'high', 'why' => 'Structured data helps AI understand your business as a defined entity.', 'fix' => 'Generate and publish JSON-LD markup.', 'quick_fix' => 'json_ld'],
            'has_faq_schema' => ['label' => 'FAQ schema is present', 'tier' => 1, 'priority' => 'medium', 'why' => 'FAQ schema gives AI systems ready-made question and answer context.', 'fix' => 'Generate FAQ content and publish the FAQPage schema.', 'quick_fix' => 'faq_schema'],
            'has_about_page' => ['label' => 'About page exists', 'tier' => 1, 'priority' => 'medium', 'why' => 'About pages help AI understand who your business is and why it exists.', 'fix' => 'Publish an About page with clear business identity details.', 'quick_fix' => 'about_copy'],
            'has_contact_page' => ['label' => 'Contact page exists', 'tier' => 1, 'priority' => 'medium', 'why' => 'Contact pages strengthen trust signals.', 'fix' => 'Create a contact page with your phone, email, and location.'],
            'has_contact_details_on_site' => ['label' => 'Homepage shows contact details', 'tier' => 1, 'priority' => 'medium', 'why' => 'Visible contact details confirm your business is real.', 'fix' => 'Add your phone number or email to the homepage.'],
            'mentions_city' => ['label' => 'Homepage mentions your city/country', 'tier' => 1, 'priority' => 'high', 'why' => 'Location language helps AI match your business to local-intent queries.', 'fix' => 'Add your city and market language to the homepage.'],
            'mentions_industry' => ['label' => 'Homepage mentions your industry', 'tier' => 1, 'priority' => 'high', 'why' => 'Industry language helps AI connect your site to the right recommendation category.', 'fix' => 'Add clear category language so your business type is obvious.'],
            'has_robots_txt' => ['label' => 'robots.txt is present', 'tier' => 1, 'priority' => 'medium', 'why' => 'A readable robots file helps define crawl rules.', 'fix' => 'Publish a valid robots.txt file at the root of your site.'],
            'has_xml_sitemap' => ['label' => 'XML sitemap is discoverable', 'tier' => 1, 'priority' => 'medium', 'why' => 'Sitemaps help crawlers discover your core URLs.', 'fix' => 'Generate an XML sitemap and reference it in robots.txt.'],
            'has_sameas_links' => ['label' => 'Site links to business profiles', 'tier' => 1, 'priority' => 'medium', 'why' => 'Links to profiles help AI tie your website to your business entity.', 'fix' => 'Add links to your LinkedIn, Instagram, and other official profiles.'],
            // Tier 2
            'page_indexable' => ['label' => 'Homepage appears indexable', 'tier' => 2, 'priority' => 'high', 'why' => 'Noindex directives block AI discovery even when the site looks healthy.', 'fix' => 'Remove any noindex directives from your homepage.'],
            'ai_bots_allowed' => ['label' => 'robots.txt allows AI crawlers', 'tier' => 2, 'priority' => 'high', 'why' => 'If AI bots are blocked, your visibility in answer engines will be weaker.', 'fix' => 'Review robots.txt and allow GPTBot, Google-Extended, ClaudeBot.'],
            'canonical_matches_url' => ['label' => 'Canonical matches primary URL', 'tier' => 2, 'priority' => 'medium', 'why' => 'A mismatched canonical sends crawlers to the wrong version of your site.', 'fix' => 'Align the canonical tag with your live homepage URL.'],
            'has_mobile_viewport' => ['label' => 'Mobile viewport tag exists', 'tier' => 2, 'priority' => 'medium', 'why' => 'Most discovery happens on mobile-first rendering assumptions.', 'fix' => 'Add a responsive viewport meta tag to your homepage.'],
            'has_local_business_schema' => ['label' => 'LocalBusiness schema is present', 'tier' => 2, 'priority' => 'high', 'why' => 'Local business schema gives AI stronger business identity signals.', 'fix' => 'Publish LocalBusiness schema.', 'quick_fix' => 'local_business_schema'],
            // Tier 3
            'has_google_business_profile' => ['label' => 'Google Business Profile claimed', 'tier' => 3, 'priority' => 'high', 'why' => 'Google Business Profile is the clearest local identity signal.', 'fix' => 'Claim or verify your Google Business Profile at business.google.com'],
            'nap_consistent' => ['label' => 'Name, address & phone are consistent', 'tier' => 3, 'priority' => 'high', 'why' => 'Inconsistent NAP weakens entity confidence in AI systems.', 'fix' => 'Make sure your business name, address, and phone are identical everywhere online.'],
            'has_ten_plus_reviews' => ['label' => 'At least 10 reviews online', 'tier' => 3, 'priority' => 'medium', 'why' => 'Review volume increases trust and makes your business easier to recommend.', 'fix' => 'Ask recent customers for genuine reviews on Google or your main platform.'],
            'has_three_plus_listings' => ['label' => 'Listed in 3+ trusted directories', 'tier' => 3, 'priority' => 'medium', 'why' => 'Multiple trusted listings strengthen off-site business validation.', 'fix' => 'Add or verify your business across at least 3 trusted directories.'],
            'social_profiles_linked' => ['label' => 'Social profiles linked from website', 'tier' => 3, 'priority' => 'medium', 'why' => 'Linked profiles help AI tie your website to your business entity.', 'fix' => 'Add links to LinkedIn, Instagram, and X from your homepage footer.'],
        ];
    }
}
