<?php

namespace App\Livewire\Schedule;

use App\Models\Brand;
use App\Models\Post;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class Index extends Component
{
    public string $brandId = '';

    public string $view = 'queue';      // queue | calendar

    public string $tab = 'scheduled';   // scheduled | drafts | published | failed

    public ?string $cursorMonth = null; // YYYY-MM for calendar nav

    // Schedule modal state
    public ?string $schedulingPostId = null;

    public string $scheduleDate = '';

    public string $scheduleTime = '09:00';

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
        $this->cursorMonth = now($brand->workspace->timezone ?? config('app.timezone'))->format('Y-m');
        $this->scheduleDate = now()->addDay()->format('Y-m-d');
    }

    public function placeholder(): string
    {
        return view('livewire.schedule.placeholder')->render();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function brand(): Brand
    {
        $brand = Brand::find($this->brandId);

        abort_if(
            ! $brand || $brand->workspace_id !== auth()->user()->workspace_id,
            403
        );

        return $brand;
    }

    private function brandTimezone(): string
    {
        return $this->brand()->workspace->timezone ?? config('app.timezone');
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function counts(): array
    {
        $base = Post::where('brand_id', $this->brandId);

        return [
            'drafts' => (clone $base)->where('status', 'draft')->count(),
            'scheduled' => (clone $base)->where('status', 'scheduled')->count(),
            'published' => (clone $base)->where('status', 'published')->count(),
            'failed' => (clone $base)->where('status', 'failed')->count(),
        ];
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function setView(string $view): void
    {
        $this->view = in_array($view, ['queue', 'calendar']) ? $view : 'queue';
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['scheduled', 'drafts', 'published', 'failed']) ? $tab : 'scheduled';
    }

    public function previousMonth(): void
    {
        $this->cursorMonth = Carbon::createFromFormat('Y-m', $this->cursorMonth)
            ->subMonth()
            ->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->cursorMonth = Carbon::createFromFormat('Y-m', $this->cursorMonth)
            ->addMonth()
            ->format('Y-m');
    }

    public function openSchedule(string $postId): void
    {
        $post = Post::where('brand_id', $this->brandId)->find($postId);
        abort_if(! $post, 403);

        $this->schedulingPostId = $post->id;
        $this->scheduleDate = $post->scheduled_at
            ? $post->scheduled_at->setTimezone($this->brandTimezone())->format('Y-m-d')
            : now()->addDay()->format('Y-m-d');
        $this->scheduleTime = $post->scheduled_at
            ? $post->scheduled_at->setTimezone($this->brandTimezone())->format('H:i')
            : '09:00';
    }

    public function closeSchedule(): void
    {
        $this->schedulingPostId = null;
    }

    public function confirmSchedule(): void
    {
        $this->validate([
            'scheduleDate' => ['required', 'date'],
            'scheduleTime' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
        ]);

        $post = Post::where('brand_id', $this->brandId)->find($this->schedulingPostId);
        abort_if(! $post, 403);

        $when = Carbon::createFromFormat(
            'Y-m-d H:i',
            $this->scheduleDate.' '.$this->scheduleTime,
            $this->brandTimezone()
        )->utc();

        if ($when->isPast()) {
            $this->addError('scheduleDate', 'Pick a future time.');

            return;
        }

        $post->update([
            'status' => 'scheduled',
            'scheduled_at' => $when,
            'failure_reason' => null,
        ]);

        $this->schedulingPostId = null;
        $this->tab = 'scheduled';
        $this->dispatch('show-toast', message: 'Post scheduled.');
    }

    public function cancelSchedule(string $postId): void
    {
        $post = Post::where('brand_id', $this->brandId)
            ->where('status', 'scheduled')
            ->find($postId);
        abort_if(! $post, 403);

        $post->update([
            'status' => 'draft',
            'scheduled_at' => null,
        ]);

        $this->dispatch('show-toast', message: 'Schedule cancelled — moved back to drafts.');
    }

    public function deletePost(string $postId): void
    {
        $post = Post::where('brand_id', $this->brandId)->find($postId);
        abort_if(! $post, 403);

        $post->delete();
        $this->dispatch('show-toast', message: 'Post deleted.');
    }

    public function retryFailed(string $postId): void
    {
        $post = Post::where('brand_id', $this->brandId)
            ->where('status', 'failed')
            ->find($postId);
        abort_if(! $post, 403);

        $post->update([
            'status' => 'scheduled',
            'scheduled_at' => now()->addMinute(),
            'failure_reason' => null,
            'retry_count' => 0,
        ]);

        $this->dispatch('show-toast', message: 'Post re-queued for delivery.');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $brand = $this->brand();
        $tz = $this->brandTimezone();

        $posts = Post::where('brand_id', $this->brandId)
            ->when($this->tab === 'scheduled', fn ($q) => $q->where('status', 'scheduled')->orderBy('scheduled_at'))
            ->when($this->tab === 'drafts', fn ($q) => $q->where('status', 'draft')->orderByDesc('updated_at'))
            ->when($this->tab === 'published', fn ($q) => $q->where('status', 'published')->orderByDesc('published_at'))
            ->when($this->tab === 'failed', fn ($q) => $q->where('status', 'failed')->orderByDesc('updated_at'))
            ->limit(100)
            ->get();

        $calendar = $this->view === 'calendar' ? $this->buildCalendar($tz) : null;

        return view('livewire.schedule.index', [
            'brand' => $brand,
            'posts' => $posts,
            'calendar' => $calendar,
            'brandTimezone' => $tz,
        ]);
    }

    private function buildCalendar(string $tz): array
    {
        $cursor = Carbon::createFromFormat('Y-m', $this->cursorMonth, $tz)->startOfMonth();
        $monthStart = $cursor->copy();
        $monthEnd = $cursor->copy()->endOfMonth();

        $gridStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);

        $scheduled = Post::where('brand_id', $this->brandId)
            ->whereIn('status', ['scheduled', 'published', 'failed'])
            ->where(function ($q) use ($gridStart, $gridEnd) {
                $q->whereBetween('scheduled_at', [$gridStart->copy()->utc(), $gridEnd->copy()->utc()])
                    ->orWhereBetween('published_at', [$gridStart->copy()->utc(), $gridEnd->copy()->utc()]);
            })
            ->with('contentPillar')
            ->get()
            ->groupBy(function ($post) use ($tz) {
                $when = $post->scheduled_at ?? $post->published_at;

                return $when?->setTimezone($tz)->format('Y-m-d');
            });

        $days = [];
        $day = $gridStart->copy();
        while ($day->lte($gridEnd)) {
            $key = $day->format('Y-m-d');
            $days[] = [
                'date' => $day->copy(),
                'inMonth' => $day->month === $monthStart->month,
                'isToday' => $day->isSameDay(now($tz)),
                'posts' => $scheduled->get($key, collect()),
            ];
            $day->addDay();
        }

        return [
            'monthLabel' => $monthStart->format('F Y'),
            'days' => $days,
        ];
    }
}
