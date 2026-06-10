<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your weekly results — {{ $brandName }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#F8FAFC; margin:0; padding:24px 16px; color:#0F172A; }
        .wrap { max-width:540px; margin:0 auto; background:#fff; border-radius:16px; overflow:hidden; border:1px solid #E2E8F0; }
        .header { background:linear-gradient(135deg,#1B0D35,#0E0720); padding:28px 32px; }
        .logo { color:#fff; font-size:1.1rem; font-weight:700; letter-spacing:-.01em; }
        .body { padding:28px 32px; }
        h1 { font-size:1.1rem; font-weight:700; margin:0 0 6px; }
        .sub { font-size:0.85rem; color:#64748B; margin:0 0 24px; }
        .stats { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px; }
        .stat { background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:14px 16px; }
        .stat-val { font-size:1.5rem; font-weight:700; color:#0F172A; margin:0 0 2px; }
        .stat-lbl { font-size:0.72rem; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em; margin:0; }
        .stat-change { font-size:0.75rem; font-weight:600; margin-top:4px; }
        .up { color:#16A34A; } .down { color:#DC2626; } .flat { color:#94A3B8; }
        .cta { display:block; text-align:center; background:#7C3AED; color:#fff; padding:12px 24px; border-radius:10px; text-decoration:none; font-weight:600; font-size:0.9rem; margin-top:24px; }
        .footer { background:#F8FAFC; padding:16px 32px; font-size:0.75rem; color:#94A3B8; text-align:center; border-top:1px solid #E2E8F0; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div class="logo">⚡ Brandara</div>
        </div>
        <div class="body">
            <h1>Your results for {{ $brandName }} this week</h1>
            <p class="sub">{{ now()->subDays(7)->format('d M') }} – {{ now()->format('d M Y') }}</p>

            <div class="stats">
                <div class="stat">
                    <p class="stat-val">{{ number_format($summary['total_reach']) }}</p>
                    <p class="stat-lbl">Total reach</p>
                    @php $rc = $wow['reach_change']; @endphp
                    <p class="stat-change {{ $rc > 0 ? 'up' : ($rc < 0 ? 'down' : 'flat') }}">
                        {{ $rc > 0 ? '↑' : ($rc < 0 ? '↓' : '→') }} {{ abs($rc) }}% vs last week
                    </p>
                </div>
                <div class="stat">
                    <p class="stat-val">{{ number_format($summary['total_engagements']) }}</p>
                    <p class="stat-lbl">Engagements</p>
                    @php $ec = $wow['engagement_change']; @endphp
                    <p class="stat-change {{ $ec > 0 ? 'up' : ($ec < 0 ? 'down' : 'flat') }}">
                        {{ $ec > 0 ? '↑' : ($ec < 0 ? '↓' : '→') }} {{ abs($ec) }}% vs last week
                    </p>
                </div>
                <div class="stat">
                    <p class="stat-val">{{ $summary['avg_engagement_rate'] }}%</p>
                    <p class="stat-lbl">Avg engagement rate</p>
                </div>
                <div class="stat">
                    <p class="stat-val">{{ $summary['total_posts'] }}</p>
                    <p class="stat-lbl">Posts published</p>
                </div>
            </div>

            <a href="{{ config('app.url') }}/{{ $brandSlug }}/results" class="cta">
                See full results →
            </a>
        </div>
        <div class="footer">
            You're receiving this because you have a Brandara workspace. <br>
            © {{ date('Y') }} Brandara. Built for Africa.
        </div>
    </div>
</body>
</html>
