<!DOCTYPE html>
<html lang="en" data-theme="brandara">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $currentBrand->name }} — Brandara</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" style="background:#F8FAFC;">

    <div class="brandara-shell" id="appShell">

        {{-- Mobile scrim --}}
        <div class="brandara-scrim" id="sidebarScrim" onclick="closeSidebar()"></div>

        {{-- ── SIDEBAR ──────────────────────────────────────────────────── --}}
        <aside class="brandara-sidebar" id="appSidebar">

            {{-- ── Sidebar header: Brandara brand (not client name) ── --}}
            <div style="padding:1.1rem 1.1rem 0.9rem; border-bottom:1px solid rgba(255,255,255,0.08); position:relative; z-index:10; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:0.6rem; min-width:0;">
                    <img src="{{ asset('images/brandara-icon.svg') }}" style="width:26px; height:26px; flex-shrink:0;" alt="Brandara">
                    <div style="min-width:0;">
                        <div style="color:#fff; font-weight:700; font-size:0.95rem; letter-spacing:-0.01em; line-height:1.1;">Brandara</div>
                        {{-- Client/brand name sits below as context --}}
                        <div style="color:rgba(255,255,255,0.4); font-size:0.67rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:150px; margin-top:1px;">{{ $currentBrand->name }}</div>
                    </div>
                </div>

                {{-- ✕ Close button — mobile only. z-index:10 keeps it above glow pseudo-elements --}}
                <button
                    id="sidebarCloseBtn"
                    onclick="closeSidebar()"
                    aria-label="Close menu"
                    style="display:none; width:28px; height:28px; border-radius:50%; border:1px solid rgba(255,255,255,0.18); background:rgba(255,255,255,0.08); color:#f8fafc; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; z-index:10; position:relative;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="6" y1="6" x2="18" y2="18"/><line x1="18" y1="6" x2="6" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- ── Navigation ── --}}
            <nav style="flex:1; padding:0.625rem 0.625rem; overflow-y:auto; position:relative; z-index:1;">
                @php
                    $slug      = $currentBrand->slug;
                    $workspace = auth()->user()->workspace;
                    $plan      = $workspace->plan; // starter | pro | agency

                    /*
                     * Tier access map — controls which nav items show per plan.
                     * When billing is built (Phase 21), this drives what each plan unlocks.
                     * For now all items are visible on all plans during development.
                     *
                     * 'plans' => ['starter','pro','agency']  means all tiers see it
                     * 'plans' => ['pro','agency']            means starter cannot see it
                     */
                    $navItems = [
                        [
                            'section' => null,  // no section label for top items
                            'items' => [
                                ['route'=>'dashboard',   'label'=>'Dashboard',            'plans'=>['starter','pro','agency'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                            ],
                        ],
                        [
                            'section' => 'Content',
                            'items' => [
                                ['route'=>'create',      'label'=>'Create',               'plans'=>['starter','pro','agency'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
                                ['route'=>'plan',        'label'=>'Plan',                 'plans'=>['starter','pro','agency'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                                ['route'=>'schedule',    'label'=>'Schedule',             'plans'=>['starter','pro','agency'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                            ],
                        ],
                        [
                            'section' => 'Growth',
                            'items' => [
                                ['route'=>'grow',        'label'=>'Grow',                 'plans'=>['starter','pro','agency'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'],
                                ['route'=>'results',     'label'=>'Results',              'plans'=>['starter','pro','agency'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
                                ['route'=>'ai-presence', 'label'=>'AI Presence',          'plans'=>['pro','agency'],           'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>'],
                            ],
                        ],
                        [
                            'section' => 'Brand',
                            'items' => [
                                ['route'=>'my-brand',    'label'=>'My Brand',             'plans'=>['starter','pro','agency'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>'],
                                ['route'=>'connections', 'label'=>'Connections',          'plans'=>['starter','pro','agency'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>'],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($navItems as $section)
                    {{-- Section label --}}
                    @if ($section['section'])
                        <div style="font-size:0.62rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.28); padding:0.75rem 0.75rem 0.35rem; margin-top:0.25rem;">{{ $section['section'] }}</div>
                    @endif

                    @foreach ($section['items'] as $item)
                        @php
                            $active  = request()->routeIs($item['route']);
                            $allowed = in_array($plan, $item['plans']);
                        @endphp

                        @if ($allowed)
                            <a href="{{ route($item['route'], ['brand' => $slug]) }}"
                               onclick="closeSidebar()"
                               style="display:flex; align-items:center; gap:0.6rem; padding:0.55rem 0.75rem; border-radius:8px; font-size:0.835rem; font-weight:{{ $active ? '600' : '400' }}; text-decoration:none; margin-bottom:1px; transition:background 0.15s, color 0.15s; {{ $active ? 'background:linear-gradient(90deg,rgba(124,58,237,0.45),rgba(124,58,237,0.18)); color:#fff;' : 'color:rgba(255,255,255,0.52);' }}">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; {{ $active ? 'color:#a78bfa;' : '' }}">{!! $item['icon'] !!}</svg>
                                {{ $item['label'] }}
                            </a>
                        @else
                            {{-- Locked item — shown with lock icon, not clickable --}}
                            <div style="display:flex; align-items:center; gap:0.6rem; padding:0.55rem 0.75rem; border-radius:8px; font-size:0.835rem; color:rgba(255,255,255,0.22); cursor:default; margin-bottom:1px;" title="Upgrade to {{ $item['plans'][0] }} plan to unlock">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">{!! $item['icon'] !!}</svg>
                                {{ $item['label'] }}
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-left:auto; flex-shrink:0; opacity:0.5;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </div>
                        @endif
                    @endforeach
                @endforeach

                {{-- Brand switcher --}}
                @php $otherBrands = auth()->user()->workspace->brands()->where('id','!=',$currentBrand->id)->get(); @endphp
                @if ($otherBrands->isNotEmpty())
                    <div style="margin-top:0.875rem; padding-top:0.875rem; border-top:1px solid rgba(255,255,255,0.08);">
                        <div style="font-size:0.62rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.28); padding:0 0.75rem; margin-bottom:0.4rem;">Switch brand</div>
                        @foreach ($otherBrands as $other)
                            <a href="{{ route('dashboard', ['brand' => $other->slug]) }}"
                               onclick="closeSidebar()"
                               style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.82rem; color:rgba(255,255,255,0.45); text-decoration:none;">
                                <div style="width:18px; height:18px; border-radius:4px; background:rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; font-size:0.62rem; font-weight:700; color:rgba(255,255,255,0.6); flex-shrink:0;">{{ strtoupper(substr($other->name,0,1)) }}</div>
                                <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $other->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </nav>

            {{-- Trial banner --}}
            @if ($workspace->isTrialing())
                @php $daysLeft = $workspace->trialDaysLeft(); @endphp
                <div style="margin:0 0.625rem 0.625rem; padding:0.7rem 0.875rem; border-radius:10px; background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.22); position:relative; z-index:1;">
                    <div style="color:#fbbf24; font-size:0.73rem; font-weight:600;">{{ $daysLeft }} {{ Str::plural('day', $daysLeft) }} left on your trial</div>
                    <div style="color:rgba(255,255,255,0.35); font-size:0.68rem; margin-top:2px;">Upgrade to keep publishing</div>
                </div>
            @endif

            {{-- User footer --}}
            <div style="padding:0.875rem 0.875rem; border-top:1px solid rgba(255,255,255,0.08); position:relative; z-index:1;">
                <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:0.5rem;">
                    <div style="width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,#7C3AED,#A78BFA); display:flex; align-items:center; justify-content:center; font-size:0.72rem; font-weight:700; color:#fff; flex-shrink:0;">
                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                    </div>
                    <div style="min-width:0; flex:1;">
                        <div style="color:#fff; font-size:0.78rem; font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->name }}</div>
                        <div style="color:rgba(255,255,255,0.35); font-size:0.65rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="font-size:0.72rem; color:rgba(255,255,255,0.35); background:none; border:none; cursor:pointer; padding:0;">Sign out →</button>
                </form>
            </div>

        </aside>

        {{-- ── MAIN ──────────────────────────────────────────────────────── --}}
        <main class="brandara-main">

            {{-- Topbar --}}
            <header class="brandara-topbar">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <button class="brandara-hamburger" id="hamburgerBtn" onclick="openSidebar()" aria-label="Open menu">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <line x1="4" y1="7" x2="20" y2="7"/>
                            <line x1="4" y1="12" x2="20" y2="12"/>
                            <line x1="4" y1="17" x2="20" y2="17"/>
                        </svg>
                    </button>
                    <span style="font-size:0.875rem; font-weight:600; color:#0F172A;">{{ $currentBrand->name }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:0.625rem;">
                    {{-- Plan badge --}}
                    <span style="font-size:0.68rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; padding:0.25rem 0.6rem; border-radius:6px; background:#F5F3FF; color:#7C3AED;">
                        {{ ucfirst($workspace->plan) }}
                    </span>
                    {{-- Notification bell --}}
                    <button style="width:34px; height:34px; border-radius:8px; border:1px solid #E2E8F0; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#64748B;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                </div>
            </header>

            <div class="brandara-page">
                {{ $slot }}
            </div>

        </main>

    </div>

    <script>
        function openSidebar() {
            document.getElementById('appSidebar').classList.add('is-open');
            document.getElementById('sidebarScrim').classList.add('is-open');
        }

        function closeSidebar() {
            document.getElementById('appSidebar').classList.remove('is-open');
            document.getElementById('sidebarScrim').classList.remove('is-open');
        }

        // Show close button only on mobile
        function handleResize() {
            var btn = document.getElementById('sidebarCloseBtn');
            if (btn) {
                btn.style.display = window.innerWidth <= 1024 ? 'inline-flex' : 'none';
            }
        }
        window.addEventListener('resize', handleResize);
        handleResize();
    </script>

</body>
</html>
