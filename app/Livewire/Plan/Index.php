<?php

namespace App\Livewire\Plan;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\ContentPillar;
use App\Models\Post;
use App\Services\Ai\AiProviderException;
use App\Services\CampaignPack\CampaignPackService;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class Index extends Component
{
    public string $brandId = '';

    public string $tab = 'overview'; // overview | pillars | campaigns

    public int $campaignPage = 1;

    public int $campaignPerPage = 8;

    // ── Pillar form ───────────────────────────────────────────────────────────
    public bool $showPillarForm = false;

    public ?string $editingPillarId = null;

    public string $pillarName = '';

    public string $pillarGoal = 'authority';

    public string $pillarColor = '#7C3AED';

    // ── Pack generation ───────────────────────────────────────────────────────
    public ?string $activatingPackKey = null;   // which pack the user is configuring

    public string $packKeyMessage = '';

    public string $packStartDate = '';

    public array $packPlatforms = ['linkedin'];

    /** idle | generating | done | error */
    public string $packStatus = 'idle';

    public string $packError = '';

    public ?string $generatedCampaignId = null;

    public int $generatedPostCount = 0;

    // ── Campaign form ─────────────────────────────────────────────────────────
    public bool $showCampaignForm = false;

    public ?string $editingCampaignId = null;

    public string $campaignName = '';

    public string $campaignGoal = '';

    public string $campaignKeyMessage = '';

    public string $campaignStartDate = '';

    public string $campaignEndDate = '';

    public array $campaignPlatforms = ['linkedin'];

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
        $this->campaignStartDate = now()->format('Y-m-d');
        $this->campaignEndDate = now()->addDays(14)->format('Y-m-d');
        $this->packStartDate = now()->format('Y-m-d');
    }

    public function placeholder(): string
    {
        return view('livewire.plan.placeholder')->render();
    }

    private function brand(): Brand
    {
        $brand = Brand::find($this->brandId);
        abort_if(! $brand || $brand->workspace_id !== auth()->user()->workspace_id, 403);

        return $brand;
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function pillars()
    {
        return ContentPillar::where('brand_id', $this->brandId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function pillarBalance(): array
    {
        $total = Post::where('brand_id', $this->brandId)
            ->whereNotNull('content_pillar_id')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return ContentPillar::where('brand_id', $this->brandId)
            ->where('is_active', true)
            ->get()
            ->map(function ($pillar) use ($total) {
                $count = Post::where('brand_id', $this->brandId)
                    ->where('content_pillar_id', $pillar->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count();

                $lastPost = Post::where('brand_id', $this->brandId)
                    ->where('content_pillar_id', $pillar->id)
                    ->latest('created_at')
                    ->value('created_at');

                return [
                    'pillar' => $pillar,
                    'count' => $count,
                    'pct' => $total > 0 ? round(($count / $total) * 100) : 0,
                    'days_since' => $lastPost ? now()->diffInDays($lastPost) : null,
                    // Only mark stale if the brand has at least one post AND this pillar has gone 14+ days without one.
                    // A brand-new pillar with zero posts ever is NOT overdue — it just hasn't been used yet.
                    'stale' => $lastPost ? now()->diffInDays($lastPost) >= 14 : ($total > 0),
                ];
            })
            ->toArray();
    }

    #[Computed]
    public function campaigns()
    {
        return Campaign::where('brand_id', $this->brandId)
            ->where('status', '!=', 'archived')
            ->orderByDesc('created_at')
            ->paginate($this->campaignPerPage, ['*'], 'campaign_page', $this->campaignPage);
    }

    #[Computed]
    public function campaignTotal(): int
    {
        return Campaign::where('brand_id', $this->brandId)
            ->where('status', '!=', 'archived')
            ->count();
    }

    public function campaignNextPage(): void
    {
        $maxPage = (int) ceil($this->campaignTotal() / $this->campaignPerPage);
        if ($this->campaignPage < $maxPage) {
            $this->campaignPage++;
        }
    }

    public function campaignPrevPage(): void
    {
        if ($this->campaignPage > 1) {
            $this->campaignPage--;
        }
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['overview', 'pillars', 'campaigns']) ? $tab : 'overview';
        $this->showPillarForm = false;
        $this->showCampaignForm = false;
        $this->campaignPage = 1;
    }

    // ── Pillar actions ────────────────────────────────────────────────────────

    public function openPillarForm(?string $pillarId = null): void
    {
        $this->resetPillarForm();

        if ($pillarId) {
            $pillar = ContentPillar::where('brand_id', $this->brandId)->find($pillarId);
            abort_if(! $pillar, 403);
            $this->editingPillarId = $pillar->id;
            $this->pillarName = $pillar->name;
            $this->pillarGoal = $pillar->goal;
            $this->pillarColor = $pillar->color;
        }

        $this->showPillarForm = true;
    }

    public function savePillar(): void
    {
        $this->validate([
            'pillarName' => ['required', 'string', 'max:60'],
            'pillarGoal' => ['required', 'in:authority,trust,awareness,conversion'],
            'pillarColor' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $existing = ContentPillar::where('brand_id', $this->brandId)->where('is_active', true)->count();

        if (! $this->editingPillarId && $existing >= 5) {
            $this->addError('pillarName', 'You can have a maximum of 5 content pillars.');

            return;
        }

        ContentPillar::updateOrCreate(
            ['id' => $this->editingPillarId ?? Str::uuid()->toString()],
            [
                'brand_id' => $this->brandId,
                'name' => $this->pillarName,
                'goal' => $this->pillarGoal,
                'color' => $this->pillarColor,
                'sort_order' => $this->editingPillarId
                    ? ContentPillar::find($this->editingPillarId)?->sort_order ?? 0
                    : ($existing + 1),
            ]
        );

        $this->resetPillarForm();
        session()->flash('plan_message', 'Pillar saved.');
    }

    public function deletePillar(string $pillarId): void
    {
        $pillar = ContentPillar::where('brand_id', $this->brandId)->find($pillarId);
        abort_if(! $pillar, 403);
        $pillar->update(['is_active' => false]);
        session()->flash('plan_message', 'Pillar removed.');
    }

    private function resetPillarForm(): void
    {
        $this->showPillarForm = false;
        $this->editingPillarId = null;
        $this->pillarName = '';
        $this->pillarGoal = 'authority';
        $this->pillarColor = '#7C3AED';
        $this->resetErrorBag();
    }

    // ── Campaign actions ──────────────────────────────────────────────────────

    public function openCampaignForm(?string $campaignId = null): void
    {
        $this->resetCampaignForm();

        if ($campaignId) {
            $campaign = Campaign::where('brand_id', $this->brandId)->find($campaignId);
            abort_if(! $campaign, 403);
            $this->editingCampaignId = $campaign->id;
            $this->campaignName = $campaign->name;
            $this->campaignGoal = $campaign->goal ?? '';
            $this->campaignKeyMessage = $campaign->key_message ?? '';
            $this->campaignStartDate = $campaign->start_date?->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->campaignEndDate = $campaign->end_date?->format('Y-m-d') ?? now()->addDays(14)->format('Y-m-d');
            $this->campaignPlatforms = $campaign->platforms ?? ['linkedin'];
        }

        $this->showCampaignForm = true;
    }

    public function saveCampaign(): void
    {
        $this->validate([
            'campaignName' => ['required', 'string', 'max:100'],
            'campaignGoal' => ['required', 'string', 'max:300'],
            'campaignKeyMessage' => ['required', 'string', 'max:500'],
            'campaignStartDate' => ['required', 'date'],
            'campaignEndDate' => ['required', 'date', 'after_or_equal:campaignStartDate'],
            'campaignPlatforms' => ['required', 'array', 'min:1'],
        ]);

        Campaign::updateOrCreate(
            ['id' => $this->editingCampaignId ?? Str::uuid()->toString()],
            [
                'brand_id' => $this->brandId,
                'name' => $this->campaignName,
                'type' => 'custom',
                'goal' => $this->campaignGoal,
                'key_message' => $this->campaignKeyMessage,
                'start_date' => $this->campaignStartDate,
                'end_date' => $this->campaignEndDate,
                'platforms' => $this->campaignPlatforms,
                'status' => 'draft',
            ]
        );

        $this->resetCampaignForm();
        $this->campaignPage = 1;
        session()->flash('plan_message', 'Campaign saved.');
    }

    public function archiveCampaign(string $campaignId): void
    {
        $campaign = Campaign::where('brand_id', $this->brandId)->find($campaignId);
        abort_if(! $campaign, 403);
        $campaign->update(['status' => 'archived']);
        session()->flash('plan_message', 'Campaign archived.');
    }

    public function toggleCampaignPlatform(string $platform): void
    {
        if (in_array($platform, $this->campaignPlatforms)) {
            if (count($this->campaignPlatforms) > 1) {
                $this->campaignPlatforms = array_values(
                    array_filter($this->campaignPlatforms, fn ($p) => $p !== $platform)
                );
            }
        } else {
            $this->campaignPlatforms[] = $platform;
        }
    }

    private function resetCampaignForm(): void
    {
        $this->showCampaignForm = false;
        $this->editingCampaignId = null;
        $this->campaignName = '';
        $this->campaignGoal = '';
        $this->campaignKeyMessage = '';
        $this->campaignStartDate = now()->format('Y-m-d');
        $this->campaignEndDate = now()->addDays(14)->format('Y-m-d');
        $this->campaignPlatforms = ['linkedin'];
        $this->resetErrorBag();
    }

    // ── Pack actions ──────────────────────────────────────────────────────────

    /**
     * Open pack generation modal pre-filled from an existing campaign (no pack type).
     * Uses the 'thought_leadership' pack as a generic template.
     */
    public function openPackFormForCampaign(string $campaignId): void
    {
        $campaign = Campaign::where('brand_id', $this->brandId)->find($campaignId);
        abort_if(! $campaign, 403);

        $packKey = $campaign->pack_key ?? 'thought_leadership';
        $pack = config("campaign-packs.{$packKey}") ?? config('campaign-packs.thought_leadership');

        $this->activatingPackKey = $packKey;
        $this->packKeyMessage = $campaign->key_message ?? '';
        $this->packStartDate = $campaign->start_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->packPlatforms = $campaign->platforms ?? ['linkedin'];
        $this->packStatus = 'idle';
        $this->packError = '';
        $this->generatedCampaignId = $campaignId;
        $this->generatedPostCount = 0;
    }

    public function openPackForm(string $packKey): void
    {
        $pack = config("campaign-packs.{$packKey}");
        abort_if(! $pack, 404);

        $this->activatingPackKey = $packKey;
        $this->packKeyMessage = '';
        $this->packStartDate = now()->format('Y-m-d');
        $this->packPlatforms = ['linkedin'];
        $this->packStatus = 'idle';
        $this->packError = '';
        $this->generatedCampaignId = null;
        $this->generatedPostCount = 0;
    }

    public function closePackForm(): void
    {
        $this->activatingPackKey = null;
        $this->packStatus = 'idle';
    }

    public function togglePackPlatform(string $platform): void
    {
        if (in_array($platform, $this->packPlatforms)) {
            if (count($this->packPlatforms) > 1) {
                $this->packPlatforms = array_values(
                    array_filter($this->packPlatforms, fn ($p) => $p !== $platform)
                );
            }
        } else {
            $this->packPlatforms[] = $platform;
        }
    }

    public function generatePack(): void
    {
        $this->validate([
            'packKeyMessage' => ['required', 'string', 'min:10', 'max:500'],
            'packStartDate' => ['required', 'date'],
            'packPlatforms' => ['required', 'array', 'min:1'],
        ]);

        $pack = config("campaign-packs.{$this->activatingPackKey}");
        abort_if(! $pack, 404);

        $brand = $this->brand();
        $durationDays = $pack['duration_days'] ?? 5;

        $campaign = Campaign::create([
            'brand_id' => $brand->id,
            'name' => $pack['name'],
            'type' => 'pack',
            'pack_key' => $this->activatingPackKey,
            'goal' => $pack['default_goal'],
            'key_message' => $this->packKeyMessage,
            'start_date' => $this->packStartDate,
            'end_date' => now()->parse($this->packStartDate)->addDays($durationDays - 1)->format('Y-m-d'),
            'platforms' => $this->packPlatforms,
            'tone' => $pack['default_tone'],
            'status' => 'draft',
        ]);

        $this->packStatus = 'generating';

        try {
            $service = app(CampaignPackService::class);
            $campaign = $service->generate($campaign, $brand, $pack);
            $this->generatedCampaignId = $campaign->id;
            $this->generatedPostCount = $campaign->posts()->count();
            $this->packStatus = 'done';
            $this->campaignPage = 1;
        } catch (AiProviderException $e) {
            $campaign->delete();
            $this->packError = $e->isConfigError
                ? 'AI is not configured yet. Ask your administrator to add the API key.'
                : 'Something went wrong generating the campaign. Please try again.';
            $this->packStatus = 'error';
        } catch (\Throwable) {
            $campaign->delete();
            $this->packError = 'Something went wrong. Please try again in a moment.';
            $this->packStatus = 'error';
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $campaigns = $this->campaigns();

        $activePack = $this->activatingPackKey
            ? config("campaign-packs.{$this->activatingPackKey}")
            : null;

        return view('livewire.plan.index', [
            'pillars' => $this->pillars(),
            'pillarBalance' => $this->pillarBalance(),
            'campaigns' => $campaigns,
            'campaignTotalPages' => (int) ceil($this->campaignTotal() / $this->campaignPerPage),
            'allPacks' => config('campaign-packs', []),
            'activePack' => $activePack,
        ]);
    }
}
