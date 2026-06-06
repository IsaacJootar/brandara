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

    {{-- ── Shell ─────────────────────────────────────────────────────────── --}}
    <div class="brandara-shell" id="appShell">

        {{-- Mobile scrim --}}
        <div class="brandara-scrim" id="sidebarScrim" onclick="closeSidebar()"></div>

        {{-- ── SIDEBAR ──────────────────────────────────────────────────── --}}
        <aside class="brandara-sidebar" id="appSidebar">

            {{-- Close button (mobile only) --}}
            <button class="sidebar-close-btn" onclick="closeSidebar()" aria-label="Close menu">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <line x1="6" y1="6" x2="18" y2="18"/><line x1="18" y1="6" x2="6" y2="18"/>
                </svg>
            </button>

            {{-- Logo + brand --}}
            <div style="padding:1.25rem 1.25rem 1rem; border-bottom:1px solid rgba(255,255,255,0.08); position:relative; z-index:1;">
                <div style="display:flex; align-items:center; gap:0.625rem;">
                    <img src="{{ asset('images/brandara-icon.svg') }}" class="brandara-logo-img" alt="Brandara">
                    <div style="min-width:0;">
                        <div style="color:#fff; font-weight:700; font-size:0.9rem; line-height:1.2;">{{ $currentBrand->name }}</div>
                        <div style="color:rgba(255,255,255,0.38); font-size:0.68rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->workspace->name }}</div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav style="flex:1; padding:0.75rem 0.75rem; overflow-y:auto; position:relative; z-index:1;">
                @php
                    $slug = $currentBrand->slug;
                    $navItems = [
                        ['route' => 'dashboard',   'label' => 'Dashboard',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                        ['route' => 'create',      'label' => 'Create',      'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
                        ['route' => 'plan',        'label' => 'Plan',        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                        ['route' => 'schedule',    'label' => 'Schedule',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                        ['route' => 'grow',        'label' => 'Grow',        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'],
                        ['route' => 'results',     'label' => 'Results',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
                        ['route' => 'my-brand',    'label' => 'My Brand',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>'],
                        ['route' => 'connections', 'label' => 'Connections', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>'],
                        ['route' => 'ai-presence', 'label' => 'AI Presence', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route'], ['brand' => $slug]) }}"
                       onclick="closeSidebar()"
                       style="display:flex; align-items:center; gap:0.625rem; padding:0.6rem 0.75rem; border-radius:8px; font-size:0.845rem; font-weight:{{ $active ? '600' : '400' }}; text-decoration:none; margin-bottom:1px; transition:background 0.15s, color 0.15s; {{ $active ? 'background:linear-gradient(90deg,rgba(124,58,237,0.45),rgba(124,58,237,0.18)); color:#fff;' : 'color:rgba(255,255,255,0.5);' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; {{ $active ? 'color:#a78bfa;' : '' }}">
                            {!! $item['icon'] !!}
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach

                {{-- Brand switcher for agencies --}}
                @php $otherBrands = auth()->user()->workspace->brands()->where('id','!=',$currentBrand->id)->get(); @endphp
                @if ($otherBrands->isNotEmpty())
                    <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid rgba(255,255,255,0.08);">
                        <div style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.28); padding:0 0.75rem; margin-bottom:0.5rem;">Switch brand</div>
                        @foreach ($otherBrands as $other)
                            <a href="{{ route('dashboard', ['brand' => $other->slug]) }}"
                               onclick="closeSidebar()"
                               style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.83rem; color:rgba(255,255,255,0.45); text-decoration:none; transition:background 0.15s, color 0.15s;">
                                <div style="width:20px; height:20px; border-radius:5px; background:rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; font-size:0.65rem; font-weight:700; color:rgba(255,255,255,0.6); flex-shrink:0;">
                                    {{ strtoupper(substr($other->name,0,1)) }}
                                </div>
                                <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $other->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </nav>

            {{-- Trial banner --}}
            @php $workspace = auth()->user()->workspace; @endphp
            @if ($workspace->isTrialing())
                @php $daysLeft = $workspace->trialDaysLeft(); @endphp
                <div style="margin:0 0.75rem 0.75rem; padding:0.75rem; border-radius:10px; background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.25); position:relative; z-index:1;">
                    <div style="color:#fbbf24; font-size:0.75rem; font-weight:600;">{{ $daysLeft }} {{ Str::plural('day', $daysLeft) }} left on your trial</div>
                    <div style="color:rgba(255,255,255,0.38); font-size:0.7rem; margin-top:2px;">Upgrade to keep publishing</div>
                </div>
            @endif

            {{-- User footer --}}
            <div style="padding:0.875rem 1rem; border-top:1px solid rgba(255,255,255,0.08); position:relative; z-index:1;">
                <div style="display:flex; align-items:center; gap:0.625rem; margin-bottom:0.625rem;">
                    <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#7C3AED,#A78BFA); display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; color:#fff; flex-shrink:0;">
                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                    </div>
                    <div style="min-width:0; flex:1;">
                        <div style="color:#fff; font-size:0.8rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                        <div style="color:rgba(255,255,255,0.38); font-size:0.68rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="font-size:0.75rem; color:rgba(255,255,255,0.38); background:none; border:none; cursor:pointer; padding:0; transition:color 0.15s;">
                        Sign out →
                    </button>
                </form>
            </div>

        </aside>

        {{-- ── MAIN ──────────────────────────────────────────────────────── --}}
        <main class="brandara-main">

            {{-- Topbar (always visible) --}}
            <header class="brandara-topbar">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <button class="brandara-hamburger" onclick="openSidebar()" aria-label="Open menu">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <line x1="4" y1="7" x2="20" y2="7"/>
                            <line x1="4" y1="12" x2="20" y2="12"/>
                            <line x1="4" y1="17" x2="20" y2="17"/>
                        </svg>
                    </button>
                    <span style="font-size:0.875rem; font-weight:600; color:#0F172A;">{{ $currentBrand->name }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    {{-- Notification bell placeholder --}}
                    <button style="width:2.25rem; height:2.25rem; border-radius:8px; border:1px solid #E2E8F0; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#64748B;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                </div>
            </header>

            {{-- Page content --}}
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
    </script>

</body>
</html>
