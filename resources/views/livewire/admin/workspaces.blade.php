<div>
    <h1 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 1.5rem;">Workspaces</h1>

    {{-- Filters --}}
    <div class="admin-card" style="margin-bottom:1rem; display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, email, or slug..."
            style="flex:1; min-width:200px; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.82rem; outline:none;">
        <select wire:model.live="filterPlan" style="padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.82rem; outline:none; background:#fff;">
            <option value="">All plans</option>
            <option value="starter">Basic</option>
            <option value="pro">Growth</option>
            <option value="agency">Agency</option>
        </select>
        <select wire:model.live="filterStatus" style="padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.82rem; outline:none; background:#fff;">
            <option value="">All statuses</option>
            <option value="trialing">Trialing</option>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="admin-card" style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
            <thead>
                <tr style="border-bottom:2px solid #E2E8F0;">
                    <th style="text-align:left; padding:0.625rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Workspace</th>
                    <th style="text-align:left; padding:0.625rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Owner</th>
                    <th style="text-align:left; padding:0.625rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Plan</th>
                    <th style="text-align:left; padding:0.625rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Status</th>
                    <th style="text-align:left; padding:0.625rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Trial ends</th>
                    <th style="text-align:left; padding:0.625rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Brands</th>
                    <th style="text-align:left; padding:0.625rem 0.75rem; color:#94A3B8; font-weight:600; font-size:0.72rem; text-transform:uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workspaces as $ws)
                    @php
                        $statusColor = match($ws->subscription_status) {
                            'active' => '#16A34A', 'trialing' => '#D97706', default => '#DC2626',
                        };
                        $planLabel = match($ws->plan) { 'starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency', default => ucfirst($ws->plan) };
                    @endphp
                    <tr style="border-bottom:1px solid #F1F5F9;">
                        <td style="padding:0.75rem; color:#0F172A; font-weight:600;">{{ $ws->name }}</td>
                        <td style="padding:0.75rem; color:#64748B;">{{ $ws->owner_email }}</td>
                        <td style="padding:0.75rem;">
                            <select wire:change="changePlan('{{ $ws->id }}', $event.target.value)"
                                style="padding:0.25rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.78rem; background:#fff;">
                                @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $k => $l)
                                    <option value="{{ $k }}" {{ $ws->plan === $k ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:99px; color:#fff; background:{{ $statusColor }};">
                                {{ ucfirst($ws->subscription_status) }}
                            </span>
                        </td>
                        <td style="padding:0.75rem; color:#64748B; font-size:0.78rem;">{{ $ws->trial_ends_at?->format('M j, Y') ?? '—' }}</td>
                        <td style="padding:0.75rem; color:#0F172A; font-weight:600;">{{ $ws->brands()->count() }}</td>
                        <td style="padding:0.75rem;">
                            <button type="button" wire:click="extendTrial('{{ $ws->id }}', 7)"
                                style="font-size:0.72rem; color:#7C3AED; background:#F5F3FF; border:1px solid #DDD6FE; border-radius:5px; padding:2px 8px; cursor:pointer; font-weight:600;">
                                +7 days trial
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:2rem; text-align:center; color:#94A3B8;">No workspaces found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
