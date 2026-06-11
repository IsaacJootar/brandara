<div>
    <h1 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 1.5rem;">Billing & Plans</h1>

    {{-- Billing settings --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);" style="margin-bottom:1.5rem;">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">Payment settings</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-bottom:1rem;">
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#94A3B8; text-transform:uppercase; display:block; margin-bottom:0.375rem;">Default provider</label>
                <select wire:model="defaultProvider" style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.82rem; background:#fff;">
                    <option value="flutterwave">Flutterwave</option>
                    <option value="paystack">Paystack</option>
                </select>
            </div>
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#94A3B8; text-transform:uppercase; display:block; margin-bottom:0.375rem;">Fallback provider</label>
                <select wire:model="fallbackProvider" style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.82rem; background:#fff;">
                    <option value="paystack">Paystack</option>
                    <option value="flutterwave">Flutterwave</option>
                </select>
            </div>
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#94A3B8; text-transform:uppercase; display:block; margin-bottom:0.375rem;">Test mode</label>
                <button type="button" wire:click="$set('testMode', {{ $testMode ? 'false' : 'true' }})"
                    style="width:48px; height:26px; border-radius:99px; border:none; cursor:pointer; position:relative; background:{{ $testMode ? '#D97706' : '#16A34A' }};">
                    <span style="position:absolute; top:3px; width:20px; height:20px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $testMode ? '25px' : '3px' }};"></span>
                </button>
                <span style="font-size:0.75rem; color:{{ $testMode ? '#D97706' : '#16A34A' }}; font-weight:600; margin-left:0.5rem;">{{ $testMode ? 'Test mode ON' : 'Live mode' }}</span>
            </div>
        </div>
        <button type="button" wire:click="saveBillingSettings"
            style="padding:0.5rem 1.25rem; background:#7C3AED; color:#fff; border:none; border-radius:8px; font-size:0.82rem; font-weight:600; cursor:pointer;">
            Save settings
        </button>
    </div>

    {{-- Plans table --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);" style="margin-bottom:1.5rem; overflow-x:auto;">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">Plan pricing</p>
        <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
            <thead>
                <tr style="border-bottom:2px solid #E2E8F0;">
                    <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Plan</th>
                    <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Interval</th>
                    <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Currency</th>
                    <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Amount</th>
                    <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Active</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                    <tr style="border-bottom:1px solid #F1F5F9;">
                        <td style="padding:0.625rem 0.75rem; font-weight:600; color:#0F172A;">{{ $plan->planLabel() }}</td>
                        <td style="padding:0.625rem 0.75rem;">{{ ucfirst($plan->interval) }}</td>
                        <td style="padding:0.625rem 0.75rem;">{{ strtoupper($plan->currency) }}</td>
                        <td style="padding:0.625rem 0.75rem;">
                            <input type="number" value="{{ $plan->amount }}"
                                wire:change="updatePlanPrice('{{ $plan->id }}', $event.target.value)"
                                style="width:100px; padding:0.25rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.82rem; text-align:right;">
                        </td>
                        <td style="padding:0.625rem 0.75rem;">
                            <button type="button" wire:click="togglePlanActive('{{ $plan->id }}')"
                                style="width:36px; height:20px; border-radius:99px; border:none; cursor:pointer; position:relative; background:{{ $plan->is_active ? '#16A34A' : '#E2E8F0' }};">
                                <span style="position:absolute; top:2px; width:16px; height:16px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $plan->is_active ? '18px' : '2px' }};"></span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Recent subscriptions --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);" style="overflow-x:auto;">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">Recent subscriptions</p>
        @if($subscriptions->isEmpty())
            <p style="font-size:0.82rem; color:#94A3B8; margin:0;">No subscriptions yet.</p>
        @else
            <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
                <thead>
                    <tr style="border-bottom:1px solid #E2E8F0;">
                        <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Workspace</th>
                        <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Plan</th>
                        <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Amount</th>
                        <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Status</th>
                        <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Period</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $sub)
                        <tr style="border-bottom:1px solid #F8FAFC;">
                            <td style="padding:0.625rem 0.75rem; color:#0F172A;">{{ $sub->workspace?->name ?? '—' }}</td>
                            <td style="padding:0.625rem 0.75rem;">{{ $sub->plan }} / {{ $sub->interval }}</td>
                            <td style="padding:0.625rem 0.75rem; font-weight:600;">{{ strtoupper($sub->currency) }} {{ number_format($sub->amount / 100, 2) }}</td>
                            <td style="padding:0.625rem 0.75rem;">
                                <span style="font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:99px; color:#fff; background:{{ $sub->status === 'active' ? '#16A34A' : '#94A3B8' }};">{{ ucfirst($sub->status) }}</span>
                            </td>
                            <td style="padding:0.625rem 0.75rem; color:#64748B; font-size:0.78rem;">{{ $sub->period_start?->format('M j') }} — {{ $sub->period_end?->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
