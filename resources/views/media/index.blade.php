<x-layouts.app>

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Media</h1>
            <p style="font-size:0.875rem; color:#64748B; margin:0;">
                Images and videos for <strong style="color:#0F172A;">{{ $brand->name }}</strong>
            </p>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
        @livewire('media.media-library', ['brand' => $brand])
    </div>

</x-layouts.app>
