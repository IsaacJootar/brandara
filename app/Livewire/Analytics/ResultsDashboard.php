<?php

namespace App\Livewire\Analytics;

use App\Models\Brand;
use App\Models\PostAnalytic;
use App\Services\Analytics\AnalyticsService;
use Illuminate\View\View;
use Livewire\Component;

class ResultsDashboard extends Component
{
    public string $brandId = '';

    public int $period = 30; // days

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function setPeriod(int $days): void
    {
        $this->period = $days;
    }

    public function render(): View
    {
        $brand = Brand::findOrFail($this->brandId);
        $svc = app(AnalyticsService::class);
        $hasData = PostAnalytic::where('brand_id', $this->brandId)->exists();

        if (! $hasData) {
            return view('livewire.analytics.results-dashboard', [
                'hasData' => false,
            ]);
        }

        return view('livewire.analytics.results-dashboard', [
            'hasData' => true,
            'summary' => $svc->summary($brand, $this->period),
            'wow' => $svc->weekOverWeek($brand),
            'chart' => $svc->dailyChart($brand, $this->period),
            'platformBreakdown' => $svc->platformBreakdown($brand, $this->period),
            'topPosts' => $svc->topPosts($brand, 5, $this->period),
            'bottomPosts' => $svc->bottomPosts($brand, 5, $this->period),
            'bestTimes' => $svc->bestPostingTimes($brand),
            'period' => $this->period,
        ]);
    }
}
