{{-- ── STAT CARDS ──────────────────────────────────────────────────────────── --}}
<div class="metric-grid">
    <div class="metric-card metric-violet">
        <div class="metric-label">Published this month</div>
        <div class="metric-value">{{ $postsThisMonth }}</div>
        <div class="metric-sub">
            {{ $draftCount }} draft{{ $draftCount !== 1 ? 's' : '' }} · {{ $scheduledCount }} scheduled
            @if($failedCount > 0)
                · <span style="color:rgba(252,165,165,0.9);">{{ $failedCount }} failed</span>
            @endif
        </div>
    </div>
    <div class="metric-card metric-blue">
        <div class="metric-label">Total reach (30 days)</div>
        <div class="metric-value">{{ $totalReach > 0 ? number_format($totalReach) : '—' }}</div>
        <div class="metric-sub">{{ $totalEngagements > 0 ? number_format($totalEngagements).' engagements' : 'Connect platforms to track' }}</div>
    </div>
    <div class="metric-card metric-amber">
        <div class="metric-label">Warm leads</div>
        <div class="metric-value">{{ $warmLeads }}</div>
        <div class="metric-sub">
            {{ $totalLeads }} total tracked
            @if($followUpsDue > 0)
                · <span style="color:rgba(252,165,165,0.9);">{{ $followUpsDue }} follow-up{{ $followUpsDue !== 1 ? 's' : '' }} due</span>
            @endif
        </div>
    </div>
    <div class="metric-card metric-teal">
        <div class="metric-label">Platforms connected</div>
        <div class="metric-value">{{ $activeConnections }}</div>
        <div class="metric-sub">
            @if($activeConnections === 0)
                No platforms connected yet
            @elseif($activeConnections === 1)
                1 platform active
            @else
                {{ $activeConnections }} platforms active
            @endif
        </div>
    </div>
</div>

