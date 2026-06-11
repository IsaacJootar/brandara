<div>
    <h1 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 1.5rem;">Admin Dashboard</h1>

    {{-- Stat cards --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="admin-card" style="border-left:4px solid #7C3AED;">
            <p style="font-size:0.72rem; font-weight:600; color:#94A3B8; text-transform:uppercase; margin:0 0 0.375rem;">Total workspaces</p>
            <p style="font-size:1.75rem; font-weight:800; color:#0F172A; margin:0;">{{ $totalWorkspaces }}</p>
        </div>
        <div class="admin-card" style="border-left:4px solid #16A34A;">
            <p style="font-size:0.72rem; font-weight:600; color:#94A3B8; text-transform:uppercase; margin:0 0 0.375rem;">Active</p>
            <p style="font-size:1.75rem; font-weight:800; color:#16A34A; margin:0;">{{ $active }}</p>
        </div>
        <div class="admin-card" style="border-left:4px solid #D97706;">
            <p style="font-size:0.72rem; font-weight:600; color:#94A3B8; text-transform:uppercase; margin:0 0 0.375rem;">Trialing</p>
            <p style="font-size:1.75rem; font-weight:800; color:#D97706; margin:0;">{{ $trialing }}</p>
        </div>
        <div class="admin-card" style="border-left:4px solid #DC2626;">
            <p style="font-size:0.72rem; font-weight:600; color:#94A3B8; text-transform:uppercase; margin:0 0 0.375rem;">Expired</p>
            <p style="font-size:1.75rem; font-weight:800; color:#DC2626; margin:0;">{{ $expired }}</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1rem; margin-bottom:1.5rem;">
        {{-- MRR --}}
        <div class="admin-card">
            <p style="font-size:0.72rem; font-weight:600; color:#94A3B8; text-transform:uppercase; margin:0 0 0.375rem;">Estimated MRR</p>
            <p style="font-size:1.5rem; font-weight:800; color:#0F172A; margin:0;">${{ number_format($mrr / 100, 2) }}</p>
            <p style="font-size:0.75rem; color:#64748B; margin:0.25rem 0 0;">From active subscriptions</p>
        </div>

        {{-- By plan --}}
        <div class="admin-card">
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
    <div class="admin-card">
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
