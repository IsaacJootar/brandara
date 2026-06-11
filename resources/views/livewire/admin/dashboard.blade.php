<div>
    <h1 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 1.5rem;">Admin Dashboard</h1>

    {{-- Stat cards — gradient style --}}
    <div class="metric-grid" style="margin-bottom:1.5rem;">
        <div class="metric-card metric-violet">
            <div class="metric-label">Total workspaces</div>
            <div class="metric-value">{{ $totalWorkspaces }}</div>
            <div class="metric-sub">Across all plans</div>
        </div>
        <div class="metric-card metric-teal">
            <div class="metric-label">Active</div>
            <div class="metric-value">{{ $active }}</div>
            <div class="metric-sub">Paying customers</div>
        </div>
        <div class="metric-card metric-amber">
            <div class="metric-label">Trialing</div>
            <div class="metric-value">{{ $trialing }}</div>
            <div class="metric-sub">Free trial users</div>
        </div>
        <div class="metric-card metric-rose">
            <div class="metric-label">Expired</div>
            <div class="metric-value">{{ $expired }}</div>
            <div class="metric-sub">Need follow-up</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1rem; margin-bottom:1.5rem;">
        {{-- MRR --}}
        <div class="metric-card metric-blue">
            <div class="metric-label">Estimated MRR</div>
            <div class="metric-value">${{ number_format($mrr / 100, 2) }}</div>
            <div class="metric-sub">From active subscriptions</div>
        </div>

        {{-- By plan --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <p style="font-size:0.72rem; font-weight:600; color:#94A3B8; text-transform:uppercase; margin:0 0 0.75rem;">By plan</p>
            @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $key => $label)
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.375rem;">
                    <span style="font-size:0.82rem; color:#0F172A;">{{ $label }}</span>
                    <span style="font-size:0.82rem; font-weight:700; color:#0F172A;">{{ $byPlan[$key] ?? 0 }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent payments --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">Recent payments</p>
        @if($recentPayments->isEmpty())
            <p style="font-size:0.82rem; color:#94A3B8; margin:0;">No payments recorded yet.</p>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
                    <thead>
                        <tr style="border-bottom:1px solid #E2E8F0;">
                            <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Workspace</th>
                            <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Plan</th>
                            <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Amount</th>
                            <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Provider</th>
                            <th style="text-align:left; padding:0.5rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                            <tr style="border-bottom:1px solid #F8FAFC;">
                                <td style="padding:0.625rem 0.75rem; color:#0F172A;">{{ $payment->workspace?->name ?? '—' }}</td>
                                <td style="padding:0.625rem 0.75rem;">{{ $payment->plan }}</td>
                                <td style="padding:0.625rem 0.75rem; font-weight:600;">{{ strtoupper($payment->currency) }} {{ number_format($payment->amount / 100, 2) }}</td>
                                <td style="padding:0.625rem 0.75rem;">{{ ucfirst($payment->provider) }}</td>
                                <td style="padding:0.625rem 0.75rem; color:#64748B;">{{ $payment->created_at?->format('M j, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
