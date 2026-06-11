<div>
    <h1 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 0.5rem;">Features & Tier Access</h1>
    <p style="font-size:0.82rem; color:#64748B; margin:0 0 1.5rem;">Toggle which features each plan can access. Changes take effect immediately for all users on that plan.</p>

    {{-- Feature gates --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06); margin-bottom:1.5rem;">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 0.375rem;">Feature access by plan</p>
        <p style="font-size:0.78rem; color:#64748B; margin:0 0 1.25rem;">Click a toggle to grant or remove access for that plan.</p>

        <div style="display:flex; flex-direction:column; gap:0.625rem;">
            @foreach($gates as $featureKey => $gate)
                <div style="display:flex; align-items:center; gap:1rem; padding:0.875rem 1rem; background:#F8FAFC; border-radius:10px; border:1px solid #E2E8F0; flex-wrap:wrap;">
                    {{-- Feature info --}}
                    <div style="flex:1; min-width:200px;">
                        <p style="font-size:0.85rem; font-weight:700; color:#0F172A; margin:0 0 0.125rem;">{{ $gate['label'] ?? $featureKey }}</p>
                        <p style="font-size:0.72rem; color:#94A3B8; margin:0;">{{ $gate['description'] ?? '' }}</p>
                    </div>

                    {{-- Plan toggles --}}
                    @foreach([
                        'starter' => ['label' => 'Basic', 'color' => '#64748B'],
                        'pro' => ['label' => 'Growth', 'color' => '#7C3AED'],
                        'agency' => ['label' => 'Agency', 'color' => '#D97706'],
                    ] as $plan => $planInfo)
                        @php $hasAccess = in_array($plan, $gate['plans'] ?? []); @endphp
                        <div style="display:flex; align-items:center; gap:0.375rem; min-width:90px;">
                            <button type="button" wire:click="toggleFeaturePlan('{{ $featureKey }}', '{{ $plan }}')"
                                style="width:36px; height:20px; border-radius:99px; border:none; cursor:pointer; position:relative; transition:background 0.2s;
                                background:{{ $hasAccess ? $planInfo['color'] : '#E2E8F0' }};">
                                <span style="position:absolute; top:2px; width:16px; height:16px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $hasAccess ? '18px' : '2px' }};"></span>
                            </button>
                            <span style="font-size:0.72rem; font-weight:600; color:{{ $hasAccess ? $planInfo['color'] : '#CBD5E1' }};">{{ $planInfo['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- Limits --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 0.375rem;">Plan limits</p>
        <p style="font-size:0.78rem; color:#64748B; margin:0 0 1.25rem;">Set 0 for unlimited.</p>

        <div style="display:flex; flex-direction:column; gap:0.625rem;">
            {{-- Generation limits --}}
            <div style="padding:0.875rem 1rem; background:#F8FAFC; border-radius:10px; border:1px solid #E2E8F0;">
                <p style="font-size:0.85rem; font-weight:700; color:#0F172A; margin:0 0 0.75rem;">AI generations per month</p>
                <div style="display:flex; gap:1.5rem; flex-wrap:wrap;">
                    @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $key => $label)
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <span style="font-size:0.78rem; color:#64748B; min-width:55px;">{{ $label }}</span>
                            <input type="number" wire:model="generationLimits.{{ $key }}" min="0"
                                style="width:80px; padding:0.375rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.82rem; text-align:center; background:#fff;">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Brand limits --}}
            <div style="padding:0.875rem 1rem; background:#F8FAFC; border-radius:10px; border:1px solid #E2E8F0;">
                <p style="font-size:0.85rem; font-weight:700; color:#0F172A; margin:0 0 0.75rem;">Brands per workspace</p>
                <div style="display:flex; gap:1.5rem; flex-wrap:wrap;">
                    @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $key => $label)
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <span style="font-size:0.78rem; color:#64748B; min-width:55px;">{{ $label }}</span>
                            <input type="number" wire:model="brandLimits.{{ $key }}" min="0"
                                style="width:80px; padding:0.375rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.82rem; text-align:center; background:#fff;">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Storage limits --}}
            <div style="padding:0.875rem 1rem; background:#F8FAFC; border-radius:10px; border:1px solid #E2E8F0;">
                <p style="font-size:0.85rem; font-weight:700; color:#0F172A; margin:0 0 0.75rem;">Storage per workspace (MB)</p>
                <div style="display:flex; gap:1.5rem; flex-wrap:wrap;">
                    @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $key => $label)
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <span style="font-size:0.78rem; color:#64748B; min-width:55px;">{{ $label }}</span>
                            <input type="number" wire:model="storageLimits.{{ $key }}" min="0"
                                style="width:80px; padding:0.375rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.82rem; text-align:center; background:#fff;">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="button" wire:click="saveLimits"
            style="margin-top:1rem; padding:0.6rem 2rem; background:#7C3AED; color:#fff; border:none; border-radius:9px; font-size:0.875rem; font-weight:600; cursor:pointer;">
            Save limits
        </button>
    </div>
</div>
