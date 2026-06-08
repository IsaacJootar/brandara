<?php

namespace App\Livewire\MyBrand;

use App\Models\Brand;
use Illuminate\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class BrandProfile extends Component
{
    public string $brandId = '';

    public string $vision = '';

    public string $mission = '';

    public string $negativeBrief = '';

    public string $positioning = '';

    /** @var array<int, array{title: string, description: string}> */
    public array $values = [];

    public string $saveStatus = '';

    public function mount(): void
    {
        $brand = $this->brand();

        $this->brandId = $brand->id;
        $this->vision = $brand->vision ?? '';
        $this->mission = $brand->mission ?? '';
        $this->negativeBrief = $brand->negative_brief ?? '';
        $this->positioning = $brand->positioning ?? '';
        $this->values = $brand->values ?? [
            ['title' => '', 'description' => ''],
        ];

        if (empty($this->values)) {
            $this->values = [['title' => '', 'description' => '']];
        }
    }

    public function addValue(): void
    {
        if (count($this->values) >= 5) {
            return;
        }

        $this->values[] = ['title' => '', 'description' => ''];
    }

    public function removeValue(int $index): void
    {
        if (count($this->values) <= 1) {
            return;
        }

        array_splice($this->values, $index, 1);
        $this->values = array_values($this->values);
    }

    public function save(): void
    {
        $this->validate([
            'vision' => ['nullable', 'string', 'max:500'],
            'mission' => ['nullable', 'string', 'max:500'],
            'negativeBrief' => ['nullable', 'string', 'max:1000'],
            'positioning' => ['nullable', 'string', 'max:500'],
            'values' => ['nullable', 'array', 'max:5'],
            'values.*.title' => ['nullable', 'string', 'max:80'],
            'values.*.description' => ['nullable', 'string', 'max:300'],
        ]);

        $cleanValues = array_values(array_filter(
            $this->values,
            fn ($v) => ! empty(trim($v['title'] ?? ''))
        ));

        $this->brand()->update([
            'vision' => $this->vision,
            'mission' => $this->mission,
            'negative_brief' => $this->negativeBrief,
            'positioning' => $this->positioning,
            'values' => $cleanValues ?: null,
        ]);

        $this->saveStatus = 'saved';
        $this->dispatch('brand-profile-saved');
    }

    private function brand(): Brand
    {
        if ($this->brandId) {
            return Brand::findOrFail($this->brandId);
        }

        return currentBrand();
    }

    public function placeholder(): View
    {
        return view('livewire.my-brand.form-skeleton');
    }

    public function render(): View
    {
        return view('livewire.my-brand.brand-profile');
    }
}
