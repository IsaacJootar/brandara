<?php

namespace App\Livewire\Dashboard;

use App\Models\Brand;
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

        return view('livewire.dashboard.metrics', [
            'postsThisMonth' => $brand->posts()
                ->whereMonth('published_at', now()->month)
                ->where('status', 'published')
                ->count(),
            'activeConnections' => $brand->platformConnections()
                ->where('status', 'connected')
                ->count(),
            'warmLeads' => $brand->leads()
                ->where('tag', 'warm_lead')
                ->count(),
        ]);
    }
}
