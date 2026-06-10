<?php

namespace App\Services\Analytics;

use App\Models\Brand;
use App\Models\PostAnalytic;
use Illuminate\Support\Collection;

class AnalyticsService
{
    /**
     * Summary stats for the given period.
     *
     * @return array{total_reach: int, total_engagements: int, avg_engagement_rate: float, total_posts: int}
     */
    public function summary(Brand $brand, int $days = 30): array
    {
        $from = now()->subDays($days)->startOfDay();

        $stats = PostAnalytic::where('brand_id', $brand->id)
            ->where('fetched_date', '>=', $from)
            ->selectRaw('
                SUM(reach) as total_reach,
                SUM(likes + comments + shares) as total_engagements,
                AVG(engagement_rate) as avg_engagement_rate,
                COUNT(DISTINCT post_id) as total_posts
            ')
            ->first();

        return [
            'total_reach' => (int) ($stats->total_reach ?? 0),
            'total_engagements' => (int) ($stats->total_engagements ?? 0),
            'avg_engagement_rate' => round((float) ($stats->avg_engagement_rate ?? 0), 2),
            'total_posts' => (int) ($stats->total_posts ?? 0),
        ];
    }

    /**
     * Daily reach + engagements for the chart — last N days.
     *
     * @return array{labels: string[], reach: int[], engagements: int[]}
     */
    public function dailyChart(Brand $brand, int $days = 30): array
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $rows = PostAnalytic::where('brand_id', $brand->id)
            ->where('fetched_date', '>=', $from)
            ->selectRaw('fetched_date, SUM(reach) as reach, SUM(likes + comments + shares) as engagements')
            ->groupBy('fetched_date')
            ->orderBy('fetched_date')
            ->get()
            ->keyBy(fn ($r) => $r->fetched_date->format('Y-m-d'));

        $labels = [];
        $reach = [];
        $engagements = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $row = $rows->get($date);
            $reach[] = $row ? (int) $row->reach : 0;
            $engagements[] = $row ? (int) $row->engagements : 0;
        }

        return compact('labels', 'reach', 'engagements');
    }

    /**
     * Platform breakdown — total engagements per platform.
     *
     * @return Collection<string, int>
     */
    public function platformBreakdown(Brand $brand, int $days = 30): Collection
    {
        $from = now()->subDays($days)->startOfDay();

        return PostAnalytic::where('brand_id', $brand->id)
            ->where('fetched_date', '>=', $from)
            ->selectRaw('platform, SUM(likes + comments + shares) as total')
            ->groupBy('platform')
            ->orderByDesc('total')
            ->pluck('total', 'platform');
    }

    /**
     * Top N posts by total engagements.
     */
    public function topPosts(Brand $brand, int $limit = 5, int $days = 30): Collection
    {
        $from = now()->subDays($days)->startOfDay();

        return PostAnalytic::where('post_analytics.brand_id', $brand->id)
            ->where('fetched_date', '>=', $from)
            ->join('posts', 'posts.id', '=', 'post_analytics.post_id')
            ->selectRaw('post_analytics.post_id, posts.raw_input, post_analytics.platform,
                SUM(likes + comments + shares) as total_engagements,
                SUM(reach) as total_reach,
                AVG(engagement_rate) as avg_rate')
            ->groupBy('post_analytics.post_id', 'posts.raw_input', 'post_analytics.platform')
            ->orderByDesc('total_engagements')
            ->limit($limit)
            ->get();
    }

    /**
     * Best posting hours based on historical engagement.
     *
     * @return array<int, array{hour: int, label: string, avg_engagements: float}>
     */
    public function bestPostingTimes(Brand $brand): array
    {
        // Join with posts to get the hour they were published
        $rows = PostAnalytic::where('post_analytics.brand_id', $brand->id)
            ->join('posts', 'posts.id', '=', 'post_analytics.post_id')
            ->whereNotNull('posts.scheduled_at')
            ->selectRaw("strftime('%H', posts.scheduled_at) as hour,
                AVG(likes + comments + shares) as avg_engagements")
            ->groupByRaw("strftime('%H', posts.scheduled_at)")
            ->orderByDesc('avg_engagements')
            ->limit(5)
            ->get();

        return $rows->map(fn ($r) => [
            'hour' => (int) $r->hour,
            'label' => date('g A', mktime((int) $r->hour, 0, 0)),
            'avg_engagements' => round((float) $r->avg_engagements, 1),
        ])->all();
    }

    /**
     * Week-over-week change for summary stats.
     *
     * @return array{reach_change: float, engagement_change: float}
     */
    public function weekOverWeek(Brand $brand): array
    {
        $thisWeek = $this->summary($brand, 7);
        $lastWeek = PostAnalytic::where('brand_id', $brand->id)
            ->whereBetween('fetched_date', [now()->subDays(14), now()->subDays(7)])
            ->selectRaw('SUM(reach) as total_reach, SUM(likes + comments + shares) as total_engagements')
            ->first();

        $prevReach = (int) ($lastWeek->total_reach ?? 1);
        $prevEngage = (int) ($lastWeek->total_engagements ?? 1);

        return [
            'reach_change' => $prevReach > 0
                ? round(($thisWeek['total_reach'] - $prevReach) / $prevReach * 100, 1)
                : 0,
            'engagement_change' => $prevEngage > 0
                ? round(($thisWeek['total_engagements'] - $prevEngage) / $prevEngage * 100, 1)
                : 0,
        ];
    }
}
