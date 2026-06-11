<?php

namespace App\Livewire\AiVisibility;

use App\Models\AiGeneratedAsset;
use App\Models\AiVisibilityCheck;
use App\Models\Brand;
use App\Services\AiVisibility\AiPresenceService;
use App\Services\AiVisibility\AssetGeneratorService;
use App\Services\AiVisibility\CountryDirectoryService;
use App\Services\AiVisibility\WebsiteScannerService;
use Illuminate\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public string $brandId = '';

    public string $activeTab = 'readiness';

    public string $websiteUrl = '';

    public bool $scanning = false;

    public bool $generating = false;

    public bool $querying = false;

    public string $successMessage = '';

    public string $errorMessage = '';

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
        $this->websiteUrl = $brand->website_url ?? '';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ── Section 1: Website Scan ───────────────────────────────────────────────

    public function runScan(): void
    {
        $this->validate(['websiteUrl' => 'required|url|max:255']);

        $brand = Brand::findOrFail($this->brandId);

        // Save website URL to brand
        $brand->update(['website_url' => $this->websiteUrl]);

        $this->scanning = true;

        try {
            app(WebsiteScannerService::class)->scan($brand, $this->websiteUrl);
            $this->successMessage = 'Scan complete. Your AI Readiness score has been updated.';
            $this->dispatch('show-toast', message: $this->successMessage, type: 'success');
        } catch (\Throwable $e) {
            $this->errorMessage = 'Could not scan the website. Make sure the URL is correct and the site is live.';
            $this->dispatch('show-toast', message: $this->errorMessage, type: 'error');
        }

        $this->scanning = false;
    }

    // ── Section 2: Manual check toggle ───────────────────────────────────────

    public function toggleManualCheck(string $key, bool $passed): void
    {
        $brand = Brand::findOrFail($this->brandId);
        $check = AiVisibilityCheck::where('brand_id', $brand->id)->first();

        if (! $check) {
            return;
        }

        app(WebsiteScannerService::class)->updateManual($check, $key, $passed);
        $this->dispatch('show-toast', message: 'Saved.', type: 'success');
    }

    // ── Section 4: Asset generation ───────────────────────────────────────────

    public function generateAsset(string $type): void
    {
        $brand = Brand::findOrFail($this->brandId);
        $this->generating = true;

        try {
            app(AssetGeneratorService::class)->generate($brand, $type);
            $this->dispatch('show-toast', message: 'Asset generated — copy it and paste on your website.', type: 'success');
        } catch (\Throwable $e) {
            $this->dispatch('show-toast', message: 'Could not generate the asset. Please try again.', type: 'error');
        }

        $this->generating = false;
    }

    public function markAssetPublished(string $assetId): void
    {
        $brand = Brand::findOrFail($this->brandId);
        AiGeneratedAsset::where('id', $assetId)->where('brand_id', $brand->id)
            ->update(['status' => 'published']);
        $this->dispatch('show-toast', message: 'Marked as published. Run a new scan to see the check turn green.', type: 'success');
    }

    // ── Section 5: Live AI presence query ────────────────────────────────────

    public function runPresenceQuery(string $provider = 'all'): void
    {
        $brand = Brand::findOrFail($this->brandId);
        $this->querying = true;

        try {
            $svc = app(AiPresenceService::class);
            if ($provider === 'all') {
                $svc->runAll($brand);
            } else {
                $svc->runProvider($brand, $provider);
            }
            $this->dispatch('show-toast', message: 'AI presence scan complete.', type: 'success');
        } catch (\Throwable $e) {
            $this->dispatch('show-toast', message: 'Could not complete the AI presence scan. Check your API keys.', type: 'error');
        }

        $this->querying = false;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render(): View
    {
        $brand = Brand::findOrFail($this->brandId);
        $country = $brand->workspace?->country ?? 'NG';
        $check = AiVisibilityCheck::where('brand_id', $brand->id)->first();
        $scanner = app(WebsiteScannerService::class);
        $presence = app(AiPresenceService::class);

        return view('livewire.ai-visibility.dashboard', [
            'brand' => $brand,
            'check' => $check,
            'checkDefs' => $scanner->checkDefinitions(),
            'assets' => AiGeneratedAsset::where('brand_id', $brand->id)->get()->keyBy('type'),
            'directories' => app(CountryDirectoryService::class)->forCountry($country),
            'presenceSummary' => $presence->presenceSummary($brand),
            'activeProviders' => $presence->activeProviders(),
            'activeTab' => $this->activeTab,
        ]);
    }
}