{{-- ── ACTIVITY + SCHEDULE ─────────────────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">

    {{-- Recent posts --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; justify-content:space-between;">
            <p style="font-size:0.82rem; font-weight:600; color:#0F172A; margin:0;">Recent posts</p>
            <a href="{{ route('schedule', ['brand' => $brand->slug]) }}" style="font-size:0.72rem; color:#7C3AED; text-decoration:none; font-weight:500;">View all →</a>
        </div>
        @if($recentPosts->isEmpty())
            <div style="padding:1.5rem 1.25rem; text-align:center;">
                <p style="font-size:0.82rem; color:#94A3B8; margin:0;">No posts published yet.</p>
                <a href="{{ route('create', ['brand' => $brand->slug]) }}" style="display:inline-block; margin-top:0.625rem; font-size:0.78rem; color:#7C3AED; font-weight:600; text-decoration:none;">Write your first post →</a>
            </div>
        @else
            @foreach($recentPosts as $post)
                @php
                    $platforms = array_keys($post->platform_contents ?? []);
                    $pColors = ['linkedin'=>'#0077B5','twitter'=>'#000','instagram'=>'#DD2A7B','facebook'=>'#1877F2','threads'=>'#333','tiktok'=>'#FE2C55'];
                @endphp
                <div style="padding:0.75rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }}">
                    <p style="font-size:0.82rem; color:#0F172A; margin:0 0 0.25rem; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ Str::limit($post->raw_input ?? 'Untitled post', 55) }}
                    </p>
                    <div style="display:flex; align-items:center; gap:0.375rem; flex-wrap:wrap;">
                        @foreach(array_slice($platforms, 0, 3) as $p)
                            <span style="width:8px; height:8px; border-radius:50%; background:{{ $pColors[$p] ?? '#94A3B8' }}; display:inline-block;"></span>
                        @endforeach
                        <span style="font-size:0.7rem; color:#94A3B8;">{{ $post->published_at ? $post->published_at->diffForHumans() : 'Published' }}</span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Upcoming scheduled --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; justify-content:space-between;">
            <p style="font-size:0.82rem; font-weight:600; color:#0F172A; margin:0;">Coming up</p>
            <a href="{{ route('schedule', ['brand' => $brand->slug]) }}" style="font-size:0.72rem; color:#7C3AED; text-decoration:none; font-weight:500;">Schedule →</a>
        </div>
        @if($upcomingPosts->isEmpty())
            <div style="padding:1.5rem 1.25rem; text-align:center;">
                <p style="font-size:0.82rem; color:#94A3B8; margin:0;">Nothing scheduled yet.</p>
                <a href="{{ route('schedule', ['brand' => $brand->slug]) }}" style="display:inline-block; margin-top:0.625rem; font-size:0.78rem; color:#7C3AED; font-weight:600; text-decoration:none;">Plan your week →</a>
            </div>
        @else
            @foreach($upcomingPosts as $post)
                @php
                    $platforms = array_keys($post->platform_contents ?? []);
                    $pColors = ['linkedin'=>'#0077B5','twitter'=>'#000','instagram'=>'#DD2A7B','facebook'=>'#1877F2','threads'=>'#333','tiktok'=>'#FE2C55'];
                @endphp
                <div style="padding:0.75rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:center; gap:0.75rem;">
                    <div style="flex-shrink:0; text-align:center; background:#F5F3FF; border-radius:8px; padding:0.375rem 0.5rem; min-width:40px;">
                        <div style="font-size:0.72rem; font-weight:800; color:#7C3AED; line-height:1;">{{ $post->scheduled_at->format('d') }}</div>
                        <div style="font-size:0.62rem; color:#A78BFA; text-transform:uppercase; letter-spacing:0.05em;">{{ $post->scheduled_at->format('M') }}</div>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:0.82rem; color:#0F172A; margin:0 0 0.2rem; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ Str::limit($post->raw_input ?? 'Untitled post', 45) }}
                        </p>
                        <div style="display:flex; align-items:center; gap:0.375rem;">
                            @foreach(array_slice($platforms, 0, 3) as $p)
                                <span style="width:7px; height:7px; border-radius:50%; background:{{ $pColors[$p] ?? '#94A3B8' }}; display:inline-block;"></span>
                            @endforeach
                            <span style="font-size:0.68rem; color:#94A3B8;">{{ $post->scheduled_at->format('g:i A') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- ── BRAND HEALTH + CAMPAIGNS ────────────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">

    {{-- Brand setup progress --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.875rem;">
            <p style="font-size:0.82rem; font-weight:600; color:#0F172A; margin:0;">Brand setup</p>
            <span style="font-size:0.82rem; font-weight:800; color:{{ $completionScore >= 80 ? '#16A34A' : ($completionScore >= 40 ? '#D97706' : '#7C3AED') }};">{{ $completionScore }}%</span>
        </div>
        <div style="height:8px; background:#F1F5F9; border-radius:99px; overflow:hidden; margin-bottom:0.875rem;">
            <div style="height:100%; width:{{ $completionScore }}%; background:{{ $completionScore >= 80 ? 'linear-gradient(90deg,#16A34A,#22C55E)' : ($completionScore >= 40 ? 'linear-gradient(90deg,#D97706,#F59E0B)' : 'linear-gradient(90deg,#7C3AED,#A78BFA)') }}; border-radius:99px; transition:width 0.5s;"></div>
        </div>
        <div style="display:flex; flex-direction:column; gap:0.375rem;">
            @foreach([
                ['label' => 'Brand profile & tagline', 'done' => filled($brand->tagline) && filled($brand->description)],
                ['label' => 'Target audience defined', 'done' => filled($brand->target_audience)],
                ['label' => 'Brand Voice trained', 'done' => filled($brand->brand_voice)],
                ['label' => 'Platform connected', 'done' => $activeConnections > 0],
                ['label' => 'First post published', 'done' => $postsThisMonth > 0],
            ] as $check)
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <div style="width:16px; height:16px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; background:{{ $check['done'] ? '#DCFCE7' : '#F1F5F9' }};">
                        @if($check['done'])
                            <svg width="9" height="9" fill="none" stroke="#16A34A" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <div style="width:5px; height:5px; border-radius:50%; background:#CBD5E1;"></div>
                        @endif
                    </div>
                    <span style="font-size:0.78rem; color:{{ $check['done'] ? '#374151' : '#94A3B8' }};">{{ $check['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Content strategy snapshot --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <p style="font-size:0.82rem; font-weight:600; color:#0F172A; margin:0 0 1rem;">Content strategy</p>
        <div style="display:flex; flex-direction:column; gap:0.75rem;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <div style="width:32px; height:32px; border-radius:8px; background:#F5F3FF; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="#7C3AED" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <span style="font-size:0.82rem; color:#374151;">Content pillars</span>
                </div>
                <span style="font-size:0.875rem; font-weight:700; color:#0F172A;">{{ $pillarCount }}<span style="font-size:0.72rem; font-weight:400; color:#94A3B8;">/5</span></span>
            </div>
            <div style="height:1px; background:#F1F5F9;"></div>
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <div style="width:32px; height:32px; border-radius:8px; background:#FFF7ED; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="#D97706" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span style="font-size:0.82rem; color:#374151;">Active campaigns</span>
                </div>
                <span style="font-size:0.875rem; font-weight:700; color:#0F172A;">{{ $activeCampaigns }}</span>
            </div>
            <div style="height:1px; background:#F1F5F9;"></div>
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <div style="width:32px; height:32px; border-radius:8px; background:#F0FDF4; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="#16A34A" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span style="font-size:0.82rem; color:#374151;">Total leads</span>
                </div>
                <span style="font-size:0.875rem; font-weight:700; color:#0F172A;">{{ $totalLeads }}</span>
            </div>
            @if($failedCount > 0)
                <div style="height:1px; background:#F1F5F9;"></div>
                <a href="{{ route('schedule', ['brand' => $brand->slug]) }}" style="display:flex; align-items:center; justify-content:space-between; text-decoration:none;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <div style="width:32px; height:32px; border-radius:8px; background:#FEF2F2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="15" height="15" fill="none" stroke="#DC2626" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <span style="font-size:0.82rem; color:#DC2626; font-weight:500;">Posts need attention</span>
                    </div>
                    <span style="font-size:0.875rem; font-weight:700; color:#DC2626;">{{ $failedCount }} →</span>
                </a>
            @endif
        </div>
    </div>
</div>
