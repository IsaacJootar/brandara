<?php

namespace App\Livewire\Dashboard;

use App\Models\Brand;
use App\Models\PostAnalytic;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class Metrics extends Component
{
    public string $brandId = '';

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function placeholder(): string
    {
        return view('livewire.dashboard.metrics-placeholder')->render();
    }

    public function render()
    {
        $brand = Brand::find($this->brandId);

        abort_if(
            ! $brand || $brand->workspace_id !== auth()->user()->workspace_id,
            403
        );

        // ── Content stats ──────────────────────────────────────────────────────
        $postsThisMonth = $brand->posts()
            ->whereMonth('created_at', now()->month)
            ->where('status', 'published')
            ->count();

        $scheduledCount = $brand->posts()
            ->where('status', 'scheduled')
            ->count();

        $draftCount = $brand->posts()
            ->where('status', 'draft')
            ->count();

        $failedCount = $brand->posts()
            ->where('status', 'failed')
            ->count();

        // ── Audience & reach ───────────────────────────────────────────────────
        $totalReach = PostAnalytic::where('brand_id', $brand->id)
            ->where('fetched_date', '>=', now()->subDays(30))
            ->sum('reach');

        $totalEngagements = PostAnalytic::where('brand_id', $brand->id)
            ->where('fetched_date', '>=', now()->subDays(30))
            ->selectRaw('SUM(likes + comments + shares) as total')
            ->value('total') ?? 0;

        // ── Leads ──────────────────────────────────────────────────────────────
        $totalLeads = $brand->leads()->count();
        $warmLeads = $brand->leads()->where('tag', 'warm_lead')->count();
        $followUpsDue = $brand->leads()
            ->whereNotNull('follow_up_at')
            ->whereDate('follow_up_at', '<=', today())
            ->count();

        // ── Connections ────────────────────────────────────────────────────────
        $activeConnections = $brand->platformConnections()
            ->where('status', 'connected')
            ->count();

        // ── Recent published posts ─────────────────────────────────────────────
        $recentPosts = $brand->posts()
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(3)
            ->get();

        // ── Upcoming scheduled posts ───────────────────────────────────────────
        $upcomingPosts = $brand->posts()
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(3)
            ->get();

        // ── Brand completion score ─────────────────────────────────────────────
        $completionFields = [
            filled($brand->tagline),
            filled($brand->description),
            filled($brand->target_audience),
            filled($brand->mission),
            filled($brand->brand_voice),
            $activeConnections > 0,
            $postsThisMonth > 0,
        ];
        $completionScore = (int) round(
            collect($completionFields)->filter()->count() / count($completionFields) * 100
        );

        // ── Campaigns ─────────────────────────────────────────────────────────
        $activeCampaigns = $brand->campaigns()
            ->where('end_date', '>=', today())
            ->count();

        // ── Pillars ───────────────────────────────────────────────────────────
        $pillarCount = $brand->contentPillars()->count();

        return view('livewire.dashboard.metrics', [
            // Stat cards
            'postsThisMonth' => $postsThisMonth,
            'scheduledCount' => $scheduledCount,
            'totalReach' => $totalReach,
            'warmLeads' => $warmLeads,
            // Content overview
            'draftCount' => $draftCount,
            'failedCount' => $failedCount,
            'totalEngagements' => $totalEngagements,
            'totalLeads' => $totalLeads,
            'followUpsDue' => $followUpsDue,
            'activeConnections' => $activeConnections,
            // Lists
            'recentPosts' => $recentPosts,
            'upcomingPosts' => $upcomingPosts,
            // Brand health
            'completionScore' => $completionScore,
            'activeCampaigns' => $activeCampaigns,
            'pillarCount' => $pillarCount,
            // Brand for links
            'brand' => $brand,
        ]);
    }
}
