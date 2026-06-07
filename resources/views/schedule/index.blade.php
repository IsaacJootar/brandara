<x-layouts.app>

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Schedule</h1>
            <p style="font-size:0.875rem; color:#64748B; margin:0;">
                Plan when your posts go live for <strong style="color:#0F172A;">{{ $currentBrand->name }}</strong>.
            </p>
        </div>
        <a href="{{ route('create', ['brand' => $currentBrand->slug]) }}"
            style="display:inline-flex; align-items:center; gap:0.45rem; padding:0.6rem 1rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.85rem; font-weight:600; border-radius:9px; text-decoration:none;">
            + Write a post
        </a>
    </div>

    @livewire('schedule.index', ['brand' => $currentBrand])

</x-layouts.app>
