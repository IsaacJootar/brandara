<?php

namespace App\Livewire\Create;

use App\Models\Brand;
use App\Models\ContentPillar;
use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class PillarAlert extends Component
{
    public string $brandId = '';

    public bool $dismissed = false;

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function dismiss(): void
    {
        $this->dismissed = true;
    }

    /**
     * Returns pillars that have not been used in the last 14 days,
     * where the brand has at least one published post (so new brands
     * don't see false alerts).
     *
     * @return Collection<int, array{pillar: ContentPillar, days: int}>
     */
    public function neglectedPillars(): Collection
    {
        $brand = Brand::findOrFail($this->brandId);

        $hasAnyPosts = Post::where('brand_id', $this->brandId)
            ->whereIn('status', ['published', 'scheduled', 'draft'])
            ->exists();

        if (! $hasAnyPosts) {
            return collect();
        }

        $pillars = ContentPillar::where('brand_id', $this->brandId)
            ->where('is_active', true)
            ->get();

        return $pillars
            ->map(function (ContentPillar $pillar) {
                $lastPost = Post::where('brand_id', $this->brandId)
                    ->where('content_pillar_id', $pillar->id)
                    ->whereIn('status', ['published', 'scheduled'])
                    ->latest('created_at')
                    ->first();

                $daysSince = $lastPost
                    ? (int) now()->diffInDays($lastPost->created_at)
                    : 999;

                return ['pillar' => $pillar, 'days' => $daysSince];
            })
            ->filter(fn ($item) => $item['days'] >= 14)
            ->sortByDesc('days')
            ->values();
    }

    public function render(): View
    {
        if ($this->dismissed) {
            return view('livewire.create.pillar-alert', ['neglected' => collect()]);
        }

        return view('livewire.create.pillar-alert', [
            'neglected' => $this->neglectedPillars(),
        ]);
    }
}
