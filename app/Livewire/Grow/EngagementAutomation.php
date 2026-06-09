<?php

namespace App\Livewire\Grow;

use App\Models\Brand;
use App\Models\EngagementAction;
use App\Models\EngagementRule;
use App\Services\Engagement\EngagementService;
use Illuminate\View\View;
use Livewire\Component;

class EngagementAutomation extends Component
{
    public string $brandId = '';

    // ── Rule form ─────────────────────────────────────────────────────────────
    public string $ruleType = 'auto_like';

    public string $platform = 'linkedin';

    public string $accountsRaw = '';   // comma-separated handles

    public string $keywordsRaw = '';   // comma-separated keywords

    public string $industry = '';

    public int $dailyLimit = 20;

    public bool $requireReview = true;

    public string $commentTone = 'professional';

    public bool $showForm = false;

    public string $formError = '';

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function openForm(): void
    {
        $this->showForm = true;
        $this->formError = '';
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function saveRule(): void
    {
        $this->validate([
            'ruleType' => ['required', 'in:auto_like,auto_comment'],
            'platform' => ['required', 'in:linkedin,twitter,instagram,facebook,threads'],
            'dailyLimit' => ['required', 'integer', 'min:1', 'max:200'],
        ], [
            'dailyLimit.max' => 'Daily limit cannot exceed 200 — stay within platform guidelines.',
        ]);

        if (empty(trim($this->accountsRaw)) && empty(trim($this->keywordsRaw))) {
            $this->formError = 'Add at least one account handle or keyword to target.';

            return;
        }

        $accounts = $this->parseCsv($this->accountsRaw);
        $keywords = $this->parseCsv($this->keywordsRaw);

        EngagementRule::create([
            'brand_id' => $this->brandId,
            'type' => $this->ruleType,
            'platform' => $this->platform,
            'target_accounts' => $accounts,
            'target_keywords' => $keywords,
            'target_industry' => trim($this->industry) ?: null,
            'daily_limit' => $this->dailyLimit,
            'require_review' => $this->ruleType === 'auto_comment' ? $this->requireReview : false,
            'comment_tone' => $this->ruleType === 'auto_comment' ? $this->commentTone : null,
            'is_active' => true,
        ]);

        $this->closeForm();
        $this->dispatch('show-toast', message: 'Rule saved. It will run on the next engagement scan.');
    }

    public function toggleRule(string $id): void
    {
        $rule = EngagementRule::where('id', $id)
            ->where('brand_id', $this->brandId)
            ->firstOrFail();

        $rule->update(['is_active' => ! $rule->is_active]);
    }

    public function deleteRule(string $id): void
    {
        EngagementRule::where('id', $id)
            ->where('brand_id', $this->brandId)
            ->firstOrFail()
            ->delete();

        $this->dispatch('show-toast', message: 'Rule deleted.');
    }

    public function approveComment(string $actionId): void
    {
        $action = EngagementAction::where('id', $actionId)
            ->where('brand_id', $this->brandId)
            ->firstOrFail();

        app(EngagementService::class)->approveComment($action);
        $this->dispatch('show-toast', message: 'Comment approved and sent.');
    }

    public function skipAction(string $actionId): void
    {
        $action = EngagementAction::where('id', $actionId)
            ->where('brand_id', $this->brandId)
            ->firstOrFail();

        app(EngagementService::class)->skipAction($action);
    }

    public function render(): View
    {
        $brand = Brand::findOrFail($this->brandId);

        $rules = EngagementRule::where('brand_id', $this->brandId)
            ->latest()
            ->get();

        $pendingComments = EngagementAction::where('brand_id', $this->brandId)
            ->where('type', 'comment')
            ->where('status', 'pending')
            ->with('rule')
            ->latest()
            ->get();

        $recentActions = EngagementAction::where('brand_id', $this->brandId)
            ->whereIn('status', ['posted', 'failed'])
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.grow.engagement-automation', compact(
            'rules', 'pendingComments', 'recentActions'
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->ruleType = 'auto_like';
        $this->platform = 'linkedin';
        $this->accountsRaw = '';
        $this->keywordsRaw = '';
        $this->industry = '';
        $this->dailyLimit = 20;
        $this->requireReview = true;
        $this->commentTone = 'professional';
        $this->formError = '';
    }

    /** @return string[] */
    private function parseCsv(string $raw): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', $raw)),
            fn ($v) => $v !== ''
        ));
    }
}
