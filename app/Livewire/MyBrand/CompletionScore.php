<?php

namespace App\Livewire\MyBrand;

use App\Models\Brand;
use Illuminate\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class CompletionScore extends Component
{
    public string $brandId = '';

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    /**
     * @return array<string, array{label: string, done: bool, tab: string}>
     */
    public function fields(): array
    {
        $brand = Brand::findOrFail($this->brandId);

        return [
            'name' => ['label' => 'Brand name', 'done' => ! empty($brand->name), 'tab' => 'kit'],
            'tagline' => ['label' => 'Tagline', 'done' => ! empty($brand->tagline), 'tab' => 'kit'],
            'description' => ['label' => 'What your business does', 'done' => ! empty($brand->description), 'tab' => 'kit'],
            'target_audience' => ['label' => 'Target audience', 'done' => ! empty($brand->target_audience), 'tab' => 'kit'],
            'primary_color' => ['label' => 'Brand colours', 'done' => ! empty($brand->primary_color), 'tab' => 'kit'],
            'vision' => ['label' => 'Vision', 'done' => ! empty($brand->vision), 'tab' => 'profile'],
            'mission' => ['label' => 'Mission', 'done' => ! empty($brand->mission), 'tab' => 'profile'],
            'values' => ['label' => 'Brand values', 'done' => ! empty($brand->values), 'tab' => 'profile'],
            'negative_brief' => ['label' => 'Negative brief', 'done' => ! empty($brand->negative_brief), 'tab' => 'profile'],
            'positioning' => ['label' => 'Positioning', 'done' => ! empty($brand->positioning), 'tab' => 'profile'],
            'brand_voice' => ['label' => 'Brand Voice profile', 'done' => ! empty($brand->brand_voice), 'tab' => 'voice'],
        ];
    }

    public function percentage(): int
    {
        $fields = $this->fields();
        $done = count(array_filter($fields, fn ($f) => $f['done']));

        return (int) round(($done / count($fields)) * 100);
    }

    #[On('brand-kit-saved')]
    #[On('brand-profile-saved')]
    public function refresh(): void
    {
        // Re-renders with fresh brand data from DB
    }

    public function placeholder(): View
    {
        return view('livewire.my-brand.score-skeleton');
    }

    public function render(): View
    {
        return view('livewire.my-brand.completion-score', [
            'fields' => $this->fields(),
            'percentage' => $this->percentage(),
        ]);
    }
}
