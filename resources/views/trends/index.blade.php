<x-layouts.app>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem;">
        <div>
            <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Trends</h1>
            <p style="font-size:0.875rem; color:#64748B; margin:0;">Trending topics, content formats, and competitor signals in your industry.</p>
        </div>
    </div>

    <x-tier-gate feature="trends">
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
            @livewire('trends.trends-dashboard', ['brand' => $currentBrand])
        </div>
    </x-tier-gate>

</x-layouts.app>
