<?php

namespace App\Livewire\Create;

use App\Models\Brand;
use App\Models\Post;
use App\Services\Ai\AiProviderException;
use App\Services\Ai\ContentGenerationService;
use Livewire\Component;

class VariationPicker extends Component
{
    public string $brandId = '';

    // Input from composer
    public string $inputType = 'topic';

    public string $input = '';

    public array $platforms = ['linkedin'];

    public string $tone = 'professional';

    // State
    public string $status = 'idle';   // idle | generating | done | error

    public string $errorMessage = '';

    public ?string $selectedVariation = null;  // authority | story | bold

    public string $previewPlatform = 'linkedin';

    public array $variations = [];   // parsed from AI

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
        $this->previewPlatform = $this->platforms[0] ?? 'linkedin';
    }

    private function brand(): Brand
    {
        $brand = Brand::find($this->brandId);
        abort_if(! $brand || $brand->workspace_id !== auth()->user()->workspace_id, 403);

        return $brand;
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function generate(): void
    {
        if (empty(trim($this->input))) {
            $this->errorMessage = 'Please enter something to generate from.';
            $this->status = 'error';

            return;
        }

        $this->status = 'generating';
        $this->errorMessage = '';
        $this->variations = [];
        $this->selectedVariation = null;

        try {
            $brand = $this->brand();
            $service = app(ContentGenerationService::class);

            $this->variations = $service->generate(
                brand: $brand,
                inputType: $this->inputType,
                input: $this->input,
                platforms: $this->platforms,
                tone: $this->tone,
            );

            $this->status = 'done';
            $this->selectedVariation = 'authority'; // default selection
        } catch (AiProviderException $e) {
            $this->status = 'error';
            $this->errorMessage = $e->isConfigError
                ? 'AI is not configured yet. Add your API key to get started.'
                : $e->getMessage();
        } catch (\Throwable $e) {
            $this->status = 'error';
            $this->errorMessage = 'Something went wrong. Please try again.';
        }
    }

    public function selectVariation(string $variation): void
    {
        if (isset($this->variations[$variation])) {
            $this->selectedVariation = $variation;
        }
    }

    public function setPreviewPlatform(string $platform): void
    {
        if (in_array($platform, $this->platforms)) {
            $this->previewPlatform = $platform;
        }
    }

    public function useVariation(): void
    {
        if (! $this->selectedVariation || empty($this->variations)) {
            return;
        }

        $variation = $this->variations[$this->selectedVariation];
        $brand = $this->brand();

        // Build platform_contents from ALL platforms
        $platformContents = [];
        foreach ($this->platforms as $platform) {
            $content = $variation['platforms'][$platform] ?? ['body' => '', 'hashtags' => []];
            $body = $content['body'] ?? '';
            $hashtags = $content['hashtags'] ?? [];
            $platformContents[$platform] = [
                'body' => $body,
                'hashtags' => $hashtags,
            ];
        }

        // Build the primary body (linkedin or first platform)
        $primaryPlatform = in_array('linkedin', $this->platforms) ? 'linkedin' : ($this->platforms[0] ?? 'linkedin');
        $primaryContent = $platformContents[$primaryPlatform] ?? [];
        $primaryBody = trim(($primaryContent['body'] ?? '').' '.implode(' ', array_map(fn ($h) => "#{$h}", $primaryContent['hashtags'] ?? [])));

        $post = Post::create([
            'brand_id' => $brand->id,
            'created_by' => auth()->id(),
            'input_type' => $this->inputType,
            'raw_input' => $this->input,
            'ai_generated' => true,
            'variation_selected' => $this->selectedVariation,
            'platform_contents' => $platformContents,
            'tone' => $this->tone,
            'status' => 'draft',
        ]);

        // Tell the composer to load this post
        $this->dispatch('variation-selected', postId: $post->id, body: $primaryBody);
        $this->reset(['status', 'variations', 'selectedVariation', 'errorMessage']);
        $this->status = 'idle';
    }

    public function reset_generator(): void
    {
        $this->status = 'idle';
        $this->variations = [];
        $this->selectedVariation = null;
        $this->errorMessage = '';
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.create.variation-picker');
    }
}
