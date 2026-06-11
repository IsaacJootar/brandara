<?php

namespace App\Services\AiVisibility;

use Anthropic\Anthropic;
use App\Models\AiGeneratedAsset;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;

/**
 * Generates AI-readable assets using Claude.
 * Users copy-paste these onto their website to improve AI visibility.
 *
 * When real APIs are available for Google/Bing schema validation,
 * replace the prompt layer — storage and UI stay unchanged.
 */
class AssetGeneratorService
{
    public function generate(Brand $brand, string $type): AiGeneratedAsset
    {
        $content = match ($type) {
            'json_ld' => $this->generateJsonLd($brand),
            'local_business_schema' => $this->generateLocalBusinessSchema($brand),
            'faq_schema' => $this->generateFaqSchema($brand),
            'about_copy' => $this->generateAboutCopy($brand),
            'brand_markdown' => $this->generateBrandMarkdown($brand),
            default => throw new \InvalidArgumentException("Unknown asset type: {$type}"),
        };

        return AiGeneratedAsset::updateOrCreate(
            ['brand_id' => $brand->id, 'type' => $type],
            ['content' => $content, 'status' => 'draft', 'generated_at' => now()]
        );
    }

    // ── JSON-LD Schema ────────────────────────────────────────────────────────

    private function generateJsonLd(Brand $brand): string
    {
        $prompt = $this->buildPrompt($brand,
            'Generate a valid JSON-LD schema block for this business. Use @type Organization or the most appropriate type. '.
            'Include: name, description, url (use website_url if available), sameAs (social links if mentioned), '.
            'foundingDate (omit if unknown), areaServed, contactPoint. '.
            'Return ONLY the raw JSON-LD script tag, nothing else. No explanation.'
        );

        $json = $this->callClaude($prompt);

        // Fallback: build a basic schema from brand data
        if (empty($json)) {
            $json = $this->fallbackJsonLd($brand);
        }

        return $json;
    }

    private function generateLocalBusinessSchema(Brand $brand): string
    {
        $prompt = $this->buildPrompt($brand,
            'Generate a valid JSON-LD LocalBusiness schema for this business. '.
            'Choose the most specific @type (e.g. ProfessionalService, FinancialService, HealthAndBeautyBusiness, etc.). '.
            'Include: name, description, url, address (with country from workspace), telephone if mentioned, '.
            'openingHours if mentioned, priceRange if mentioned. '.
            'Return ONLY the raw JSON-LD script tag wrapped in <script type="application/ld+json">...</script>. No explanation.'
        );

        $result = $this->callClaude($prompt);

        return $result ?: $this->fallbackLocalBusinessSchema($brand);
    }

    private function generateFaqSchema(Brand $brand): string
    {
        $prompt = $this->buildPrompt($brand,
            'Generate 5 FAQ questions and answers for this business that would help it appear in AI-generated answers. '.
            'Questions should be things real customers ask (What do you do? Where are you? How much does it cost? Who is it for? Why choose you?). '.
            'Return as a valid JSON-LD FAQPage schema wrapped in <script type="application/ld+json">...</script>. No explanation.'
        );

        $result = $this->callClaude($prompt);

        return $result ?: '<script type="application/ld+json">{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[]}</script>';
    }

    // ── Content assets ────────────────────────────────────────────────────────

    private function generateAboutCopy(Brand $brand): string
    {
        $prompt = $this->buildPrompt($brand,
            'Write a clear, professional About page for this business. '.
            'Structure: 1 opening paragraph (who we are), 1 paragraph (what we do and who we serve), '.
            '1 paragraph (why we exist / mission), 1 paragraph (why choose us). '.
            'Write in a tone that matches the brand voice. Plain English. No jargon. Max 350 words. '.
            'This will be used on the business website to help AI systems understand who the business is.'
        );

        return $this->callClaude($prompt) ?: "About {$brand->name}\n\n{$brand->description}";
    }

    private function generateBrandMarkdown(Brand $brand): string
    {
        $prompt = $this->buildPrompt($brand,
            'Generate a brand.md file for this business. This file will be placed at the root of their website '.
            'so that AI agents and crawlers can quickly understand the business. '.
            'Include sections: # About, ## What we do, ## Who we serve, ## Where we are, ## Contact, ## Social profiles. '.
            'Use Markdown format. Be concise and factual. This is not a sales page — it is a machine-readable identity file. '.
            'Return only the Markdown content, no explanation.'
        );

        return $this->callClaude($prompt) ?: "# {$brand->name}\n\n{$brand->description}";
    }

    // ── Claude caller ─────────────────────────────────────────────────────────

    private function callClaude(string $prompt): string
    {
        try {
            $client = new Anthropic(['api_key' => config('services.anthropic.key')]);

            $response = $client->messages()->create([
                'model' => config('services.anthropic.model', 'claude-sonnet-4-5'),
                'max_tokens' => 1500,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

            return trim($response->content[0]->text ?? '');
        } catch (\Throwable $e) {
            Log::warning('AssetGeneratorService: Claude call failed', ['error' => $e->getMessage()]);

            return '';
        }
    }

    private function buildPrompt(Brand $brand, string $task): string
    {
        $country = $brand->workspace?->country ?? 'NG';
        $location = match ($country) {
            'NG' => 'Nigeria',
            'GH' => 'Ghana',
            'KE' => 'Kenya',
            'ZA' => 'South Africa',
            default => 'Africa',
        };

        return "Business context:\n".
            "Name: {$brand->name}\n".
            ($brand->tagline ? "Tagline: {$brand->tagline}\n" : '').
            ($brand->description ? "Description: {$brand->description}\n" : '').
            ($brand->target_audience ? "Target audience: {$brand->target_audience}\n" : '').
            ($brand->mission ? "Mission: {$brand->mission}\n" : '').
            ($brand->website_url ? "Website: {$brand->website_url}\n" : '').
            "Location: {$location}\n\n".
            "Task: {$task}";
    }

    // ── Fallbacks (no API key) ────────────────────────────────────────────────

    private function fallbackJsonLd(Brand $brand): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $brand->name,
            'description' => $brand->description ?? $brand->tagline ?? '',
            'url' => $brand->website_url ?? '',
        ];

        return '<script type="application/ld+json">'.json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</script>';
    }

    private function fallbackLocalBusinessSchema(Brand $brand): string
    {
        $country = $brand->workspace?->country ?? 'NG';
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $brand->name,
            'description' => $brand->description ?? $brand->tagline ?? '',
            'url' => $brand->website_url ?? '',
            'address' => ['@type' => 'PostalAddress', 'addressCountry' => $country],
        ];

        return '<script type="application/ld+json">'.json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</script>';
    }
}
