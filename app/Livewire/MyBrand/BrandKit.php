<?php

namespace App\Livewire\MyBrand;

use App\Models\Brand;
use Illuminate\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class BrandKit extends Component
{
    public string $brandId = '';

    public string $name = '';

    public string $tagline = '';

    public string $description = '';

    public string $targetAudience = '';

    public string $primaryColor = '#7C3AED';

    public string $secondaryColor = '#4338CA';

    public string $fontPreference = '';

    public string $saveStatus = '';

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
        $this->name = $brand->name ?? '';
        $this->tagline = $brand->tagline ?? '';
        $this->description = $brand->description ?? '';
        $this->targetAudience = $brand->target_audience ?? '';
        $this->primaryColor = $brand->primary_color ?? '#7C3AED';
        $this->secondaryColor = $brand->secondary_color ?? '#4338CA';
        $this->fontPreference = $brand->font_preference ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'targetAudience' => ['nullable', 'string', 'max:500'],
            'primaryColor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondaryColor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'fontPreference' => ['nullable', 'string', 'max:80'],
        ]);

        Brand::findOrFail($this->brandId)->update([
            'name' => $this->name,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'target_audience' => $this->targetAudience,
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
            'font_preference' => $this->fontPreference,
        ]);

        $this->saveStatus = 'saved';
        $this->dispatch('brand-kit-saved');
    }

    public function placeholder(): View
    {
        return view('livewire.my-brand.form-skeleton');
    }

    public function render(): View
    {
        return view('livewire.my-brand.brand-kit');
    }
}
