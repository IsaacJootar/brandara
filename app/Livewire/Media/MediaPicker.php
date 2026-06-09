<?php

namespace App\Livewire\Media;

use App\Models\Brand;
use App\Models\MediaFile;
use App\Services\Media\MediaLibraryService;
use Illuminate\View\View;
use Livewire\Component;

class MediaPicker extends Component
{
    public string $brandId = '';

    public string $search = '';

    public array $selected = [];

    public bool $open = false;

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function openPicker(): void
    {
        $this->open = true;
        $this->selected = [];
    }

    public function closePicker(): void
    {
        $this->open = false;
        $this->selected = [];
    }

    public function toggleSelect(string $id): void
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_values(array_filter($this->selected, fn ($s) => $s !== $id));
        } else {
            $this->selected[] = $id;
        }
    }

    public function confirmSelection(): void
    {
        $service = app(MediaLibraryService::class);

        $files = MediaFile::whereIn('id', $this->selected)
            ->where('brand_id', $this->brandId)
            ->get()
            ->map(fn ($f) => [
                'id' => $f->id,
                'url' => $service->url($f),
                'name' => $f->filename,
                'mime' => $f->mime_type,
            ])
            ->values()
            ->all();

        $this->dispatch('media-selected', files: $files);
        $this->closePicker();
    }

    public function render(): View
    {
        $files = MediaFile::where('brand_id', $this->brandId)
            ->when($this->search, fn ($q) => $q->where('filename', 'like', '%'.$this->search.'%'))
            ->latest()
            ->limit(60)
            ->get();

        return view('livewire.media.media-picker', [
            'files' => $files,
            'service' => app(MediaLibraryService::class),
        ]);
    }
}
