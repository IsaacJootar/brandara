<?php

namespace App\Livewire\Trends;

use App\Models\Brand;
use App\Services\Trends\TrendsService;
use Illuminate\View\View;
use Livewire\Component;

class TrendsDashboard extends Component
{
    public string $brandId = '';

    public string $activeTab = 'industry'; // industry | format | competitor

    public string $newKeyword = '';

    public string $keywordPlatform = 'all';

    public string $successMessage = '';

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function addKeyword(): void
    {
        $this->validate(['newKeyword' => 'required|string|min:2|max:60']);

        $brand = Brand::findOrFail($this->brandId);
        app(TrendsService::class)->addKeyword($brand, $this->newKeyword, $this->keywordPlatform);

        $this->newKeyword = '';
        $this->successMessage = 'Keyword added. It will appear in your next trend scan.';
        $this->dispatch('show-toast', message: $this->successMessage, type: 'success');
    }

    public function removeKeyword(string $keywordId): void
    {
        $brand = Brand::findOrFail($this->brandId);
        app(TrendsService::class)->removeKeyword($brand, $keywordId);
    }

    public function render(): View
    {
        $brand = Brand::findOrFail($this->brandId);
        $svc = app(TrendsService::class);
        $hasData = $svc->hasData($brand);

        if (! $hasData) {
            return view('livewire.trends.trends-dashboard', [
                'hasData' => false,
                'brandSlug' => $brand->slug,
            ]);
        }

        return view('livewire.trends.trends-dashboard', [
            'hasData' => true,
            'summary' => $svc->summary($brand),
            'industryTrends' => $svc->industryTrends($brand),
            'contentFormats' => $svc->contentFormats($brand),
            'competitorSignals' => $svc->competitorSignals($brand),
            'trackedKeywords' => $svc->trackedKeywords($brand),
            'activeTab' => $this->activeTab,
        ]);
    }
}
