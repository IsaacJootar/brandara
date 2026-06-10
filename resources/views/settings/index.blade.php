<x-layouts.app>

    <div style="margin-bottom:1.75rem;">
        <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Settings</h1>
        <p style="font-size:0.875rem; color:#64748B; margin:0;">Configure preferences for <strong style="color:#0F172A;">{{ $currentBrand->name }}</strong></p>
    </div>

    @livewire('settings.brand-settings', ['brand' => $currentBrand])

</x-layouts.app>
