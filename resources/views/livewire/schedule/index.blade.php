<div>

    {{-- ── Toolbar: view switch + tab counts ───────────────────────────── --}}
    <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">

        {{-- View toggle --}}
        <div style="display:flex; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:0.25rem; gap:0.25rem;">
            @foreach (['queue' => 'Queue', 'calendar' => 'Calendar'] as $key => $label)
                <button wire:click="setView('{{ $key }}')" type="button"
                    style="padding:0.45rem 0.95rem; font-size:0.82rem; font-weight:{{ $view === $key ? '600' : '500' }}; border:none; border-radius:7px; cursor:pointer;
                    {{ $view === $key ? 'background:#fff; color:#0F172A; box-shadow:0 1px 3px rgba(15,23,42,0.08);' : 'background:transparent; color:#64748B;' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Brand timezone hint --}}
        <div style="font-size:0.75rem; color:#94A3B8;">All times shown in <strong style="color:#475569;">{{ $brandTimezone }}</strong></div>
    </div>

    @if ($view === 'queue')
        {{-- ── QUEUE VIEW ─────────────────────────────────────────────────── --}}

        {{-- Tabs --}}
        <div style="display:flex; gap:0.375rem; border-bottom:1px solid #E2E8F0; margin-bottom:1.25rem; overflow-x:auto;">
            @foreach ([
                'scheduled' => ['Scheduled', $this->counts['scheduled']],
                'drafts'    => ['Not published yet', $this->counts['drafts']],
                'published' => ['Published', $this->counts['published']],
                'failed'    => ['Needs attention', $this->counts['failed']],
            ] as $key => [$label, $count])
                <button wire:click="setTab('{{ $key }}')" type="button"
                    style="padding:0.65rem 1rem; font-size:0.85rem; font-weight:{{ $tab === $key ? '600' : '500' }}; border:none; background:transparent; cursor:pointer; white-space:nowrap; color:{{ $tab === $key ? '#7C3AED' : '#64748B' }}; border-bottom:2px solid {{ $tab === $key ? '#7C3AED' : 'transparent' }}; margin-bottom:-1px;">
                    {{ $label }}
                    @if ($count > 0)
                        <span style="font-size:0.7rem; font-weight:700; background:{{ $key === 'failed' ? '#FEF2F2' : '#F1F5F9' }}; color:{{ $key === 'failed' ? '#DC2626' : '#64748B' }}; padding:0.1rem 0.45rem; border-radius:99px; margin-left:0.35rem;">{{ $count }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- List --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
            @forelse ($posts as $post)
                <div style="display:flex; align-items:flex-start; gap:1rem; padding:1rem 1.25rem; {{ ! $loop->last ? 'border-bottom:1px solid #F1F5F9;' : '' }}">

                    {{-- Content preview --}}
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.875rem; color:#0F172A; line-height:1.5; word-break:break-word;">
                            {{ \Illuminate\Support\Str::limit($post->raw_input ?? '(no content)', 180) }}
                        </div>
                        <div style="font-size:0.72rem; color:#94A3B8; margin-top:0.4rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                            @if ($post->scheduled_at && $tab === 'scheduled')
                                <span>🗓 {{ $post->scheduled_at->setTimezone($brandTimezone)->format('D, M j · g:i A') }}</span>
                            @elseif ($post->published_at && $tab === 'published')
                                <span>✓ Published {{ $post->published_at->setTimezone($brandTimezone)->diffForHumans() }}</span>
                            @else
                                <span>Updated {{ $post->updated_at->diffForHumans() }}</span>
                            @endif
                            <span>{{ implode(', ', array_keys($post->platform_contents ?? [])) ?: '—' }}</span>
                        </div>

                        @if ($tab === 'failed' && $post->failure_reason)
                            <div style="margin-top:0.5rem; background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; padding:0.5rem 0.75rem; font-size:0.78rem; color:#DC2626;">
                                {{ $post->failure_reason }}
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex; gap:0.4rem; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end;">
                        @if ($tab === 'drafts')
                            <button wire:click="openSchedule('{{ $post->id }}')" type="button"
                                style="font-size:0.75rem; font-weight:600; color:#fff; background:#7C3AED; border:none; padding:0.45rem 0.85rem; border-radius:7px; cursor:pointer;">
                                Schedule
                            </button>
                        @endif

                        @if ($tab === 'scheduled')
                            <button wire:click="openSchedule('{{ $post->id }}')" type="button"
                                style="font-size:0.75rem; font-weight:500; color:#475569; background:#F8FAFC; border:1px solid #E2E8F0; padding:0.45rem 0.75rem; border-radius:7px; cursor:pointer;">
                                Reschedule
                            </button>
                            <button wire:click="cancelSchedule('{{ $post->id }}')" type="button"
                                wire:confirm="Cancel this schedule and move back to drafts?"
                                style="font-size:0.75rem; font-weight:500; color:#DC2626; background:transparent; border:1px solid transparent; padding:0.45rem 0.75rem; border-radius:7px; cursor:pointer;">
                                Cancel
                            </button>
                        @endif

                        @if ($tab === 'failed')
                            <button wire:click="retryFailed('{{ $post->id }}')" type="button"
                                style="font-size:0.75rem; font-weight:600; color:#fff; background:#7C3AED; border:none; padding:0.45rem 0.85rem; border-radius:7px; cursor:pointer;">
                                Retry now
                            </button>
                            <button wire:click="openSchedule('{{ $post->id }}')" type="button"
                                style="font-size:0.75rem; font-weight:500; color:#475569; background:#F8FAFC; border:1px solid #E2E8F0; padding:0.45rem 0.75rem; border-radius:7px; cursor:pointer;">
                                Reschedule
                            </button>
                        @endif

                        @if ($tab === 'published')
                            @foreach (($post->live_post_urls ?? []) as $platform => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                    style="font-size:0.72rem; font-weight:500; color:#7C3AED; text-decoration:none; padding:0.45rem 0.75rem; border:1px solid #EDE9FE; border-radius:7px;">
                                    View on {{ ucfirst($platform) }} ↗
                                </a>
                            @endforeach
                        @endif

                        <button wire:click="deletePost('{{ $post->id }}')" type="button"
                            wire:confirm="Delete this post forever?"
                            style="font-size:0.75rem; color:#94A3B8; background:none; border:none; cursor:pointer; padding:0.45rem 0.5rem;" title="Delete">✕</button>
                    </div>
                </div>
            @empty
                <div style="padding:3rem 1.5rem; text-align:center; color:#94A3B8;">
                    <div style="font-size:0.9rem; font-weight:600; color:#475569; margin-bottom:0.35rem;">Nothing here yet</div>
                    <div style="font-size:0.82rem;">
                        @switch ($tab)
                            @case ('scheduled') Nothing is scheduled. Schedule a draft to see it here. @break
                            @case ('drafts') No drafts. Head to <strong>Create</strong> to write a post. @break
                            @case ('published') Once a post goes live it will show here. @break
                            @case ('failed') 🎉 No failed posts. @break
                        @endswitch
                    </div>
                </div>
            @endforelse
        </div>

    @else
        {{-- ── CALENDAR VIEW ──────────────────────────────────────────────── --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">

            {{-- Calendar header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:0.875rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                <div style="font-size:0.95rem; font-weight:600; color:#0F172A;">{{ $calendar['monthLabel'] }}</div>
                <div style="display:flex; gap:0.4rem;">
                    <button wire:click="previousMonth" type="button"
                        style="width:30px; height:30px; border-radius:7px; border:1px solid #E2E8F0; background:#fff; cursor:pointer; color:#475569;">‹</button>
                    <button wire:click="nextMonth" type="button"
                        style="width:30px; height:30px; border-radius:7px; border:1px solid #E2E8F0; background:#fff; cursor:pointer; color:#475569;">›</button>
                </div>
            </div>

            {{-- Day-of-week header --}}
            <div style="display:grid; grid-template-columns:repeat(7,1fr); background:#F8FAFC; border-bottom:1px solid #F1F5F9;">
                @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow)
                    <div style="padding:0.6rem; text-align:center; font-size:0.7rem; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.06em;">{{ $dow }}</div>
                @endforeach
            </div>

            {{-- Day grid --}}
            <div style="display:grid; grid-template-columns:repeat(7,1fr);">
                @foreach ($calendar['days'] as $day)
                    <div style="min-height:96px; padding:0.45rem 0.55rem; border-right:1px solid #F1F5F9; border-bottom:1px solid #F1F5F9; background:{{ $day['inMonth'] ? '#fff' : '#FAFBFF' }}; position:relative;">
                        <div style="font-size:0.75rem; font-weight:{{ $day['isToday'] ? '700' : '500' }}; color:{{ $day['isToday'] ? '#7C3AED' : ($day['inMonth'] ? '#475569' : '#CBD5E1') }};">
                            {{ $day['date']->day }}
                        </div>
                        <div style="display:flex; flex-direction:column; gap:0.2rem; margin-top:0.3rem;">
                            @foreach ($day['posts'] as $post)
                                @php
                                    $color = $post->status === 'published' ? '#16A34A'
                                           : ($post->status === 'failed'   ? '#DC2626'
                                           : ($post->contentPillar?->color ?? '#7C3AED'));
                                @endphp
                                <div title="{{ ($post->contentPillar?->name ? '[' . $post->contentPillar->name . '] ' : '') . \Illuminate\Support\Str::limit($post->raw_input ?? '', 80) }}"
                                    style="font-size:0.68rem; padding:0.2rem 0.4rem; background:{{ $color }}1A; color:{{ $color }}; border-left:2px solid {{ $color }}; border-radius:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    @if ($post->scheduled_at) {{ $post->scheduled_at->setTimezone($brandTimezone)->format('g:i A') }} @endif
                                    {{ \Illuminate\Support\Str::limit($post->raw_input ?? '', 24) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Schedule modal ────────────────────────────────────────────────── --}}
    @if ($schedulingPostId)
        <div wire:click.self="closeSchedule"
            style="position:fixed; inset:0; background:rgba(15,23,42,0.55); backdrop-filter:blur(4px); display:flex; align-items:center; justify-content:center; padding:1rem; z-index:100;">
            <div style="background:#fff; border-radius:14px; width:100%; max-width:420px; padding:1.5rem; box-shadow:0 24px 48px rgba(15,23,42,0.22);">
                <div style="font-size:1.05rem; font-weight:700; color:#0F172A; margin-bottom:0.25rem;">Schedule post</div>
                <div style="font-size:0.78rem; color:#64748B; margin-bottom:1.25rem;">Pick a date and time in <strong style="color:#475569;">{{ $brandTimezone }}</strong>.</div>

                <label style="display:block; font-size:0.75rem; font-weight:600; color:#475569; margin-bottom:0.3rem;">Date</label>
                <input wire:model="scheduleDate" type="date" min="{{ now($brandTimezone)->format('Y-m-d') }}"
                    style="width:100%; padding:0.65rem 0.85rem; border:1px solid #E2E8F0; border-radius:9px; font-size:0.88rem; margin-bottom:0.9rem; box-sizing:border-box;">
                @error ('scheduleDate') <div style="font-size:0.75rem; color:#DC2626; margin-top:-0.6rem; margin-bottom:0.9rem;">{{ $message }}</div> @enderror

                <label style="display:block; font-size:0.75rem; font-weight:600; color:#475569; margin-bottom:0.3rem;">Time</label>
                <input wire:model="scheduleTime" type="time"
                    style="width:100%; padding:0.65rem 0.85rem; border:1px solid #E2E8F0; border-radius:9px; font-size:0.88rem; margin-bottom:1.25rem; box-sizing:border-box;">
                @error ('scheduleTime') <div style="font-size:0.75rem; color:#DC2626; margin-top:-1rem; margin-bottom:1.25rem;">{{ $message }}</div> @enderror

                <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                    <button wire:click="closeSchedule" type="button"
                        style="padding:0.6rem 1rem; font-size:0.83rem; font-weight:500; color:#475569; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:9px; cursor:pointer;">
                        Cancel
                    </button>
                    <button wire:click="confirmSchedule" wire:loading.attr="disabled" wire:target="confirmSchedule" type="button"
                        style="padding:0.6rem 1.1rem; font-size:0.83rem; font-weight:600; color:#fff; background:linear-gradient(135deg,#7C3AED,#4338CA); border:none; border-radius:9px; cursor:pointer;">
                        <span wire:loading.remove wire:target="confirmSchedule">Schedule it</span>
                        <span wire:loading wire:target="confirmSchedule" style="display:none; align-items:center; gap:0.4rem;"><span class="btn-spinner"></span>Saving…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
