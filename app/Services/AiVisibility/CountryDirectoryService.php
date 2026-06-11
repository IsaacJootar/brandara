<?php

namespace App\Services\AiVisibility;

/**
 * Country-aware directory and publication guide.
 * Publishing on these platforms increases the chance AI systems discover and index the brand.
 */
class CountryDirectoryService
{
    public function forCountry(string $country): array
    {
        return match ($country) {
            'NG' => $this->nigeria(),
            'GH' => $this->ghana(),
            'KE' => $this->kenya(),
            'ZA' => $this->southAfrica(),
            'GB' => $this->uk(),
            default => $this->global(),
        };
    }

    private function nigeria(): array
    {
        return [
            [
                'category' => 'Business Directories',
                'why' => 'AI systems index these directories when answering "best businesses in Nigeria".',
                'items' => [
                    ['name' => 'VConnect Nigeria', 'url' => 'https://www.vconnect.com', 'effort' => 'low'],
                    ['name' => 'BusinessList Nigeria', 'url' => 'https://nigeria.businesslist.com.ng', 'effort' => 'low'],
                    ['name' => 'Nigeria Galleria', 'url' => 'https://www.nigeriagalleria.com', 'effort' => 'low'],
                    ['name' => 'Kompass Nigeria', 'url' => 'https://ng.kompass.com', 'effort' => 'medium'],
                ],
            ],
            [
                'category' => 'Media & Publications',
                'why' => 'Articles and mentions on these sites are heavily indexed by ChatGPT, Gemini, and Perplexity.',
                'items' => [
                    ['name' => 'Techpoint Africa', 'url' => 'https://techpoint.africa', 'effort' => 'high'],
                    ['name' => 'BusinessDay Nigeria', 'url' => 'https://businessday.ng', 'effort' => 'high'],
                    ['name' => 'Nairametrics', 'url' => 'https://nairametrics.com', 'effort' => 'medium'],
                    ['name' => 'The Cable', 'url' => 'https://www.thecable.ng', 'effort' => 'high'],
                    ['name' => 'Vanguard Nigeria', 'url' => 'https://www.vanguardngr.com', 'effort' => 'high'],
                ],
            ],
            [
                'category' => 'Professional Platforms',
                'why' => 'LinkedIn articles and thought leadership posts from Nigeria are indexed and cited by AI systems.',
                'items' => [
                    ['name' => 'LinkedIn Articles', 'url' => 'https://linkedin.com', 'effort' => 'low'],
                    ['name' => 'Medium', 'url' => 'https://medium.com', 'effort' => 'low'],
                    ['name' => 'Substack', 'url' => 'https://substack.com', 'effort' => 'low'],
                ],
            ],
        ];
    }

    private function ghana(): array
    {
        return [
            ['category' => 'Business Directories', 'why' => 'AI systems index these when answering questions about Ghana businesses.', 'items' => [
                ['name' => 'Ghana Business Directory', 'url' => 'https://www.ghanayello.com', 'effort' => 'low'],
                ['name' => 'BusinessList Ghana', 'url' => 'https://ghana.businesslist.com.gh', 'effort' => 'low'],
            ]],
            ['category' => 'Media & Publications', 'why' => 'Mentions on Ghanaian media are indexed by AI answer engines.', 'items' => [
                ['name' => 'MyJoyOnline', 'url' => 'https://www.myjoyonline.com', 'effort' => 'high'],
                ['name' => 'GhanaWeb', 'url' => 'https://www.ghanaweb.com', 'effort' => 'high'],
                ['name' => 'Graphic Online', 'url' => 'https://www.graphic.com.gh', 'effort' => 'high'],
                ['name' => 'Techgh24', 'url' => 'https://techgh24.com', 'effort' => 'medium'],
            ]],
            ['category' => 'Professional Platforms', 'why' => 'Global platforms indexed by AI.', 'items' => [
                ['name' => 'LinkedIn Articles', 'url' => 'https://linkedin.com', 'effort' => 'low'],
                ['name' => 'Medium', 'url' => 'https://medium.com', 'effort' => 'low'],
            ]],
        ];
    }

