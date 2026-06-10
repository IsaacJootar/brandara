<?php

namespace App\Livewire\Grow;

use App\Models\Brand;
use App\Models\Lead;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadTracker extends Component
{
    use WithPagination;

    public string $brandId = '';

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $search = '';

    public string $filterTag = '';

    public string $filterPlatform = '';

    public string $sortBy = 'last_engaged_at';

    // ── Edit lead state ───────────────────────────────────────────────────────
    public ?string $editingId = null;

    public string $editTag = '';

    public string $editNotes = '';

    public string $editFollowUp = '';

    public const TAGS = [
        '' => 'All tags',
        'warm_lead' => '🔥 Warm lead',
        'prospect' => '👀 Prospect',
        'client' => '✓ Client',
        'partner' => '🤝 Partner',
        'other' => 'Other',
    ];

    public const PLATFORMS = [
        '' => 'All platforms',
        'linkedin' => 'LinkedIn',
        'twitter' => 'X',
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'threads' => 'Threads',
    ];

    public function mount(Brand $brand): void
    {
        $this->brandId = $brand->id;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTag(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPlatform(): void
    {
        $this->resetPage();
    }

    // ── Edit lead ─────────────────────────────────────────────────────────────

    public function startEdit(string $id): void
    {
        $lead = $this->findLead($id);
        $this->editingId = $id;
        $this->editTag = $lead->tag ?? '';
        $this->editNotes = $lead->notes ?? '';
        $this->editFollowUp = $lead->follow_up_at?->format('Y-m-d') ?? '';
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editFollowUp' => ['nullable', 'date'],
        ], [
            'editFollowUp.date' => 'Enter a valid follow-up date.',
        ]);

        $this->findLead($this->editingId)->update([
            'tag' => $this->editTag ?: null,
            'notes' => trim($this->editNotes) ?: null,
            'follow_up_at' => $this->editFollowUp ?: null,
        ]);

        $this->cancelEdit();
        $this->dispatch('show-toast', message: 'Lead updated.');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editTag = '';
        $this->editNotes = '';
        $this->editFollowUp = '';
    }

    public function deleteLead(string $id): void
    {
        $this->findLead($id)->delete();
        $this->dispatch('show-toast', message: 'Lead removed.');
    }

    // ── CSV export ────────────────────────────────────────────────────────────

    public function export(): StreamedResponse
    {
        $leads = Lead::where('brand_id', $this->brandId)
            ->orderByDesc('last_engaged_at')
            ->get();

        $brand = Brand::findOrFail($this->brandId);
        $filename = 'leads-'.str($brand->name)->slug().'-'.now()->format('Y-m-d').'.csv';

        return Response::streamDownload(function () use ($leads) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Name', 'Platform', 'Company', 'Headline', 'Tag', 'Total Engagements', 'Last Engaged', 'Follow-up Date', 'Notes', 'Profile URL']);

            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->name,
                    ucfirst($lead->platform === 'twitter' ? 'X' : $lead->platform),
                    $lead->company ?? '',
                    $lead->headline ?? '',
                    $lead->tag ?? '',
                    $lead->total_engagements,
                    $lead->last_engaged_at?->format('Y-m-d') ?? '',
                    $lead->follow_up_at?->format('Y-m-d') ?? '',
                    $lead->notes ?? '',
                    $lead->profile_url ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render(): View
    {
        $query = Lead::where('brand_id', $this->brandId)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('company', 'like', '%'.$this->search.'%')
                    ->orWhere('headline', 'like', '%'.$this->search.'%');
            }))
            ->when($this->filterTag, fn ($q) => $q->where('tag', $this->filterTag))
            ->when($this->filterPlatform, fn ($q) => $q->where('platform', $this->filterPlatform))
            ->orderByDesc($this->sortBy === 'follow_up_at' ? 'follow_up_at' : 'last_engaged_at')
            ->paginate(20);

        // Stats
        $total = Lead::where('brand_id', $this->brandId)->count();
        $warmLeads = Lead::where('brand_id', $this->brandId)->where('tag', 'warm_lead')->count();
        $followUpsDue = Lead::where('brand_id', $this->brandId)
            ->whereNotNull('follow_up_at')
            ->whereDate('follow_up_at', '<=', now()->addDays(7))
            ->whereDate('follow_up_at', '>=', now())
            ->count();
        $tagBreakdown = Lead::where('brand_id', $this->brandId)
            ->selectRaw('tag, count(*) as total')
            ->whereNotNull('tag')
            ->groupBy('tag')
            ->pluck('total', 'tag');

        return view('livewire.grow.lead-tracker', compact(
            'query', 'total', 'warmLeads', 'followUpsDue', 'tagBreakdown'
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function findLead(string $id): Lead
    {
        return Lead::where('id', $id)
            ->where('brand_id', $this->brandId)
            ->firstOrFail();
    }
}
