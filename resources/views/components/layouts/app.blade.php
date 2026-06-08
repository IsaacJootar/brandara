<!DOCTYPE html>
<html lang="en" data-theme="brandara">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $currentBrand->name }} — Brandara</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('brandara-icon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('brandara-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @livewireStyles
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

            {{-- ── Navigation — driven by config/navigation.php, NOT hard-coded ── --}}
            <nav style="flex:1; padding:0.625rem 0.625rem; overflow-y:auto; position:relative; z-index:1;">
                @php
                    $slug      = $currentBrand->slug;
                    $workspace = auth()->user()->workspace;
                    $plan      = $workspace->plan;
                    // Load sections from config — single source of truth
                    $navSections = config('navigation.sections', []);
                @endphp

                @foreach ($navSections as $section)
                    {{-- Section label --}}
                    @if ($section['label'])
                        <div style="font-size:0.62rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.28); padding:0.75rem 0.75rem 0.35rem; margin-top:0.25rem;">{{ $section['label'] }}</div>
                    @endif

                    @foreach ($section['items'] as $item)
                        @php
                            $active  = request()->routeIs($item['route']);
                            $allowed = in_array($plan, $item['tiers']);
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
                            <div style="display:flex; align-items:center; gap:0.6rem; padding:0.55rem 0.75rem; border-radius:8px; font-size:0.835rem; color:rgba(255,255,255,0.22); cursor:default; margin-bottom:1px;" title="Upgrade to {{ $item['tiers'][0] }} plan to unlock">
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
                <form method="POST" action="{{ route('logout') }}" data-loading-form>
                    @csrf
                    <button type="submit" style="font-size:0.72rem; color:rgba(255,255,255,0.35); background:none; border:none; cursor:pointer; padding:0;">
                        <span class="btn-label">Sign out →</span>
                        <span class="btn-loading" style="display:none; align-items:center; gap:0.4rem;"><span class="btn-spinner"></span>Signing out…</span>
                    </button>
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
                    @livewire('notification-bell')
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

    @livewireScripts

    {{-- Global toast — triggered by Livewire dispatch('show-toast', message: '...') --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:show-toast.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="position:fixed; top:1.25rem; right:1.25rem; z-index:9999; display:flex; align-items:center; gap:0.5rem; background:#0F172A; color:#fff; font-size:0.85rem; font-weight:500; padding:0.75rem 1.125rem; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.18); min-width:220px; pointer-events:none;"
    >
        <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <span x-text="message"></span>
    </div>

    {{-- Web push subscription --}}
    @if (config('webpush.vapid.public_key'))
    <script>
    (function () {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

        navigator.serviceWorker.register('/sw.js').then(function (reg) {
            return reg.pushManager.getSubscription().then(function (sub) {
                if (sub) return; // Already subscribed

                const vapidKey = '{{ config('webpush.vapid.public_key') }}';
                const key = urlBase64ToUint8Array(vapidKey);

                return reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: key,
                }).then(function (subscription) {
                    return fetch('/push/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(subscription),
                    });
                });
            });
        }).catch(function () {});

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const raw     = window.atob(base64);
            return new Uint8Array([...raw].map(c => c.charCodeAt(0)));
        }
    })();
    </script>
    @endif
</body>
</html>
