<?php

namespace App\Services\Trends;

use App\Models\Brand;
use App\Models\TrackedKeyword;
use App\Models\TrendSignal;
use Illuminate\Support\Collection;

/**
 * Reads trend signals from the trend_signals table.
 *
 * Data source today: FakeTrendsSeeder (dev/demo).
 * When real APIs are approved, swap in LiveTrendsFetcher which writes to the
 * same table with source='api'. Nothing here changes.
 */
class TrendsService
{
    /**
     * Summary counts for the stat cards.
     *
     * @return array{industry_count: int, format_count: int, competitor_count: int, hot_platform: string}
     */
    public function summary(Brand $brand): array
    {
        $industry = TrendSignal::where('brand_id', $brand->id)
            ->where('category', 'industry')->count();

        $format = TrendSignal::where('brand_id', $brand->id)
            ->where('category', 'format')->count();

        $competitor = TrendSignal::where('brand_id', $brand->id)
            ->where('category', 'competitor')->count();

        $hotPlatform = TrendSignal::where('brand_id', $brand->id)
            ->where('strength', '>=', 70)
            ->where('platform', '!=', 'all')
            ->selectRaw('platform, COUNT(*) as cnt')
            ->groupBy('platform')
            ->orderByDesc('cnt')
            ->value('platform') ?? 'LinkedIn';

        return [
            'industry_count' => $industry,
            'format_count' => $format,
            'competitor_count' => $competitor,
            'hot_platform' => ucfirst($hotPlatform),
        ];
    }

    /** Top trending topics in the brand's niche. */
    public function industryTrends(Brand $brand, int $limit = 10): Collection
    {
        return TrendSignal::where('brand_id', $brand->id)
            ->where('category', 'industry')
            ->orderByDesc('strength')
            ->limit($limit)
            ->get();
    }

    /** Content formats performing well per platform. */
    public function contentFormats(Brand $brand, int $limit = 10): Collection
    {
        return TrendSignal::where('brand_id', $brand->id)
            ->where('category', 'format')
            ->orderByDesc('strength')
            ->limit($limit)
            ->get();
    }

    /** Activity around tracked keywords and competitor names. */
    public function competitorSignals(Brand $brand, int $limit = 10): Collection
    {
        return TrendSignal::where('brand_id', $brand->id)
            ->where('category', 'competitor')
            ->orderByDesc('strength')
            ->limit($limit)
            ->get();
    }

    /** Tracked keywords for this brand. */
    public function trackedKeywords(Brand $brand): Collection
    {
        return TrackedKeyword::where('brand_id', $brand->id)
            ->orderBy('created_at')
            ->get();
    }

    /** Add a keyword to track. */
    public function addKeyword(Brand $brand, string $keyword, string $platform = 'all'): TrackedKeyword
    {
        return TrackedKeyword::firstOrCreate([
            'brand_id' => $brand->id,
            'keyword' => strtolower(trim($keyword)),
            'platform' => $platform,
        ]);
    }

    /** Remove a tracked keyword. */
    public function removeKeyword(Brand $brand, string $keywordId): void
    {
        TrackedKeyword::where('brand_id', $brand->id)
            ->where('id', $keywordId)
            ->delete();
    }

    /** Has any data been seeded for this brand? */
    public function hasData(Brand $brand): bool
    {
        return TrendSignal::where('brand_id', $brand->id)->exists();
    }
}
