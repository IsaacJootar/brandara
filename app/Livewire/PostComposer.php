<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Post;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PostComposer extends Component
{
    // ── Injected properties ───────────────────────────────────────────────────

    public string $brandId = '';   // passed from blade, never exposed as form field

    // ── State ─────────────────────────────────────────────────────────────────

    public string $body = '';

    public string $inputType = 'manual';

    public string $tone = 'professional';

    public array $platforms = ['linkedin'];

    public ?string $savedDraftId = null;

    public string $saveStatus = '';

    public array $charLimits = [
        'linkedin' => 3000,
        'twitter' => 280,
        'facebook' => 63206,
        'instagram' => 2200,
        'threads' => 500,
        'whatsapp' => 4096,
        'tiktok' => 2200,
    ];

    public array $platformNames = [
        'linkedin' => 'LinkedIn',
        'twitter' => 'X',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'threads' => 'Threads',
        'whatsapp' => 'WhatsApp',
        'tiktok' => 'TikTok',
    ];

    public array $tones = [
        'professional' => 'Professional',
        'founder' => 'Founder voice',
        'african' => 'African business',
        'friendly' => 'Friendly',
        'bold' => 'Bold & direct',
        'educational' => 'Educational',
        'corporate' => 'Corporate',
        'luxury' => 'Premium / Luxury',
    ];

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;

        // Default to connected platforms for this brand
        $connected = $brand->platformConnections()
            ->where('status', 'connected')
            ->pluck('platform')
            ->toArray();

        if (! empty($connected)) {
            $this->platforms = $connected;
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function brand(): Brand
    {
        $brand = Brand::find($this->brandId);

        // Verify the brand belongs to the authenticated user's workspace
        abort_if(
            ! $brand || $brand->workspace_id !== auth()->user()->workspace_id,
            403
        );

        return $brand;
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function setTone(string $tone): void
    {
        if (array_key_exists($tone, $this->tones)) {
            $this->tone = $tone;
        }
    }

    public function setInputType(string $type): void
    {
        $allowed = ['manual', 'topic', 'transcript', 'product'];
        if (in_array($type, $allowed)) {
            $this->inputType = $type;
        }
    }

    public function togglePlatform(string $platform): void
    {
        if (in_array($platform, $this->platforms)) {
            if (count($this->platforms) > 1) {
                $this->platforms = array_values(
                    array_filter($this->platforms, fn ($p) => $p !== $platform)
                );
            }
        } else {
            $this->platforms[] = $platform;
        }
    }

    public function saveDraft(): void
    {
        $this->validate([
            'body' => ['required', 'string', 'min:1', 'max:63206'],
            'platforms' => ['required', 'array', 'min:1'],
            'tone' => ['required', 'string'],
        ]);

        $brand = $this->brand();

        $this->saveStatus = 'saving';

        $platformContents = [];
        foreach ($this->platforms as $platform) {
            $platformContents[$platform] = ['body' => $this->body];
        }

        $id = $this->savedDraftId ?? Str::uuid()->toString();
        $post = Post::updateOrCreate(
            ['id' => $id],
            [
                'brand_id' => $brand->id,
                'created_by' => auth()->id(),
                'input_type' => 'manual',
                'raw_input' => $this->body,
                'ai_generated' => false,
                'platform_contents' => $platformContents,
                'tone' => $this->tone,
                'status' => 'draft',
            ]
        );

        $this->savedDraftId = $post->id;
        $this->saveStatus = 'saved';
    }

    public function clearComposer(): void
    {
        $this->body = '';
        $this->savedDraftId = null;
        $this->saveStatus = '';
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function charCount(): int
    {
        return strlen($this->body);
    }

    #[Computed]
    public function overLimitPlatforms(): array
    {
        $over = [];
        foreach ($this->platforms as $platform) {
            if (strlen($this->body) > ($this->charLimits[$platform] ?? 63206)) {
                $over[] = $this->platformNames[$platform];
            }
        }

        return $over;
    }

    #[Computed]
    public function tightestLimit(): int
    {
        if (empty($this->platforms)) {
            return 63206;
        }

        return min(array_map(fn ($p) => $this->charLimits[$p] ?? 63206, $this->platforms));
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.post-composer', [
            'charCount' => $this->charCount(),
            'tightestLimit' => $this->tightestLimit(),
            'overLimitPlatforms' => $this->overLimitPlatforms(),
        ]);
    }
}
