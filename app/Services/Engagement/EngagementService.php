<?php

namespace App\Services\Engagement;

use App\Models\Brand;
use App\Models\EngagementAction;
use App\Models\EngagementRule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * Core engagement orchestration service.
 *
 * Processes engagement rules, creates action records,
 * generates AI comments, and dispatches via publisher.
 *
 * Live publishing is behind config('services.engagement.live').
 * When false (default), FakeEngagementPublisher is used.
 */
class EngagementService
{
    public function __construct(
        private readonly CommentGeneratorService $commentGenerator,
        private readonly FakeEngagementPublisher $publisher,
    ) {}

    /**
     * Process a single engagement rule — check limits, create action, dispatch.
     *
     * @param  array{post_id: string, account: string, excerpt: string}  $targetPost
     */
    public function processRule(EngagementRule $rule, array $targetPost): EngagementAction
    {
        if (! $rule->is_active) {
            throw new \RuntimeException('Rule is not active.');
        }

        if ($rule->isDailyLimitReached()) {
            throw new \RuntimeException("Daily limit of {$rule->daily_limit} actions reached for this rule.");
        }

        $brand = $rule->brand;

        if ($rule->type === 'auto_like') {
            return $this->processLike($rule, $brand, $targetPost);
        }

        return $this->processComment($rule, $brand, $targetPost);
    }

    /**
     * Approve a pending comment and dispatch it.
     */
    public function approveComment(EngagementAction $action): void
    {
        if ($action->status !== 'pending') {
            throw new \RuntimeException('Only pending actions can be approved.');
        }

        $action->update(['status' => 'approved']);

        $this->dispatch($action);
    }

    /**
     * Skip (dismiss) a pending action.
     */
    public function skipAction(EngagementAction $action): void
    {
        $action->update(['status' => 'skipped']);
    }

    /**
     * Get pending review queue for a brand.
     *
     * @return Collection
     */
    public function pendingQueue(Brand $brand)
    {
        return EngagementAction::where('brand_id', $brand->id)
            ->where('status', 'pending')
            ->with('rule')
            ->latest()
            ->get();
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function processLike(EngagementRule $rule, Brand $brand, array $targetPost): EngagementAction
    {
        $action = EngagementAction::create([
            'brand_id' => $brand->id,
            'rule_id' => $rule->id,
            'type' => 'like',
            'platform' => $rule->platform,
            'target_post_id' => $targetPost['post_id'],
            'target_account' => $targetPost['account'],
            'target_post_excerpt' => Str::limit($targetPost['excerpt'], 280),
            'status' => 'approved', // likes never need review
        ]);

        $this->dispatch($action);
        $rule->incrementDailyActions();

        return $action;
    }

    private function processComment(EngagementRule $rule, Brand $brand, array $targetPost): EngagementAction
    {
        // Generate contextual comment via AI
        $commentBody = $this->commentGenerator->generate(
            $brand,
            $rule,
            $targetPost['excerpt'],
            $targetPost['account'],
        );

        $status = $rule->require_review ? 'pending' : 'approved';

        $action = EngagementAction::create([
            'brand_id' => $brand->id,
            'rule_id' => $rule->id,
            'type' => 'comment',
            'platform' => $rule->platform,
            'target_post_id' => $targetPost['post_id'],
            'target_account' => $targetPost['account'],
            'target_post_excerpt' => Str::limit($targetPost['excerpt'], 280),
            'comment_body' => $commentBody,
            'status' => $status,
        ]);

        if (! $rule->require_review) {
            $this->dispatch($action);
        }

        $rule->incrementDailyActions();

        return $action;
    }

    private function dispatch(EngagementAction $action): void
    {
        $live = config('services.engagement.live', false);

        $success = match (true) {
            $live && $action->type === 'like' => false, // real publisher goes here
            $live && $action->type === 'comment' => false, // real publisher goes here
            $action->type === 'like' => $this->publisher->like($action),
            default => $this->publisher->comment($action),
        };

        if ($success) {
            $action->update(['status' => 'posted', 'posted_at' => now()]);
        } else {
            $action->update(['status' => 'failed', 'failure_reason' => 'Publisher returned false.']);
        }
    }
}
