<?php

namespace App\Livewire\Create;

use App\Models\Brand;
use App\Services\Ai\AiProviderException;
use App\Services\TikTok\TikTokService;
use Illuminate\View\View;
use Livewire\Component;

class TikTokToolkit extends Component
{
    public string $brandId = '';

    public string $topic = '';

    public string $tone = 'founder';

    /** idle | generating | done | error */
    public string $status = 'idle';

    public string $errorMessage = '';

    /** @var array<string, mixed> */
    public array $result = [];

    public array $tones = [
        'founder' => 'Founder voice',
        'african' => 'African business',
        'friendly' => 'Friendly & energetic',
        'bold' => 'Bold & direct',
        'educational' => 'Educational',
        'professional' => 'Professional',
    ];

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function setTone(string $tone): void
    {
        if (array_key_exists($tone, $this->tones)) {
            $this->tone = $tone;
        }
    }

    public function generate(): void
    {
        $this->errorMessage = '';

        $this->validate([
            'topic' => ['required', 'string', 'min:5', 'max:500'],
            'tone' => ['required', 'string'],
        ]);

        $this->status = 'generating';

        try {
            $brand = Brand::findOrFail($this->brandId);
            $this->result = app(TikTokService::class)->generate($brand, $this->topic, $this->tone);
            $this->status = 'done';
        } catch (AiProviderException $e) {
            $this->errorMessage = $e->isConfigError
                ? 'AI is not configured yet. Add your API key to get started.'
                : 'Something went wrong generating your content. Please try again.';
            $this->status = 'error';
        } catch (\Throwable) {
            $this->errorMessage = 'Something went wrong. Please try again in a moment.';
            $this->status = 'error';
        }
    }

    public function startOver(): void
    {
        $this->topic = '';
        $this->status = 'idle';
        $this->result = [];
        $this->errorMessage = '';
    }

    public function render(): View
    {
        return view('livewire.create.tiktok-toolkit', [
            'brand' => Brand::findOrFail($this->brandId),
        ]);
    }
}
