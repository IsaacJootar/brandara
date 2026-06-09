<?php

namespace App\Livewire\Media;

use App\Models\Brand;
use App\Models\MediaFile;
use App\Services\Media\MediaLibraryService;
use App\Services\Media\MediaUploadException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MediaLibrary extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $brandId = '';

    /** @var TemporaryUploadedFile[] */
    public array $uploads = [];

    public string $search = '';

    /** idle | uploading | done | error */
    public string $uploadStatus = 'idle';

    public string $uploadError = '';

    /** IDs of files selected (used when component is embedded as picker) */
    public array $selected = [];

    /** Whether this instance is used as a picker modal (true) or standalone page (false) */
    public bool $pickerMode = false;

    public function mount(Brand $brand, bool $pickerMode = false): void
    {
        $this->brandId = $brand->id;
        $this->pickerMode = $pickerMode;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function upload(): void
    {
        $this->validate([
            'uploads' => ['required', 'array', 'min:1'],
            'uploads.*' => ['file', 'max:20480', 'mimes:jpg,jpeg,png,gif,webp,mp4'],
        ], [
            'uploads.required' => 'Please select at least one file.',
            'uploads.*.max' => 'Each file must be under 20 MB.',
            'uploads.*.mimes' => 'Only JPG, PNG, GIF, WEBP, or MP4 files are allowed.',
        ]);

        $this->uploadStatus = 'uploading';
        $this->uploadError = '';

        try {
            $brand = Brand::findOrFail($this->brandId);
            $service = app(MediaLibraryService::class);

            foreach ($this->uploads as $file) {
                $service->store($file, $brand, auth()->id());
            }

            $this->uploads = [];
            $this->uploadStatus = 'done';
            $this->resetPage();
        } catch (MediaUploadException $e) {
            $this->uploadError = $e->getMessage();
            $this->uploadStatus = 'error';
        } catch (\Throwable) {
            $this->uploadError = 'Something went wrong uploading your file. Please try again.';
            $this->uploadStatus = 'error';
        }
    }

    public function deleteFile(string $id): void
    {
        $file = MediaFile::where('id', $id)
            ->where('brand_id', $this->brandId)
            ->firstOrFail();

        app(MediaLibraryService::class)->delete($file);
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
        $files = MediaFile::whereIn('id', $this->selected)
            ->where('brand_id', $this->brandId)
            ->get()
            ->map(fn ($f) => [
                'id' => $f->id,
                'url' => app(MediaLibraryService::class)->url($f),
                'name' => $f->filename,
                'mime' => $f->mime_type,
            ])
            ->values()
            ->all();

        $this->dispatch('media-selected', files: $files);
        $this->selected = [];
    }

    public function render(): View
    {
        $query = MediaFile::where('brand_id', $this->brandId)
            ->when($this->search, fn ($q) => $q->where('filename', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(24);

        $storageUsedKb = app(MediaLibraryService::class)->storageUsedKb(Brand::findOrFail($this->brandId));

        return view('livewire.media.media-library', [
            'files' => $query,
            'storageUsedKb' => $storageUsedKb,
            'service' => app(MediaLibraryService::class),
        ]);
    }
}