    private function kenya(): array
    {
        return [
            ['category' => 'Business Directories', 'why' => 'Indexed by AI when answering about Kenyan businesses.', 'items' => [
                ['name' => 'Mocality Kenya', 'url' => 'https://www.mocality.co.ke', 'effort' => 'low'],
                ['name' => 'Kenya Yellow Pages', 'url' => 'https://www.yellowpages.co.ke', 'effort' => 'low'],
            ]],
            ['category' => 'Media & Publications', 'why' => 'Kenyan media heavily indexed by AI systems.', 'items' => [
                ['name' => 'Nation Africa', 'url' => 'https://nation.africa', 'effort' => 'high'],
                ['name' => 'Business Daily Africa', 'url' => 'https://www.businessdailyafrica.com', 'effort' => 'high'],
                ['name' => 'TechTrendsKE', 'url' => 'https://techtrendskenya.com', 'effort' => 'medium'],
                ['name' => 'Capital FM Kenya', 'url' => 'https://www.capitalfm.co.ke', 'effort' => 'high'],
            ]],
            ['category' => 'Professional Platforms', 'why' => 'Global platforms indexed by AI.', 'items' => [
                ['name' => 'LinkedIn Articles', 'url' => 'https://linkedin.com', 'effort' => 'low'],
                ['name' => 'Medium', 'url' => 'https://medium.com', 'effort' => 'low'],
            ]],
        ];
    }

    private function southAfrica(): array
    {
        return [
            ['category' => 'Business Directories', 'why' => 'Indexed by AI for South Africa business queries.', 'items' => [
                ['name' => 'Yellow Pages SA', 'url' => 'https://www.yellowpages.co.za', 'effort' => 'low'],
                ['name' => 'Brabys', 'url' => 'https://www.brabys.com', 'effort' => 'low'],
            ]],
            ['category' => 'Media & Publications', 'why' => 'SA media indexed by AI answer engines.', 'items' => [
                ['name' => 'Business Insider SA', 'url' => 'https://www.businessinsider.co.za', 'effort' => 'high'],
                ['name' => 'Bizcommunity', 'url' => 'https://www.bizcommunity.com', 'effort' => 'medium'],
                ['name' => 'Daily Maverick', 'url' => 'https://dailymaverick.co.za', 'effort' => 'high'],
                ['name' => 'ITWeb', 'url' => 'https://www.itweb.co.za', 'effort' => 'medium'],
            ]],
            ['category' => 'Professional Platforms', 'why' => 'Global platforms indexed by AI.', 'items' => [
                ['name' => 'LinkedIn Articles', 'url' => 'https://linkedin.com', 'effort' => 'low'],
                ['name' => 'Medium', 'url' => 'https://medium.com', 'effort' => 'low'],
            ]],
        ];
    }

    private function uk(): array
    {
        return [
            ['category' => 'Business Directories', 'why' => 'UK directories indexed by AI for local business queries.', 'items' => [
                ['name' => 'Yell.com', 'url' => 'https://www.yell.com', 'effort' => 'low'],
                ['name' => 'Thomson Local', 'url' => 'https://www.thomsonlocal.com', 'effort' => 'low'],
                ['name' => 'Scoot', 'url' => 'https://www.scoot.co.uk', 'effort' => 'low'],
            ]],
            ['category' => 'Media & Publications', 'why' => 'UK media heavily indexed by all AI systems.', 'items' => [
                ['name' => 'The Guardian', 'url' => 'https://www.theguardian.com', 'effort' => 'high'],
                ['name' => 'Forbes UK', 'url' => 'https://www.forbes.com', 'effort' => 'high'],
                ['name' => 'Medium', 'url' => 'https://medium.com', 'effort' => 'low'],
            ]],
            ['category' => 'Professional Platforms', 'why' => 'Global platforms indexed by AI.', 'items' => [
                ['name' => 'LinkedIn Articles', 'url' => 'https://linkedin.com', 'effort' => 'low'],
                ['name' => 'Substack', 'url' => 'https://substack.com', 'effort' => 'low'],
            ]],
        ];
    }

    private function global(): array
    {
        return [
            ['category' => 'Professional Platforms', 'why' => 'These global platforms are indexed by all major AI systems.', 'items' => [
                ['name' => 'LinkedIn Articles', 'url' => 'https://linkedin.com', 'effort' => 'low'],
                ['name' => 'Medium', 'url' => 'https://medium.com', 'effort' => 'low'],
                ['name' => 'Substack', 'url' => 'https://substack.com', 'effort' => 'low'],
                ['name' => 'Quora', 'url' => 'https://www.quora.com', 'effort' => 'low'],
            ]],
            ['category' => 'Business Directories', 'why' => 'Universal directories indexed globally.', 'items' => [
                ['name' => 'Crunchbase', 'url' => 'https://www.crunchbase.com', 'effort' => 'low'],
                ['name' => 'Google Business Profile', 'url' => 'https://business.google.com', 'effort' => 'low'],
            ]],
        ];
    }
}
