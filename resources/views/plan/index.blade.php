<x-layouts.app>

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Plan</h1>
            <p style="font-size:0.875rem; color:#64748B; margin:0;">
                Manage content pillars and campaigns for <strong style="color:#0F172A;">{{ $currentBrand->name }}</strong>.
            </p>
        </div>
    </div>

    @livewire('plan.index', ['brand' => $currentBrand])

</x-layouts.app>
