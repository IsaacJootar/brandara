<div>
    <h1 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 0.5rem;">Features & Tier Access</h1>
    <p style="font-size:0.82rem; color:#64748B; margin:0 0 1.5rem;">Toggle which features each plan can access. Changes take effect immediately for all users on that plan.</p>

    {{-- Feature gates matrix --}}
    <div class="admin-card" style="margin-bottom:1.5rem; overflow-x:auto;">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">Feature access by plan</p>
        <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
            <thead>
                <tr style="border-bottom:2px solid #E2E8F0;">
                    <th style="text-align:left; padding:0.625rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase; min-width:200px;">Feature</th>
                    <th style="text-align:center; padding:0.625rem 0.75rem; color:#64748B; font-weight:700; font-size:0.75rem; width:100px;">Basic</th>
                    <th style="text-align:center; padding:0.625rem 0.75rem; color:#7C3AED; font-weight:700; font-size:0.75rem; width:100px;">Growth</th>
                    <th style="text-align:center; padding:0.625rem 0.75rem; color:#D97706; font-weight:700; font-size:0.75rem; width:100px;">Agency</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gates as $featureKey => $gate)
                    <tr style="border-bottom:1px solid #F1F5F9;">
                        <td style="padding:0.75rem;">
                            <p style="font-size:0.835rem; font-weight:600; color:#0F172A; margin:0;">{{ $gate['label'] ?? $featureKey }}</p>
                            <p style="font-size:0.72rem; color:#94A3B8; margin:0.125rem 0 0;">{{ $gate['description'] ?? '' }}</p>
                        </td>
                        @foreach(['starter', 'pro', 'agency'] as $plan)
                            @php $hasAccess = in_array($plan, $gate['plans'] ?? []); @endphp
                            <td style="text-align:center; padding:0.75rem;">
                                <button type="button" wire:click="toggleFeaturePlan('{{ $featureKey }}', '{{ $plan }}')"
                                    style="width:32px; height:32px; border-radius:8px; border:2px solid {{ $hasAccess ? '#16A34A' : '#E2E8F0' }}; background:{{ $hasAccess ? '#DCFCE7' : '#F8FAFC' }}; cursor:pointer; display:inline-flex; align-items:center; justify-content:center;">
                                    @if($hasAccess)
                                        <svg width="14" height="14" fill="none" stroke="#16A34A" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg width="14" height="14" fill="none" stroke="#CBD5E1" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @endif
                                </button>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Limits --}}
    <div class="admin-card">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">Plan limits</p>
        <p style="font-size:0.78rem; color:#64748B; margin:0 0 1rem;">Set 0 for unlimited.</p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1.5rem;">
            {{-- Generation limits --}}
            <div>
                <p style="font-size:0.78rem; font-weight:700; color:#94A3B8; text-transform:uppercase; margin:0 0 0.75rem;">AI generations per month</p>
                @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $key => $label)
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <span style="font-size:0.82rem; color:#0F172A; min-width:60px;">{{ $label }}</span>
                        <input type="number" wire:model="generationLimits.{{ $key }}" min="0"
                            style="width:80px; padding:0.375rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.82rem; text-align:center;">
                    </div>
                @endforeach
            </div>

            {{-- Brand limits --}}
            <div>
                <p style="font-size:0.78rem; font-weight:700; color:#94A3B8; text-transform:uppercase; margin:0 0 0.75rem;">Brands per workspace</p>
                @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $key => $label)
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <span style="font-size:0.82rem; color:#0F172A; min-width:60px;">{{ $label }}</span>
                        <input type="number" wire:model="brandLimits.{{ $key }}" min="0"
                            style="width:80px; padding:0.375rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.82rem; text-align:center;">
                    </div>
                @endforeach
            </div>

            {{-- Storage limits --}}
            <div>
                <p style="font-size:0.78rem; font-weight:700; color:#94A3B8; text-transform:uppercase; margin:0 0 0.75rem;">Storage (MB)</p>
                @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $key => $label)
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <span style="font-size:0.82rem; color:#0F172A; min-width:60px;">{{ $label }}</span>
                        <input type="number" wire:model="storageLimits.{{ $key }}" min="0"
                            style="width:80px; padding:0.375rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.82rem; text-align:center;">
                    </div>
                @endforeach
            </div>
        </div>

        <button type="button" wire:click="saveLimits"
            style="margin-top:1rem; padding:0.55rem 1.5rem; background:#7C3AED; color:#fff; border:none; border-radius:9px; font-size:0.85rem; font-weight:600; cursor:pointer;">
            Save limits
        </button>
    </div>
</div>
