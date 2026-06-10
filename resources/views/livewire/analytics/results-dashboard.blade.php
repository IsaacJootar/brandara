<div>

@if(!$hasData)
    {{-- ── NO DATA STATE ───────────────────────────────────────────────────── --}}
    <div style="background:#F8FAFC; border:2px dashed #E2E8F0; border-radius:14px; padding:3rem 2rem; text-align:center;">
        <div style="width:52px;height:52px;background:#F5F3FF;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg style="width:24px;height:24px;" fill="none" viewBox="0 0 24 24" stroke="#7C3AED">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <p style="font-size:0.95rem; font-weight:700; color:#0F172A; margin:0 0 0.5rem;">No analytics data yet</p>
        <p style="font-size:0.85rem; color:#64748B; margin:0; max-width:380px; margin-left:auto; margin-right:auto; line-height:1.6;">
            Publish posts and connect your platforms — Brandara will start tracking your reach, engagement, and performance automatically.
        </p>
    </div>

@else
    {{-- ── PERIOD SELECTOR ─────────────────────────────────────────────────── --}}
    <div style="display:flex; align-items:center; justify-content:flex-end; margin-bottom:1.25rem; gap:0.375rem;">
        @foreach([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days'] as $days => $label)
            <button type="button" wire:click="setPeriod({{ $days }})"
                style="padding:0.35rem 0.875rem; border-radius:7px; font-size:0.78rem; cursor:pointer; font-weight:{{ $period === $days ? '600' : '400' }}; border:{{ $period === $days ? '2px solid #7C3AED' : '1px solid #E2E8F0' }}; background:{{ $period === $days ? '#F5F3FF' : '#fff' }}; color:{{ $period === $days ? '#7C3AED' : '#64748B' }};">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── STAT CARDS — same gradient style as dashboard metric cards ────────── --}}
    <div class="metric-grid">

        {{-- Total reach --}}
        <div class="metric-card metric-blue">
            <div class="metric-label">Total reach</div>
            <div class="metric-value">{{ number_format($summary['total_reach']) }}</div>
            @php $rc = $wow['reach_change']; $up = $rc > 0; $flat = $rc == 0; @endphp
            <div class="metric-sub" style="color:{{ $up ? 'rgba(134,239,172,0.9)' : ($flat ? 'rgba(255,255,255,0.55)' : 'rgba(252,165,165,0.9)') }};">
                {{ $up ? '↑' : ($flat ? '→' : '↓') }} {{ abs($rc) }}% vs last week
            </div>
        </div>

        {{-- Total engagements --}}
        <div class="metric-card metric-violet">
            <div class="metric-label">Total engagements</div>
            <div class="metric-value">{{ number_format($summary['total_engagements']) }}</div>
            @php $ec = $wow['engagement_change']; $up = $ec > 0; $flat = $ec == 0; @endphp
            <div class="metric-sub" style="color:{{ $up ? 'rgba(134,239,172,0.9)' : ($flat ? 'rgba(255,255,255,0.55)' : 'rgba(252,165,165,0.9)') }};">
                {{ $up ? '↑' : ($flat ? '→' : '↓') }} {{ abs($ec) }}% vs last week
            </div>
        </div>

        {{-- Avg engagement rate --}}
        <div class="metric-card metric-teal">
            <div class="metric-label">Avg engagement rate</div>
            <div class="metric-value">{{ $summary['avg_engagement_rate'] }}%</div>
            <div class="metric-sub">Over the last {{ $period }} days</div>
        </div>

        {{-- Posts published --}}
        <div class="metric-card metric-amber">
            <div class="metric-label">Posts published</div>
            <div class="metric-value">{{ $summary['total_posts'] }}</div>
            <div class="metric-sub">In the last {{ $period }} days</div>
        </div>

    </div>

    {{-- ── ENGAGEMENT CHART ─────────────────────────────────────────────────── --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 1rem;">Reach & Engagements over time</p>
        <canvas id="analyticsChart" height="90"></canvas>
    </div>

    {{-- ── PLATFORM BREAKDOWN + BEST TIMES ─────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">

        {{-- Platform breakdown --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 1rem;">By platform</p>
            @if($platformBreakdown->isEmpty())
                <p style="font-size:0.82rem; color:#94A3B8; margin:0;">No data yet.</p>
            @else
                @php $maxEngagement = $platformBreakdown->max() ?: 1; @endphp
                @foreach($platformBreakdown as $platform => $total)
                    @php
                        $pct = (int) round($total / $maxEngagement * 100);
                        $pColors = ['linkedin'=>'#0077B5','twitter'=>'#000','instagram'=>'#DD2A7B','facebook'=>'#1877F2','threads'=>'#333','tiktok'=>'#FE2C55'];
                        $pColor = $pColors[$platform] ?? '#7C3AED';
                        $pName = ucfirst($platform === 'twitter' ? 'X' : $platform);
                    @endphp
                    <div style="margin-bottom:0.875rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.3rem;">
                            <span style="font-size:0.82rem; font-weight:600; color:#374151;">{{ $pName }}</span>
                            <span style="font-size:0.78rem; color:#64748B; font-weight:500;">{{ number_format($total) }}</span>
                        </div>
                        <div style="height:7px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $pColor }}; border-radius:99px; transition:width 0.5s;"></div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Best posting times --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 0.25rem;">Best times to post</p>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 1rem;">When your posts get the most engagement</p>
            @if(empty($bestTimes))
                <p style="font-size:0.82rem; color:#94A3B8; margin:0;">Not enough data yet.</p>
            @else
                @foreach($bestTimes as $i => $time)
                    <div style="display:flex; align-items:center; gap:0.75rem; padding:0.5rem 0.625rem; border-radius:8px; background:{{ $i === 0 ? '#F5F3FF' : '#F8FAFC' }}; margin-bottom:0.4rem;">
                        <span style="width:20px; height:20px; border-radius:5px; background:{{ $i === 0 ? '#7C3AED' : '#E2E8F0' }}; color:{{ $i === 0 ? '#fff' : '#94A3B8' }}; font-size:0.65rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $i + 1 }}</span>
                        <span style="font-size:0.82rem; font-weight:{{ $i === 0 ? '600' : '400' }}; color:{{ $i === 0 ? '#0F172A' : '#374151' }}; flex:1;">{{ $time['label'] }}</span>
                        <span style="font-size:0.72rem; color:#94A3B8;">{{ $time['avg_engagements'] }} avg</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- ── TOP POSTS ────────────────────────────────────────────────────────── --}}
    @if($topPosts->isNotEmpty())
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0;">Top performing posts</p>
            </div>
            @foreach($topPosts as $i => $post)
                <div style="padding:0.875rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:center; gap:1rem;">
                    <span style="width:26px; height:26px; border-radius:7px; background:{{ $i === 0 ? 'linear-gradient(135deg,#7C3AED,#4338CA)' : '#F1F5F9' }}; color:{{ $i === 0 ? '#fff' : '#94A3B8' }}; font-size:0.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $i + 1 }}</span>
                    <p style="flex:1; font-size:0.85rem; color:#374151; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ Str::limit($post->raw_input ?? 'Untitled post', 70) }}
                    </p>
                    <div style="text-align:right; flex-shrink:0;">
                        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0;">{{ number_format($post->total_engagements) }}</p>
                        <p style="font-size:0.68rem; color:#94A3B8; margin:0;">engagements</p>
                    </div>
                    <span style="font-size:0.72rem; color:#7C3AED; background:#F5F3FF; padding:3px 8px; border-radius:99px; font-weight:600; flex-shrink:0;">{{ round($post->avg_rate, 1) }}%</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── BOTTOM POSTS ─────────────────────────────────────────────────────── --}}
    @if($bottomPosts->isNotEmpty())
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06); margin-top:1.25rem;">
            <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; gap:0.5rem;">
                <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0;">Needs improvement</p>
                <span style="font-size:0.72rem; color:#94A3B8; background:#F8FAFC; padding:2px 7px; border-radius:99px;">lowest engagement</span>
            </div>
            @foreach($bottomPosts as $i => $post)
                <div style="padding:0.875rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:center; gap:1rem;">
                    <span style="width:26px; height:26px; border-radius:7px; background:#FEF2F2; color:#DC2626; font-size:0.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $i + 1 }}</span>
                    <p style="flex:1; font-size:0.85rem; color:#374151; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ Str::limit($post->raw_input ?? 'Untitled post', 70) }}
                    </p>
                    <div style="text-align:right; flex-shrink:0;">
                        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0;">{{ number_format($post->total_engagements) }}</p>
                        <p style="font-size:0.68rem; color:#94A3B8; margin:0;">engagements</p>
                    </div>
                    <span style="font-size:0.72rem; color:#DC2626; background:#FEF2F2; padding:3px 8px; border-radius:99px; font-weight:600; flex-shrink:0;">{{ round($post->avg_rate, 1) }}%</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Chart.js init --}}
    <script>
    (function() {
        const labels = @json($chart['labels']);
        const reach  = @json($chart['reach']);
        const engs   = @json($chart['engagements']);

        function initChart() {
            const canvas = document.getElementById('analyticsChart');
            if (!canvas || !window.Chart) { setTimeout(initChart, 100); return; }
            if (canvas._chartInstance) canvas._chartInstance.destroy();
            canvas._chartInstance = new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Reach',
                            data: reach,
                            borderColor: '#0369A1',
                            backgroundColor: 'rgba(3,105,161,0.07)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#0369A1',
                            fill: true,
                            tension: 0.4,
                        },
                        {
                            label: 'Engagements',
                            data: engs,
                            borderColor: '#7C3AED',
                            backgroundColor: 'rgba(124,58,237,0.07)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#7C3AED',
                            fill: true,
                            tension: 0.4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 10, padding: 16 } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 }, maxTicksLimit: 10 } },
                        y: { grid: { color: '#F1F5F9' }, ticks: { font: { size: 10 } } }
                    }
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initChart);
        } else {
            initChart();
        }

        document.addEventListener('livewire:updated', initChart);
    })();
    </script>
@endif

</div>
