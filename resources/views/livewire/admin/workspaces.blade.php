<div>
    <h1 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 1.5rem;">Workspaces</h1>

    {{-- Filters --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1rem 1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06); margin-bottom:1rem; display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
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

    {{-- Workspace cards --}}
    @forelse($workspaces as $ws)
        @php
            $statusColor = match($ws->subscription_status) {
                'active' => '#16A34A', 'trialing' => '#D97706', default => '#DC2626',
            };
            $planLabel = match($ws->plan) { 'starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency', default => ucfirst($ws->plan) };
            $brands = $ws->brands;
        @endphp
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06); margin-bottom:0.75rem;">
            {{-- Header --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:0.75rem;">
                <div>
                    <p style="font-size:0.95rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">{{ $ws->name }}</p>
                    <p style="font-size:0.78rem; color:#64748B; margin:0;">{{ $ws->owner_email }}</p>
                </div>
                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                    <span style="font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:99px; color:#fff; background:{{ $statusColor }};">
                        {{ ucfirst($ws->subscription_status) }}
                    </span>
                    <select wire:change="changePlan('{{ $ws->id }}', $event.target.value)"
                        style="padding:0.3rem 0.5rem; border:1px solid #E2E8F0; border-radius:6px; font-size:0.78rem; background:#fff;">
                        @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $k => $l)
                            <option value="{{ $k }}" {{ $ws->plan === $k ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <button type="button" wire:click="extendTrial('{{ $ws->id }}', 7)"
                        style="font-size:0.72rem; color:#7C3AED; background:#F5F3FF; border:1px solid #DDD6FE; border-radius:6px; padding:3px 10px; cursor:pointer; font-weight:600;">
                        +7 days trial
                    </button>
                </div>
            </div>

            {{-- Info row --}}
            <div style="display:flex; gap:1.5rem; flex-wrap:wrap; font-size:0.78rem; color:#64748B; margin-bottom:0.75rem;">
                <span>Trial ends: <strong style="color:#0F172A;">{{ $ws->trial_ends_at?->format('M j, Y') ?? '—' }}</strong></span>
                <span>Country: <strong style="color:#0F172A;">{{ $ws->country ?? '—' }}</strong></span>
                <span>Brands: <strong style="color:#0F172A;">{{ $brands->count() }}</strong></span>
            </div>

            {{-- Brands list --}}
            @if($brands->isNotEmpty())
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    @foreach($brands as $brand)
                        <div style="display:flex; align-items:center; gap:0.375rem; padding:0.3rem 0.625rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px;">
                            <div style="width:18px; height:18px; border-radius:5px; background:linear-gradient(135deg,#7C3AED,#A78BFA); display:flex; align-items:center; justify-content:center; font-size:0.58rem; font-weight:700; color:#fff; flex-shrink:0;">{{ strtoupper(substr($brand->name,0,1)) }}</div>
                            <span style="font-size:0.75rem; color:#0F172A; font-weight:500;">{{ $brand->name }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:3rem; text-align:center;">
            <p style="font-size:0.82rem; color:#94A3B8; margin:0;">No workspaces found.</p>
        </div>
    @endforelse
</div>
