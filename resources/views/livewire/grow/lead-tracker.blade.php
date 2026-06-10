<div>

    {{-- ── STATS ROW ────────────────────────────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0.875rem; margin-bottom:1.5rem;">

        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:1rem 1.125rem;">
            <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#94A3B8; margin:0 0 0.375rem;">Total leads</p>
            <p style="font-size:1.75rem; font-weight:700; color:#0F172A; margin:0; line-height:1;">{{ $total }}</p>
        </div>

        <div style="background:#FFF7ED; border:1px solid #FED7AA; border-radius:12px; padding:1rem 1.125rem;">
            <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#EA580C; margin:0 0 0.375rem;">🔥 Warm leads</p>
            <p style="font-size:1.75rem; font-weight:700; color:#EA580C; margin:0; line-height:1;">{{ $warmLeads }}</p>
        </div>

        <div style="background:{{ $followUpsDue > 0 ? '#FEF2F2' : '#F8FAFC' }}; border:1px solid {{ $followUpsDue > 0 ? '#FECACA' : '#E2E8F0' }}; border-radius:12px; padding:1rem 1.125rem;">
            <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:{{ $followUpsDue > 0 ? '#DC2626' : '#94A3B8' }}; margin:0 0 0.375rem;">Follow-ups due</p>
            <p style="font-size:1.75rem; font-weight:700; color:{{ $followUpsDue > 0 ? '#DC2626' : '#0F172A' }}; margin:0; line-height:1;">{{ $followUpsDue }}</p>
        </div>
    </div>

    {{-- ── FILTERS + EXPORT ─────────────────────────────────────────────────── --}}
    <div style="display:flex; gap:0.75rem; margin-bottom:1.25rem; flex-wrap:wrap; align-items:center;">

        {{-- Search --}}
        <div style="position:relative; flex:1; min-width:180px; max-width:280px;">
            <svg style="position:absolute;left:0.625rem;top:50%;transform:translateY(-50%);width:13px;height:13px;color:#94A3B8;pointer-events:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search leads…"
                style="width:100%;padding:0.45rem 0.75rem 0.45rem 2rem;border:1px solid #E2E8F0;border-radius:8px;font-size:0.82rem;color:#0F172A;background:#fff;outline:none;">
        </div>

        {{-- Tag filter --}}
        <select wire:model.live="filterTag"
            style="padding:0.45rem 0.75rem;border:1px solid #E2E8F0;border-radius:8px;font-size:0.82rem;color:#0F172A;background:#fff;outline:none;">
            @foreach(\App\Livewire\Grow\LeadTracker::TAGS as $val => $lbl)
                <option value="{{ $val }}">{{ $lbl }}</option>
            @endforeach
        </select>

        {{-- Platform filter --}}
        <select wire:model.live="filterPlatform"
            style="padding:0.45rem 0.75rem;border:1px solid #E2E8F0;border-radius:8px;font-size:0.82rem;color:#0F172A;background:#fff;outline:none;">
            @foreach(\App\Livewire\Grow\LeadTracker::PLATFORMS as $val => $lbl)
                <option value="{{ $val }}">{{ $lbl }}</option>
            @endforeach
        </select>

        {{-- Export --}}
        @if($total > 0)
            <a wire:click.prevent="export" href="#"
               style="display:flex;align-items:center;gap:0.375rem;padding:0.45rem 0.875rem;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;font-size:0.8rem;color:#64748B;text-decoration:none;cursor:pointer;font-weight:500;margin-left:auto;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
        @endif
    </div>

    {{-- ── EMPTY STATE ─────────────────────────────────────────────────────── --}}
    @if($query->isEmpty() && !$search && !$filterTag && !$filterPlatform)
        <div style="background:#F8FAFC; border:2px dashed #E2E8F0; border-radius:14px; padding:2.5rem; text-align:center;">
            <div style="width:44px;height:44px;background:#F1F5F9;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.875rem;">
                <svg style="width:20px;height:20px;color:#94A3B8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p style="font-size:0.9rem; font-weight:600; color:#0F172A; margin:0 0 0.375rem;">No leads yet</p>
            <p style="font-size:0.82rem; color:#94A3B8; margin:0; line-height:1.6; max-width:340px; margin-left:auto; margin-right:auto;">
                Leads appear here when people engage with your posts. Connect your platforms and start publishing — Brandara tracks who interacts.
            </p>
        </div>

    @elseif($query->isEmpty())
        <div style="padding:2rem; text-align:center; color:#94A3B8; font-size:0.875rem;">
            No leads match your filters.
        </div>

    @else
        {{-- ── LEADS TABLE ─────────────────────────────────────────────────── --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;" x-data>

            @foreach($query as $lead)
                @php
                    $tagColors = [
                        'warm_lead' => ['bg' => '#FFF7ED', 'color' => '#EA580C'],
                        'prospect'  => ['bg' => '#EFF6FF', 'color' => '#1D4ED8'],
                        'client'    => ['bg' => '#F0FDF4', 'color' => '#15803D'],
                        'partner'   => ['bg' => '#F5F3FF', 'color' => '#7C3AED'],
                        'other'     => ['bg' => '#F8FAFC', 'color' => '#64748B'],
                    ];
                    $tagStyle = $tagColors[$lead->tag] ?? ['bg' => '#F8FAFC', 'color' => '#94A3B8'];
                    $isEditing = $editingId === $lead->id;
                    $followUpDue = $lead->follow_up_at && $lead->follow_up_at->isToday() || ($lead->follow_up_at && $lead->follow_up_at->isPast());
                @endphp

                <div style="padding:0.875rem 1.125rem; {{ !$loop->last ? 'border-bottom:1px solid #F1F5F9;' : '' }} {{ $followUpDue && !$isEditing ? 'background:#FFFBEB;' : '' }}">

                    @if($isEditing)
                        {{-- EDIT ROW ──────────────────────────────────────── --}}
                        <div style="display:flex; flex-direction:column; gap:0.875rem;">
                            <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                                <div style="font-size:0.9rem; font-weight:700; color:#0F172A;">{{ $lead->name }}</div>
                                @if($lead->company)
                                    <span style="font-size:0.78rem; color:#64748B;">{{ $lead->company }}</span>
                                @endif
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.75rem; flex-wrap:wrap;">
                                <div>
                                    <label style="font-size:0.72rem; font-weight:600; color:#374151; display:block; margin-bottom:0.25rem;">Tag</label>
                                    <select wire:model="editTag"
                                        style="width:100%;padding:0.4rem 0.625rem;border:1px solid #E2E8F0;border-radius:7px;font-size:0.82rem;color:#0F172A;background:#fff;outline:none;">
                                        @foreach(\App\Livewire\Grow\LeadTracker::TAGS as $val => $lbl)
                                            @if($val !== '')
                                                <option value="{{ $val }}">{{ $lbl }}</option>
                                            @endif
                                        @endforeach
                                        <option value="">No tag</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:0.72rem; font-weight:600; color:#374151; display:block; margin-bottom:0.25rem;">Follow-up date</label>
                                    <input type="date" wire:model="editFollowUp"
                                        style="width:100%;padding:0.4rem 0.625rem;border:1px solid #E2E8F0;border-radius:7px;font-size:0.82rem;color:#0F172A;outline:none;">
                                    @error('editFollowUp')<p style="color:#EF4444;font-size:0.68rem;margin-top:0.2rem;">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label style="font-size:0.72rem; font-weight:600; color:#374151; display:block; margin-bottom:0.25rem;">Notes</label>
                                    <input type="text" wire:model="editNotes" placeholder="Met at Lagos Summit…"
                                        style="width:100%;padding:0.4rem 0.625rem;border:1px solid #E2E8F0;border-radius:7px;font-size:0.82rem;color:#0F172A;outline:none;">
                                </div>
                            </div>
                            <div style="display:flex; gap:0.5rem;">
                                <button type="button" wire:click="saveEdit"
                                    style="padding:0.4rem 1rem;background:#7C3AED;color:#fff;font-size:0.8rem;font-weight:600;border:none;border-radius:7px;cursor:pointer;">
                                    Save
                                </button>
                                <button type="button" wire:click="cancelEdit"
                                    style="padding:0.4rem 0.875rem;background:#F8FAFC;color:#64748B;font-size:0.8rem;border:1px solid #E2E8F0;border-radius:7px;cursor:pointer;">
                                    Cancel
                                </button>
                            </div>
                        </div>

                    @else
                        {{-- VIEW ROW ──────────────────────────────────────── --}}
                        <div style="display:flex; align-items:flex-start; gap:0.875rem;">

                            {{-- Avatar --}}
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#7C3AED,#A78BFA);display:flex;align-items:center;justify-content:center;font-size:0.82rem;font-weight:700;color:#fff;flex-shrink:0;">
                                {{ strtoupper(substr($lead->name, 0, 1)) }}
                            </div>

                            {{-- Info --}}
                            <div style="flex:1; min-width:0;">
                                <div style="display:flex; align-items:center; gap:0.625rem; flex-wrap:wrap; margin-bottom:0.2rem;">
                                    <span style="font-size:0.875rem; font-weight:700; color:#0F172A;">{{ $lead->name }}</span>

                                    {{-- Platform badge --}}
                                    <span style="font-size:0.68rem; font-weight:600; color:#fff; background:{{ match($lead->platform) { 'linkedin' => '#0077B5', 'twitter' => '#000', 'instagram' => '#DD2A7B', 'facebook' => '#1877F2', 'threads' => '#333', default => '#64748B' } }}; padding:0.15rem 0.5rem; border-radius:99px;">
                                        {{ ucfirst($lead->platform === 'twitter' ? 'X' : $lead->platform) }}
                                    </span>

                                    {{-- Tag --}}
                                    @if($lead->tag)
                                        <span style="font-size:0.72rem; font-weight:600; color:{{ $tagStyle['color'] }}; background:{{ $tagStyle['bg'] }}; padding:0.15rem 0.5rem; border-radius:99px;">
                                            {{ \App\Livewire\Grow\LeadTracker::TAGS[$lead->tag] ?? $lead->tag }}
                                        </span>
                                    @endif

                                    {{-- Follow-up due --}}
                                    @if($followUpDue)
                                        <span style="font-size:0.68rem; font-weight:700; color:#DC2626; background:#FEF2F2; padding:0.15rem 0.5rem; border-radius:99px;">
                                            Follow-up due
                                        </span>
                                    @elseif($lead->follow_up_at)
                                        <span style="font-size:0.68rem; color:#94A3B8;">
                                            Follow up {{ $lead->follow_up_at->format('d M') }}
                                        </span>
                                    @endif
                                </div>

                                @if($lead->headline || $lead->company)
                                    <p style="font-size:0.78rem; color:#64748B; margin:0 0 0.25rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ collect([$lead->headline, $lead->company])->filter()->implode(' · ') }}
                                    </p>
                                @endif

                                @if($lead->notes)
                                    <p style="font-size:0.75rem; color:#94A3B8; margin:0; font-style:italic;">
                                        "{{ Str::limit($lead->notes, 80) }}"
                                    </p>
                                @endif
                            </div>

                            {{-- Engagement count --}}
                            <div style="text-align:right; flex-shrink:0;">
                                <p style="font-size:1rem; font-weight:700; color:#0F172A; margin:0; line-height:1.2;">{{ $lead->total_engagements }}</p>
                                <p style="font-size:0.68rem; color:#94A3B8; margin:0;">engagements</p>
                            </div>

                            {{-- Actions --}}
                            <div style="display:flex; gap:0.375rem; flex-shrink:0; align-items:center;">
                                <button type="button" wire:click="startEdit('{{ $lead->id }}')"
                                    style="padding:0.3rem 0.625rem;background:#F5F3FF;color:#7C3AED;font-size:0.75rem;font-weight:600;border:none;border-radius:6px;cursor:pointer;">
                                    Edit
                                </button>
                                @if($lead->profile_url)
                                    <a href="{{ $lead->profile_url }}" target="_blank" rel="noopener"
                                        style="padding:0.3rem 0.625rem;background:#F8FAFC;color:#64748B;font-size:0.75rem;border:1px solid #E2E8F0;border-radius:6px;text-decoration:none;">
                                        View
                                    </a>
                                @endif
                                <button type="button" wire:click="deleteLead('{{ $lead->id }}')"
                                    wire:confirm="Remove this lead?"
                                    style="background:none;border:none;color:#CBD5E1;cursor:pointer;font-size:0.85rem;padding:0.2rem 0.375rem;border-radius:6px;">✕</button>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($query->hasPages())
            <div style="margin-top:1rem;">
                {{ $query->links() }}
            </div>
        @endif

    @endif

</div>
