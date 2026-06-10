<div>

@if(!$hasData)
    {{-- ── NO DATA STATE ───────────────────────────────────────────────────── --}}
    <div style="background:#F8FAFC; border:2px dashed #E2E8F0; border-radius:14px; padding:3rem 2rem; text-align:center;">
        <div style="width:52px;height:52px;background:#F0FDF4;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg style="width:24px;height:24px;" fill="none" viewBox="0 0 24 24" stroke="#16A34A">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </div>
        <p style="font-size:0.95rem; font-weight:700; color:#0F172A; margin:0 0 0.5rem;">No trend data yet</p>
        <p style="font-size:0.85rem; color:#64748B; margin:0 0 1.25rem; max-width:380px; margin-left:auto; margin-right:auto; line-height:1.6;">
            Trend scanning activates when your platform connections are approved. In development mode, seed sample data below.
        </p>
        <p style="font-size:0.75rem; color:#94A3B8; margin:0;">
            Run <code style="background:#F1F5F9;padding:2px 6px;border-radius:4px;font-size:0.72rem;">php artisan trends:seed-fake {{ $brandSlug }}</code> to load sample trends.
        </p>
    </div>

@else

    {{-- ── STAT CARDS ───────────────────────────────────────────────────────── --}}
    <div class="metric-grid">

        <div class="metric-card metric-teal">
            <div class="metric-label">Industry signals</div>
            <div class="metric-value">{{ $summary['industry_count'] }}</div>
            <div class="metric-sub">Trending topics in your niche</div>
        </div>

        <div class="metric-card metric-violet">
            <div class="metric-label">Format trends</div>
            <div class="metric-value">{{ $summary['format_count'] }}</div>
            <div class="metric-sub">Content types performing well</div>
        </div>

        <div class="metric-card metric-rose">
            <div class="metric-label">Competitor signals</div>
            <div class="metric-value">{{ $summary['competitor_count'] }}</div>
            <div class="metric-sub">Keyword & competitor activity</div>
        </div>

        <div class="metric-card metric-amber">
            <div class="metric-label">Hottest platform</div>
            <div class="metric-value" style="font-size:1.4rem;">{{ $summary['hot_platform'] }}</div>
            <div class="metric-sub">Most active right now</div>
        </div>

    </div>

    {{-- ── TAB SWITCHER ─────────────────────────────────────────────────────── --}}
    <div style="display:flex; gap:0.25rem; background:#F1F5F9; border-radius:10px; padding:0.25rem; margin-bottom:1.25rem;">
        @foreach([
            'industry'   => 'Industry Trends',
            'format'     => 'Content Formats',
            'competitor' => 'Competitor Signals',
        ] as $tab => $label)
            <button type="button" wire:click="setTab('{{ $tab }}')"
                style="flex:1; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.82rem; font-weight:{{ $activeTab === $tab ? '600' : '400' }}; border:none; cursor:pointer; transition:all 0.15s; background:{{ $activeTab === $tab ? '#fff' : 'transparent' }}; color:{{ $activeTab === $tab ? '#0F172A' : '#64748B' }}; box-shadow:{{ $activeTab === $tab ? '0 1px 3px rgba(15,23,42,0.08)' : 'none' }};">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── INDUSTRY TRENDS TAB ──────────────────────────────────────────────── --}}
    @if($activeTab === 'industry')
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0;">Trending topics in your industry</p>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0.125rem 0 0;">Topics gaining momentum — jump in while they're hot</p>
                </div>
                <span style="font-size:0.72rem; color:#0F766E; background:#F0FDFA; padding:3px 10px; border-radius:99px; font-weight:600; border:1px solid #CCFBF1;">Live signals</span>
            </div>
            @foreach($industryTrends as $trend)
                @php
                    $pColors = ['linkedin'=>'#0077B5','twitter'=>'#000','instagram'=>'#DD2A7B','facebook'=>'#1877F2','threads'=>'#333','tiktok'=>'#FE2C55','all'=>'#7C3AED'];
                    $pColor = $pColors[$trend->platform] ?? '#7C3AED';
                    $pName = ucfirst($trend->platform === 'twitter' ? 'X / Twitter' : ($trend->platform === 'all' ? 'All platforms' : $trend->platform));
                    $strengthColor = $trend->strength >= 80 ? '#16A34A' : ($trend->strength >= 60 ? '#D97706' : '#94A3B8');
                    $strengthBg = $trend->strength >= 80 ? '#DCFCE7' : ($trend->strength >= 60 ? '#FEF9C3' : '#F1F5F9');
                @endphp
                <div style="padding:1rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:flex-start; gap:1rem;">
                    {{-- Strength bar --}}
                    <div style="flex-shrink:0; width:36px; text-align:center; padding-top:2px;">
                        <div style="font-size:0.82rem; font-weight:800; color:{{ $strengthColor }};">{{ $trend->strength }}</div>
                        <div style="height:4px; background:#F1F5F9; border-radius:99px; margin-top:3px; overflow:hidden;">
                            <div style="height:100%; width:{{ $trend->strength }}%; background:{{ $strengthColor }}; border-radius:99px;"></div>
                        </div>
                    </div>
                    {{-- Content --}}
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 0.25rem; line-height:1.4;">{{ $trend->title }}</p>
                        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:0.375rem; margin-top:0.375rem;">
                            <span style="font-size:0.7rem; color:#fff; background:{{ $pColor }}; padding:2px 7px; border-radius:99px; font-weight:600;">{{ $pName }}</span>
                            @if($trend->tags)
                                @foreach(array_slice($trend->tags, 0, 3) as $tag)
                                    <span style="font-size:0.7rem; color:#64748B; background:#F1F5F9; padding:2px 7px; border-radius:99px;">{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    {{-- Signal badge --}}
                    <span style="font-size:0.7rem; color:{{ $strengthColor }}; background:{{ $strengthBg }}; padding:3px 8px; border-radius:99px; font-weight:700; flex-shrink:0; white-space:nowrap;">
                        {{ $trend->strength >= 80 ? '🔥 Hot' : ($trend->strength >= 60 ? '↑ Rising' : 'Emerging') }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── CONTENT FORMATS TAB ─────────────────────────────────────────────── --}}
    @if($activeTab === 'format')
        <div style="display:flex; flex-direction:column; gap:1rem;">
            @foreach($contentFormats as $trend)
                @php
                    $pColors = ['linkedin'=>'#0077B5','twitter'=>'#000','instagram'=>'#DD2A7B','facebook'=>'#1877F2','threads'=>'#333','tiktok'=>'#FE2C55','all'=>'#7C3AED'];
                    $pColor = $pColors[$trend->platform] ?? '#7C3AED';
                    $pName = ucfirst($trend->platform === 'twitter' ? 'X / Twitter' : ($trend->platform === 'all' ? 'All platforms' : $trend->platform));
                    $strengthColor = $trend->strength >= 80 ? '#16A34A' : ($trend->strength >= 60 ? '#D97706' : '#94A3B8');
                @endphp
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
                    <div style="height:3px; background:{{ $pColor }};"></div>
                    <div style="padding:1rem 1.25rem;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:0.5rem;">
                            <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0; flex:1;">{{ $trend->title }}</p>
                            <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
                                <span style="font-size:0.7rem; color:#fff; background:{{ $pColor }}; padding:2px 8px; border-radius:99px; font-weight:600;">{{ $pName }}</span>
                                <span style="font-size:0.72rem; font-weight:800; color:{{ $strengthColor }};">{{ $trend->strength }}</span>
                            </div>
                        </div>
                        @if($trend->description)
                            <p style="font-size:0.82rem; color:#64748B; margin:0 0 0.625rem; line-height:1.6;">{{ $trend->description }}</p>
                        @endif
                        <div style="display:flex; align-items:center; gap:0.375rem; flex-wrap:wrap;">
                            @if($trend->tags)
                                @foreach($trend->tags as $tag)
                                    <span style="font-size:0.7rem; color:#64748B; background:#F1F5F9; padding:2px 8px; border-radius:99px;">{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>
                        {{-- Strength meter --}}
                        <div style="margin-top:0.875rem;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:0.25rem;">
                                <span style="font-size:0.7rem; color:#94A3B8; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Signal strength</span>
                                <span style="font-size:0.7rem; font-weight:700; color:{{ $strengthColor }};">{{ $trend->strength }}%</span>
                            </div>
                            <div style="height:6px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                                <div style="height:100%; width:{{ $trend->strength }}%; background:{{ $pColor }}; border-radius:99px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── COMPETITOR SIGNALS TAB ───────────────────────────────────────────── --}}
    @if($activeTab === 'competitor')
        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Tracked Keywords manager --}}
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
                <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 0.25rem;">Keywords & competitors you're tracking</p>
                <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 1rem; line-height:1.5;">Add competitor names, hashtags, or topics. Brandara monitors their activity and surfaces relevant signals.</p>

                {{-- Add form --}}
                <div style="display:flex; gap:0.625rem; margin-bottom:0.875rem; flex-wrap:wrap;">
                    <input type="text" wire:model="newKeyword" placeholder="e.g. #PersonalBranding, CompetitorName"
                        style="flex:1; min-width:180px; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; background:#F8FAFC; outline:none;"
                        wire:keydown.enter="addKeyword">
                    <select wire:model="keywordPlatform"
                        style="padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.82rem; color:#374151; background:#F8FAFC; cursor:pointer;">
                        <option value="all">All platforms</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="twitter">X / Twitter</option>
                        <option value="instagram">Instagram</option>
                        <option value="tiktok">TikTok</option>
                    </select>
                    <button type="button" wire:click="addKeyword"
                        style="padding:0.5rem 1rem; background:#7C3AED; color:#fff; font-size:0.82rem; font-weight:600; border:none; border-radius:8px; cursor:pointer; white-space:nowrap;">
                        + Track
                    </button>
                </div>

                {{-- Keyword chips --}}
                @if($trackedKeywords->isEmpty())
                    <p style="font-size:0.82rem; color:#94A3B8; margin:0;">No keywords tracked yet. Add one above.</p>
                @else
                    <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                        @foreach($trackedKeywords as $kw)
                            <div style="display:inline-flex; align-items:center; gap:0.375rem; background:#F5F3FF; border:1px solid #DDD6FE; border-radius:99px; padding:4px 10px 4px 12px;">
                                <span style="font-size:0.78rem; color:#5B21B6; font-weight:500;">{{ $kw->keyword }}</span>
                                @if($kw->platform !== 'all')
                                    <span style="font-size:0.65rem; color:#94A3B8;">· {{ ucfirst($kw->platform) }}</span>
                                @endif
                                <button type="button" wire:click="removeKeyword('{{ $kw->id }}')"
                                    style="width:14px; height:14px; border-radius:50%; border:none; background:rgba(124,58,237,0.15); color:#7C3AED; font-size:0.65rem; cursor:pointer; display:flex; align-items:center; justify-content:center; line-height:1;">×</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Signal list --}}
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0;">Competitor & keyword activity</p>
                        <p style="font-size:0.78rem; color:#94A3B8; margin:0.125rem 0 0;">What's happening in your competitive space right now</p>
                    </div>
                    <span style="font-size:0.72rem; color:#DC2626; background:#FEF2F2; padding:3px 10px; border-radius:99px; font-weight:600; border:1px solid #FECACA;">Watch signals</span>
                </div>
                @foreach($competitorSignals as $trend)
                    @php
                        $pColors = ['linkedin'=>'#0077B5','twitter'=>'#000','instagram'=>'#DD2A7B','facebook'=>'#1877F2','threads'=>'#333','tiktok'=>'#FE2C55','all'=>'#7C3AED'];
                        $pColor = $pColors[$trend->platform] ?? '#7C3AED';
                        $pName = ucfirst($trend->platform === 'twitter' ? 'X / Twitter' : ($trend->platform === 'all' ? 'All platforms' : $trend->platform));
                        $strengthColor = $trend->strength >= 80 ? '#DC2626' : ($trend->strength >= 60 ? '#D97706' : '#94A3B8');
                        $strengthBg = $trend->strength >= 80 ? '#FEF2F2' : ($trend->strength >= 60 ? '#FEF9C3' : '#F1F5F9');
                    @endphp
                    <div style="padding:1rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:flex-start; gap:1rem;">
                        <div style="flex-shrink:0; width:36px; text-align:center; padding-top:2px;">
                            <div style="font-size:0.82rem; font-weight:800; color:{{ $strengthColor }};">{{ $trend->strength }}</div>
                            <div style="height:4px; background:#F1F5F9; border-radius:99px; margin-top:3px; overflow:hidden;">
                                <div style="height:100%; width:{{ $trend->strength }}%; background:{{ $strengthColor }}; border-radius:99px;"></div>
                            </div>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 0.375rem; line-height:1.4;">{{ $trend->title }}</p>
                            <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                                <span style="font-size:0.7rem; color:#fff; background:{{ $pColor }}; padding:2px 7px; border-radius:99px; font-weight:600;">{{ $pName }}</span>
                                @if($trend->tags)
                                    @foreach(array_slice($trend->tags, 0, 3) as $tag)
                                        <span style="font-size:0.7rem; color:#64748B; background:#F1F5F9; padding:2px 7px; border-radius:99px;">{{ $tag }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <span style="font-size:0.7rem; color:{{ $strengthColor }}; background:{{ $strengthBg }}; padding:3px 8px; border-radius:99px; font-weight:700; flex-shrink:0; white-space:nowrap;">
                            {{ $trend->strength >= 80 ? '⚠ Alert' : ($trend->strength >= 60 ? 'Watch' : 'Low') }}
                        </span>
                    </div>
                @endforeach
            </div>

        </div>
    @endif

@endif

</div>
