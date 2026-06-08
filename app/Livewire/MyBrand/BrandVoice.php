<?php

namespace App\Livewire\MyBrand;

use App\Models\Brand;
use App\Services\Ai\AiProviderException;
use App\Services\BrandVoice\BrandVoiceService;
use Illuminate\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class BrandVoice extends Component
{
    public string $brandId = '';

    public string $samples = '';

    /** idle | training | trained | error */
    public string $status = 'idle';

    public string $errorMessage = '';

    /** @var array<string, mixed> */
    public array $profile = [];

    public function mount(): void
    {
        $brand = $this->brand();

        $this->brandId = $brand->id;

        if ($brand->brand_voice) {
            $this->profile = $brand->brand_voice;
            $this->status = 'trained';
        }
    }

    public function train(): void
    {
        $this->errorMessage = '';

        $trimmed = trim($this->samples);

        if (strlen($trimmed) < 100) {
            $this->errorMessage = 'Please paste at least a few posts — the more you add, the better the profile.';
            $this->status = 'error';

            return;
        }

        $this->status = 'training';

        try {
            $service = app(BrandVoiceService::class);
            $this->profile = $service->train($this->brand(), $trimmed);
            $this->status = 'trained';
            $this->samples = '';
        } catch (AiProviderException $e) {
            $this->errorMessage = $e->isConfigError
                ? 'AI is not configured yet. Ask your administrator to add the API key.'
                : 'Something went wrong generating your voice profile. Please try again.';
            $this->status = 'error';
        } catch (\Throwable) {
            $this->errorMessage = 'Something went wrong. Please try again in a moment.';
            $this->status = 'error';
        }
    }

    public function retrain(): void
    {
        $this->status = 'idle';
        $this->samples = '';
        $this->errorMessage = '';
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
        return view('livewire.my-brand.brand-voice-skeleton');
    }

    public function render(): View
    {
        return view('livewire.my-brand.brand-voice', [
            'brand' => $this->brand(),
        ]);
    }
}
