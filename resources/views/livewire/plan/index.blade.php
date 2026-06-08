<div>

    {{-- Toast --}}
    @if (session('plan_message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3500)"
             x-show="show" x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             style="position:fixed; top:1.25rem; right:1.25rem; z-index:999; background:#0F172A; color:#fff; padding:0.85rem 1.4rem; border-radius:10px; font-size:0.85rem; font-weight:500; display:flex; align-items:center; gap:0.6rem; box-shadow:0 8px 24px rgba(15,23,42,0.22); width:380px; pointer-events:none;">
            <svg width="16" height="16" fill="none" stroke="#4ADE80" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            {{ session('plan_message') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div style="display:flex; gap:0.375rem; border-bottom:1px solid #E2E8F0; margin-bottom:1.25rem; overflow-x:auto;">
        @foreach (['overview' => 'Overview', 'pillars' => 'Content pillars', 'campaigns' => 'Campaigns'] as $key => $label)
            <button wire:click="setTab('{{ $key }}')" type="button"
                style="padding:0.65rem 1rem; font-size:0.85rem; font-weight:{{ $tab === $key ? '600' : '500' }}; border:none; background:transparent; cursor:pointer; white-space:nowrap; color:{{ $tab === $key ? '#7C3AED' : '#64748B' }}; border-bottom:2px solid {{ $tab === $key ? '#7C3AED' : 'transparent' }}; margin-bottom:-1px;">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ═══════════════════ OVERVIEW TAB ═══════════════════ --}}
    @if ($tab === 'overview')

        {{-- How it works tip --}}
        <div style="background:#F5F3FF; border:1px solid #EDE9FE; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1rem; display:flex; gap:0.75rem; align-items:flex-start;">
            <svg width="16" height="16" fill="none" stroke="#7C3AED" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            <div style="font-size:0.78rem; color:#5B21B6; line-height:1.55;">
                <strong>Pillars</strong> are your permanent content topics (e.g. Thought Leadership, Client Wins).
                <strong>Campaigns</strong> are a series of posts you plan around one event or goal — like a product launch or Black Friday. Campaign posts are linked to a pillar so your content balance stays on track.
            </div>
        </div>

        {{-- Pillar balance --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; margin-bottom:1rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
                <div>
                    <div style="font-size:0.9rem; font-weight:600; color:#0F172A;">Content pillar balance</div>
                    <div style="font-size:0.75rem; color:#94A3B8;">Last 30 days</div>
                </div>
                <button wire:click="setTab('pillars')" type="button"
                    style="font-size:0.78rem; color:#7C3AED; background:none; border:none; cursor:pointer; font-weight:500;">
                    Manage pillars →
                </button>
            </div>

            @if (empty($pillarBalance))
                <div style="padding:1.5rem; text-align:center; color:#94A3B8; font-size:0.85rem;">
                    No content pillars yet. <button wire:click="setTab('pillars')" type="button" style="color:#7C3AED; background:none; border:none; cursor:pointer; font-weight:500; font-size:0.85rem;">Set up pillars →</button>
                </div>
            @else
                <div style="display:flex; flex-direction:column; gap:0.75rem;">
                    @foreach ($pillarBalance as $item)
                        <div>
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.3rem;">
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <span style="width:10px; height:10px; border-radius:50%; background:{{ $item['pillar']['color'] }}; display:inline-block; flex-shrink:0;"></span>
                                    <span style="font-size:0.82rem; font-weight:500; color:#0F172A;">{{ $item['pillar']['name'] }}</span>
                                    @if ($item['stale'])
                                        <span style="font-size:0.68rem; background:#FEF3C7; color:#D97706; padding:0.15rem 0.5rem; border-radius:99px; font-weight:600;">
                                            {{ $item['days_since'] === null ? 'Not used yet' : 'Overdue' }}
                                        </span>
                                    @endif
                                </div>
                                <span style="font-size:0.75rem; color:#64748B;">{{ $item['count'] }} post{{ $item['count'] !== 1 ? 's' : '' }} · {{ $item['pct'] }}%</span>
                            </div>
                            <div style="height:6px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                                <div style="height:100%; width:{{ $item['pct'] }}%; background:{{ $item['pillar']['color'] }}; border-radius:99px; transition:width 0.4s;"></div>
                            </div>
                            @if ($item['stale'])
                                <div style="font-size:0.72rem; color:#D97706; margin-top:0.25rem;">
                                    {{ $item['days_since'] !== null ? "No posts in {$item['days_since']} days." : "No posts yet." }} Time to post on this pillar.
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Active campaigns --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
            <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9; display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:0.9rem; font-weight:600; color:#0F172A;">Campaigns</div>
                <button wire:click="setTab('campaigns')" type="button"
                    style="font-size:0.78rem; color:#7C3AED; background:none; border:none; cursor:pointer; font-weight:500;">
                    View all →
                </button>
            </div>
            @forelse ($campaigns->take(3) as $campaign)
                <div style="padding:0.875rem 1.25rem; {{ ! $loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }}">
                    <div style="font-size:0.85rem; font-weight:500; color:#0F172A;">{{ $campaign->name }}</div>
                    <div style="font-size:0.72rem; color:#94A3B8; margin-top:0.25rem;">
                        @if ($campaign->start_date && $campaign->end_date)
                            {{ $campaign->start_date->format('M j') }} – {{ $campaign->end_date->format('M j, Y') }} ·
                        @endif
                        <span style="text-transform:capitalize; color:{{ match($campaign->status) { 'active' => '#16A34A', 'draft' => '#64748B', default => '#94A3B8' } }};">{{ $campaign->status }}</span>
                    </div>
                </div>
            @empty
                <div style="padding:2rem 1.25rem; text-align:center; color:#94A3B8; font-size:0.85rem;">
                    No campaigns yet. <button wire:click="setTab('campaigns')" type="button" style="color:#7C3AED; background:none; border:none; cursor:pointer; font-weight:500; font-size:0.85rem;">Create one →</button>
                </div>
            @endforelse
        </div>

    {{-- ═══════════════════ PILLARS TAB ═══════════════════ --}}
    @elseif ($tab === 'pillars')

        <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
            @if (! $showPillarForm && count($pillars) < 5)
                <button wire:click="openPillarForm()" type="button"
                    style="padding:0.55rem 1rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.83rem; font-weight:600; border:none; border-radius:9px; cursor:pointer;">
                    + Add pillar
                </button>
            @endif
        </div>

        {{-- Pillar form --}}
        @if ($showPillarForm)
            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:1.25rem; margin-bottom:1.25rem;">
                <div style="font-size:0.88rem; font-weight:600; color:#0F172A; margin-bottom:1rem;">{{ $editingPillarId ? 'Edit pillar' : 'New content pillar' }}</div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:0.75rem;">
                    <div>
                        <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.3rem;">Pillar name</label>
                        <input wire:model="pillarName" type="text" placeholder="e.g. Thought Leadership"
                            style="width:100%; padding:0.6rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; box-sizing:border-box;">
                        @error('pillarName') <div style="font-size:0.72rem; color:#DC2626; margin-top:0.25rem;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.3rem;">Goal</label>
                        <select wire:model="pillarGoal"
                            style="width:100%; padding:0.6rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; box-sizing:border-box;">
                            <option value="authority">Authority (expert positioning)</option>
                            <option value="trust">Trust (social proof)</option>
                            <option value="awareness">Awareness (reach new people)</option>
                            <option value="conversion">Conversion (drive action)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.5rem;">Colour</label>
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                        @foreach (['#7C3AED','#0369A1','#D97706','#BE123C','#0F766E','#4338CA','#B45309','#0E7490'] as $swatch)
                            <button wire:click="$set('pillarColor','{{ $swatch }}')" type="button"
                                style="width:28px; height:28px; border-radius:50%; background:{{ $swatch }}; border:{{ $pillarColor === $swatch ? '3px solid #0F172A' : '2px solid transparent' }}; cursor:pointer;"></button>
                        @endforeach
                        <input wire:model="pillarColor" type="color" style="width:28px; height:28px; border:none; padding:0; cursor:pointer; border-radius:50%;" title="Custom colour">
                    </div>
                </div>

                <div style="display:flex; gap:0.5rem;">
                    <button wire:click="savePillar" wire:loading.attr="disabled" wire:target="savePillar" type="button"
                        style="padding:0.55rem 1rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.83rem; font-weight:600; border:none; border-radius:8px; cursor:pointer;">
                        <span wire:loading.remove wire:target="savePillar">Save pillar</span>
                        <span wire:loading wire:target="savePillar" style="display:none;">Saving…</span>
                    </button>
                    <button wire:click="$set('showPillarForm',false)" type="button"
                        style="padding:0.55rem 1rem; background:#F1F5F9; color:#475569; font-size:0.83rem; font-weight:500; border:none; border-radius:8px; cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        @endif

        {{-- Pillar list --}}
        <div style="display:flex; flex-direction:column; gap:0.5rem;">
            @forelse ($pillars as $pillar)
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:center; gap:1rem;">
                    <div style="width:14px; height:14px; border-radius:50%; background:{{ $pillar->color }}; flex-shrink:0;"></div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.875rem; font-weight:600; color:#0F172A;">{{ $pillar->name }}</div>
                        <div style="font-size:0.72rem; color:#94A3B8; text-transform:capitalize;">{{ $pillar->goal }}</div>
                    </div>
                    <button wire:click="openPillarForm('{{ $pillar->id }}')" type="button"
                        style="font-size:0.75rem; color:#64748B; background:#F8FAFC; border:1px solid #E2E8F0; padding:0.35rem 0.7rem; border-radius:7px; cursor:pointer;">
                        Edit
                    </button>
                    <button wire:click="deletePillar('{{ $pillar->id }}')" wire:confirm="Remove this pillar?" type="button"
                        style="font-size:0.75rem; color:#DC2626; background:none; border:none; cursor:pointer; padding:0.35rem 0.5rem;">
                        Remove
                    </button>
                </div>
            @empty
                <div style="background:#fff; border:1px dashed #E2E8F0; border-radius:12px; padding:2.5rem; text-align:center; color:#94A3B8; font-size:0.85rem;">
                    No pillars yet. Add up to 5 content pillars to organise your posting strategy.
                </div>
            @endforelse
        </div>

    {{-- ═══════════════════ CAMPAIGNS TAB ═══════════════════ --}}
    @elseif ($tab === 'campaigns')

        <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
            @if (! $showCampaignForm)
                <button wire:click="openCampaignForm()" type="button"
                    style="padding:0.55rem 1rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.83rem; font-weight:600; border:none; border-radius:9px; cursor:pointer;">
                    + New campaign
                </button>
            @endif
        </div>

        {{-- Campaign form --}}
        @if ($showCampaignForm)
            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:1.25rem; margin-bottom:1.25rem;">
                <div style="font-size:0.88rem; font-weight:600; color:#0F172A; margin-bottom:1rem;">{{ $editingCampaignId ? 'Edit campaign' : 'New campaign' }}</div>

                <div style="display:grid; gap:0.75rem; margin-bottom:0.75rem;">
                    <div>
                        <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.3rem;">Campaign name</label>
                        <input wire:model="campaignName" type="text" placeholder="e.g. Black Friday 2025, Accra Branch Launch"
                            style="width:100%; padding:0.6rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; box-sizing:border-box;">
                        @error('campaignName') <div style="font-size:0.72rem; color:#DC2626; margin-top:0.25rem;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.3rem;">
                            Goal
                            <span style="font-weight:400; color:#94A3B8; font-size:0.68rem; margin-left:0.35rem;">What business result do you want from this campaign?</span>
                        </label>
                        <input wire:model="campaignGoal" type="text" placeholder="e.g. Get 20 new leads from Lagos SME owners this November"
                            style="width:100%; padding:0.6rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; box-sizing:border-box;">
                        <div style="font-size:0.7rem; color:#94A3B8; margin-top:0.3rem;">Other examples: "Drive 50 sign-ups for our new payroll feature" · "Fill our December consulting slots"</div>
                        @error('campaignGoal') <div style="font-size:0.72rem; color:#DC2626; margin-top:0.25rem;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.3rem;">
                            Key message
                            <span style="font-weight:400; color:#94A3B8; font-size:0.68rem; margin-left:0.35rem;">The one thing your audience should hear.</span>
                        </label>
                        <textarea wire:model="campaignKeyMessage" rows="2"
                            placeholder="e.g. We're offering 30% off our audit services only this November"
                            style="width:100%; padding:0.6rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; box-sizing:border-box; resize:vertical;"></textarea>
                        <div style="font-size:0.7rem; color:#94A3B8; margin-top:0.3rem;">Other examples: "Our new Accra branch is open — book a free consultation" · "We now serve clients across 5 African countries"</div>
                        @error('campaignKeyMessage') <div style="font-size:0.72rem; color:#DC2626; margin-top:0.25rem;">{{ $message }}</div> @enderror
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                        <div>
                            <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.3rem;">Start date</label>
                            <input wire:model="campaignStartDate" type="date"
                                style="width:100%; padding:0.6rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.3rem;">End date</label>
                            <input wire:model="campaignEndDate" type="date"
                                style="width:100%; padding:0.6rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; box-sizing:border-box;">
                            @error('campaignEndDate') <div style="font-size:0.72rem; color:#DC2626; margin-top:0.25rem;">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.72rem; font-weight:600; color:#475569; display:block; margin-bottom:0.25rem;">
                            Which platforms will this campaign run on?
                        </label>
                        <div style="font-size:0.7rem; color:#94A3B8; margin-bottom:0.5rem;">Select all that apply — tap to toggle on/off.</div>
                        <div style="display:flex; flex-wrap:wrap; gap:0.4rem;">
                            @foreach (['linkedin'=>'LinkedIn','twitter'=>'X','facebook'=>'Facebook','instagram'=>'Instagram','threads'=>'Threads','whatsapp'=>'WhatsApp','tiktok'=>'TikTok'] as $key => $name)
                                @php $checked = in_array($key, $campaignPlatforms); @endphp
                                <button wire:click="toggleCampaignPlatform('{{ $key }}')" type="button"
                                    style="padding:0.35rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:{{ $checked ? '600' : '400' }}; border:1px solid {{ $checked ? '#7C3AED' : '#E2E8F0' }}; background:{{ $checked ? '#F5F3FF' : '#fff' }}; color:{{ $checked ? '#7C3AED' : '#64748B' }}; cursor:pointer; transition:all 0.12s;">
                                    {{ $checked ? '✓ ' : '' }}{{ $name }}
                                </button>
                            @endforeach
                        </div>
                        @error('campaignPlatforms') <div style="font-size:0.72rem; color:#DC2626; margin-top:0.25rem;">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="display:flex; gap:0.5rem;">
                    <button wire:click="saveCampaign" wire:loading.attr="disabled" wire:target="saveCampaign" type="button"
                        style="padding:0.55rem 1rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.83rem; font-weight:600; border:none; border-radius:8px; cursor:pointer;">
                        <span wire:loading.remove wire:target="saveCampaign">Save campaign</span>
                        <span wire:loading wire:target="saveCampaign" style="display:none;">Saving…</span>
                    </button>
                    <button wire:click="$set('showCampaignForm',false)" type="button"
                        style="padding:0.55rem 1rem; background:#F1F5F9; color:#475569; font-size:0.83rem; font-weight:500; border:none; border-radius:8px; cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </div>
        @endif

        {{-- Campaign list --}}
        @php
            $cardColors = [
                0 => ['bg' => '#F5F3FF', 'border' => '#EDE9FE', 'dot' => '#7C3AED'],
                1 => ['bg' => '#EFF6FF', 'border' => '#DBEAFE', 'dot' => '#2563EB'],
                2 => ['bg' => '#FFF7ED', 'border' => '#FED7AA', 'dot' => '#EA580C'],
                3 => ['bg' => '#F0FDF4', 'border' => '#BBF7D0', 'dot' => '#16A34A'],
                4 => ['bg' => '#FFF1F2', 'border' => '#FFE4E6', 'dot' => '#E11D48'],
                5 => ['bg' => '#ECFDF5', 'border' => '#A7F3D0', 'dot' => '#059669'],
                6 => ['bg' => '#FFFBEB', 'border' => '#FDE68A', 'dot' => '#D97706'],
                7 => ['bg' => '#F0F9FF', 'border' => '#BAE6FD', 'dot' => '#0284C7'],
            ];
        @endphp

        <div style="display:flex; flex-direction:column; gap:0.625rem;">
            @forelse ($campaigns as $index => $campaign)
                @php $c = $cardColors[$index % count($cardColors)]; @endphp
                <div style="background:{{ $c['bg'] }}; border:1px solid {{ $c['border'] }}; border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:flex-start; gap:1rem;">

                    {{-- Color dot --}}
                    <div style="width:10px; height:10px; border-radius:50%; background:{{ $c['dot'] }}; flex-shrink:0; margin-top:5px;"></div>

                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.875rem; font-weight:600; color:#0F172A;">{{ $campaign->name }}</div>
                        <div style="font-size:0.75rem; color:#475569; margin-top:0.2rem; line-height:1.5;">{{ $campaign->key_message }}</div>
                        <div style="font-size:0.7rem; color:#94A3B8; margin-top:0.35rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                            @if ($campaign->start_date)
                                <span>📅 {{ $campaign->start_date->format('M j') }} – {{ $campaign->end_date?->format('M j, Y') }}</span>
                            @endif
                            @if ($campaign->platforms)
                                <span>{{ implode(' · ', array_map('ucfirst', $campaign->platforms)) }}</span>
                            @endif
                        </div>
                    </div>

                    <div style="display:flex; gap:0.4rem; flex-shrink:0;">
                        <button wire:click="openCampaignForm('{{ $campaign->id }}')" type="button"
                            style="font-size:0.75rem; color:#475569; background:rgba(255,255,255,0.7); border:1px solid {{ $c['border'] }}; padding:0.35rem 0.7rem; border-radius:7px; cursor:pointer;">
                            Edit
                        </button>
                        <button wire:click="archiveCampaign('{{ $campaign->id }}')" wire:confirm="Archive this campaign?" type="button"
                            style="font-size:0.75rem; color:#94A3B8; background:none; border:none; cursor:pointer; padding:0.35rem 0.5rem;">
                            Archive
                        </button>
                    </div>
                </div>
            @empty
                <div style="background:#fff; border:1px dashed #E2E8F0; border-radius:12px; padding:2.5rem; text-align:center; color:#94A3B8; font-size:0.85rem;">
                    No campaigns yet. Create one to plan and track a series of posts.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($campaignTotalPages > 1)
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:1rem; padding-top:0.875rem; border-top:1px solid #F1F5F9;">
                <div style="font-size:0.75rem; color:#94A3B8;">
                    Page {{ $campaignPage }} of {{ $campaignTotalPages }}
                </div>
                <div style="display:flex; gap:0.4rem;">
                    <button wire:click="campaignPrevPage" type="button"
                        @if($campaignPage <= 1) disabled @endif
                        style="padding:0.4rem 0.8rem; font-size:0.78rem; font-weight:500; border:1px solid #E2E8F0; border-radius:7px; background:#fff; color:{{ $campaignPage <= 1 ? '#CBD5E1' : '#475569' }}; cursor:{{ $campaignPage <= 1 ? 'not-allowed' : 'pointer' }};">
                        ← Previous
                    </button>
                    <button wire:click="campaignNextPage" type="button"
                        @if($campaignPage >= $campaignTotalPages) disabled @endif
                        style="padding:0.4rem 0.8rem; font-size:0.78rem; font-weight:500; border:1px solid #E2E8F0; border-radius:7px; background:#fff; color:{{ $campaignPage >= $campaignTotalPages ? '#CBD5E1' : '#475569' }}; cursor:{{ $campaignPage >= $campaignTotalPages ? 'not-allowed' : 'pointer' }};">
                        Next →
                    </button>
                </div>
            </div>
        @endif
    @endif

</div>
