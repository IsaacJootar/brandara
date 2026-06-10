<?php

namespace App\Services\Analytics;

use App\Models\Brand;
use App\Models\Post;
use App\Models\PostAnalytic;

/**
 * Seeds realistic fake analytics data for development and demo.
 * Activated via: php artisan analytics:seed-fake {brand_slug}
 *
 * When real platform OAuth apps are approved, replace with
 * LiveAnalyticsFetcher that calls each platform's API.
 */
class FakeAnalyticsSeeder
{
    public function seed(Brand $brand, int $days = 30): int
    {
        $posts = Post::where('brand_id', $brand->id)
            ->where('status', 'published')
            ->latest()
            ->limit(50)
            ->get();

        if ($posts->isEmpty()) {
            return 0;
        }

        $created = 0;

        foreach ($posts as $post) {
            $platforms = array_keys($post->platform_contents ?? ['linkedin' => true]);

            foreach ($platforms as $platform) {
                $daysAgo = rand(1, $days);
                $fetchedDate = now()->subDays($daysAgo)->toDateString();

                // Skip if already exists
                if (PostAnalytic::where('post_id', $post->id)
                    ->where('platform', $platform)
                    ->where('fetched_date', $fetchedDate)
                    ->exists()) {
                    continue;
                }

                $reach = rand(200, 8000);
                $likes = (int) ($reach * (rand(2, 12) / 100));
                $comments = (int) ($likes * (rand(5, 25) / 100));
                $shares = (int) ($likes * (rand(2, 15) / 100));
                $clicks = (int) ($reach * (rand(1, 8) / 100));
                $saves = (int) ($likes * (rand(3, 10) / 100));
                $engRate = $reach > 0
                    ? round(($likes + $comments + $shares) / $reach * 100, 2)
                    : 0;

                PostAnalytic::create([
                    'post_id' => $post->id,
                    'brand_id' => $brand->id,
                    'platform' => $platform,
                    'fetched_date' => $fetchedDate,
                    'likes' => $likes,
                    'comments' => $comments,
                    'shares' => $shares,
                    'reach' => $reach,
                    'clicks' => $clicks,
                    'saves' => $saves,
                    'engagement_rate' => $engRate,
                    'source' => 'fake',
                ]);

                $created++;
            }
        }

        return $created;
    }
}
