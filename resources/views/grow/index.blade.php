<x-layouts.app>

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Grow</h1>
            <p style="font-size:0.875rem; color:#64748B; margin:0;">Engagement automation and lead tracking.</p>
        </div>
    </div>

    {{-- Tab switcher --}}
    @php $tab = request()->query('tab', 'leads'); @endphp
    <div style="display:flex; gap:0.375rem; background:#F8FAFC; padding:0.375rem; border-radius:10px; border:1px solid #E2E8F0; margin-bottom:1.5rem; width:fit-content;">
        <a href="{{ route('grow', ['brand' => $currentBrand->slug, 'tab' => 'leads']) }}"
           style="padding:0.4rem 1rem; border-radius:7px; font-size:0.82rem; font-weight:{{ $tab === 'leads' ? '600' : '400' }}; background:{{ $tab === 'leads' ? '#fff' : 'transparent' }}; color:{{ $tab === 'leads' ? '#0F172A' : '#64748B' }}; text-decoration:none; {{ $tab === 'leads' ? 'box-shadow:0 1px 3px rgba(0,0,0,0.08);' : '' }}">
            Lead Tracker
        </a>
        <a href="{{ route('grow', ['brand' => $currentBrand->slug, 'tab' => 'automation']) }}"
           style="padding:0.4rem 1rem; border-radius:7px; font-size:0.82rem; font-weight:{{ $tab === 'automation' ? '600' : '400' }}; background:{{ $tab === 'automation' ? '#fff' : 'transparent' }}; color:{{ $tab === 'automation' ? '#0F172A' : '#64748B' }}; text-decoration:none; {{ $tab === 'automation' ? 'box-shadow:0 1px 3px rgba(0,0,0,0.08);' : '' }}">
            Automation
        </a>
    </div>

    <x-tier-gate feature="lead_tracker">

        @if($tab === 'leads')
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
                @livewire('grow.lead-tracker', ['brand' => $currentBrand])
            </div>
        @else
            <x-tier-gate feature="engagement_automation">
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
                    @livewire('grow.engagement-automation', ['brand' => $currentBrand])
                </div>
            </x-tier-gate>
        @endif

    </x-tier-gate>

</x-layouts.app>
