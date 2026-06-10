<div>

@if(!$hasData)
    {{-- ── NO DATA STATE ───────────────────────────────────────────────────── --}}
    <div style="background:#F8FAFC; border:2px dashed #E2E8F0; border-radius:16px; padding:3rem 2rem; text-align:center;">
        <div style="width:52px;height:52px;background:#F5F3FF;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg style="width:24px;height:24px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <p style="font-size:0.95rem; font-weight:700; color:#0F172A; margin:0 0 0.5rem;">No analytics data yet</p>
        <p style="font-size:0.85rem; color:#64748B; margin:0 0 1.25rem; max-width:380px; margin-left:auto; margin-right:auto; line-height:1.6;">
            Publish posts and connect your platforms — Brandara will start tracking reach, engagement, and performance automatically.
        </p>
        <p style="font-size:0.75rem; color:#94A3B8; margin:0;">
            In development mode, run <code style="background:#F1F5F9;padding:2px 6px;border-radius:4px;font-size:0.72rem;">php artisan analytics:seed-fake {{ $brandSlug }}</code> to load sample data.
        </p>
    </div>

@else
    {{-- ── PERIOD SELECTOR ─────────────────────────────────────────────────── --}}
    <div style="display:flex; align-items:center; justify-content:flex-end; margin-bottom:1.25rem; gap:0.375rem;">
        @foreach([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days'] as $days => $label)
            <button type="button" wire:click="setPeriod({{ $days }})"
                style="padding:0.35rem 0.75rem; border-radius:7px; font-size:0.78rem; border:{{ $period === $days ? '2px solid #7C3AED' : '1px solid #E2E8F0' }}; background:{{ $period === $days ? '#F5F3FF' : '#fff' }}; color:{{ $period === $days ? '#7C3AED' : '#64748B' }}; cursor:pointer; font-weight:{{ $period === $days ? '600' : '400' }};">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── STAT CARDS ───────────────────────────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:0.875rem; margin-bottom:1.5rem;">

        @php
            $cards = [
                ['label' => 'Total reach',        'value' => number_format($summary['total_reach']),      'change' => $wow['reach_change'],      'color' => '#1D4ED8', 'bg' => '#EFF6FF'],
                ['label' => 'Total engagements',  'value' => number_format($summary['total_engagements']),'change' => $wow['engagement_change'], 'color' => '#7C3AED', 'bg' => '#F5F3FF'],
                ['label' => 'Avg engagement rate','value' => $summary['avg_engagement_rate'].'%',          'change' => null,                      'color' => '#0F766E', 'bg' => '#F0FDFA'],
                ['label' => 'Posts published',    'value' => $summary['total_posts'],                     'change' => null,                      'color' => '#B45309', 'bg' => '#FFFBEB'],
            ];
        @endphp

        @foreach($cards as $card)
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.125rem 1.25rem;">
                <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#94A3B8; margin:0 0 0.5rem;">{{ $card['label'] }}</p>
                <p style="font-size:1.625rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem; line-height:1;">{{ $card['value'] }}</p>
                @if($card['change'] !== null)
                    @php $up = $card['change'] > 0; $flat = $card['change'] == 0; @endphp
                    <p style="font-size:0.75rem; font-weight:600; margin:0; color:{{ $up ? '#16A34A' : ($flat ? '#94A3B8' : '#DC2626') }};">
                        {{ $up ? '↑' : ($flat ? '→' : '↓') }} {{ abs($card['change']) }}% vs last week
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ── ENGAGEMENT CHART ─────────────────────────────────────────────────── --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; margin-bottom:1.25rem;">
        <p style="font-size:0.85rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">Reach & Engagements</p>
        <canvas id="analyticsChart" height="90"></canvas>
    </div>

    {{-- ── PLATFORM BREAKDOWN + BEST TIMES ─────────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">

        {{-- Platform breakdown --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem;">
            <p style="font-size:0.85rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">By platform</p>
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
                    <div style="margin-bottom:0.75rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.25rem;">
                            <span style="font-size:0.8rem; font-weight:600; color:#374151;">{{ $pName }}</span>
                            <span style="font-size:0.78rem; color:#94A3B8;">{{ number_format($total) }}</span>
                        </div>
                        <div style="height:6px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $pColor }}; border-radius:99px; transition:width 0.5s;"></div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Best posting times --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem;">
            <p style="font-size:0.85rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Best times to post</p>
            <p style="font-size:0.72rem; color:#94A3B8; margin:0 0 1rem;">Based on when your posts get the most engagement.</p>
            @if(empty($bestTimes))
                <p style="font-size:0.82rem; color:#94A3B8; margin:0;">Not enough data yet.</p>
            @else
                @foreach($bestTimes as $i => $time)
                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.625rem;">
                        <span style="width:22px; height:22px; border-radius:6px; background:{{ $i === 0 ? '#F5F3FF' : '#F8FAFC' }}; color:{{ $i === 0 ? '#7C3AED' : '#94A3B8' }}; font-size:0.68rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $i + 1 }}</span>
                        <span style="font-size:0.875rem; font-weight:{{ $i === 0 ? '700' : '500' }}; color:{{ $i === 0 ? '#0F172A' : '#374151' }}; flex:1;">{{ $time['label'] }}</span>
                        <span style="font-size:0.75rem; color:#94A3B8;">avg {{ $time['avg_engagements'] }} engagements</span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- ── TOP POSTS ────────────────────────────────────────────────────────── --}}
    @if($topPosts->isNotEmpty())
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
            <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                <p style="font-size:0.85rem; font-weight:700; color:#0F172A; margin:0;">Top performing posts</p>
            </div>
            @foreach($topPosts as $i => $post)
                <div style="padding:0.875rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:center; gap:1rem;">
                    <span style="width:24px; height:24px; border-radius:7px; background:{{ $i === 0 ? '#7C3AED' : '#F1F5F9' }}; color:{{ $i === 0 ? '#fff' : '#94A3B8' }}; font-size:0.72rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0;">{{ $i + 1 }}</span>
                    <p style="flex:1; font-size:0.85rem; color:#374151; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ Str::limit($post->raw_input ?? 'Untitled post', 70) }}
                    </p>
                    <div style="text-align:right; flex-shrink:0;">
                        <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0;">{{ number_format($post->total_engagements) }}</p>
                        <p style="font-size:0.68rem; color:#94A3B8; margin:0;">engagements</p>
                    </div>
                    <span style="font-size:0.72rem; color:#94A3B8; flex-shrink:0;">{{ round($post->avg_rate, 1) }}%</span>
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
                            borderColor: '#1D4ED8',
                            backgroundColor: 'rgba(29,78,216,0.06)',
                            borderWidth: 2,
                            pointRadius: 2,
                            fill: true,
                            tension: 0.4,
                        },
                        {
                            label: 'Engagements',
                            data: engs,
                            borderColor: '#7C3AED',
                            backgroundColor: 'rgba(124,58,237,0.06)',
                            borderWidth: 2,
                            pointRadius: 2,
                            fill: true,
                            tension: 0.4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } } },
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
