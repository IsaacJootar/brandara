<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Brandara Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; }
        .admin-shell { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 240px; background: #0F172A; color: #fff; padding: 1.25rem 0; flex-shrink: 0; position: fixed; top: 0; left: 0; bottom: 0; overflow-y: auto; }
        .admin-main { flex: 1; margin-left: 240px; padding: 1.5rem 2rem; }
        .admin-nav-item { display: flex; align-items: center; gap: 0.625rem; padding: 0.6rem 1.25rem; font-size: 0.835rem; color: #94A3B8; text-decoration: none; transition: all 0.15s; }
        .admin-nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .admin-nav-item.active { color: #fff; background: rgba(124,58,237,0.25); border-right: 3px solid #7C3AED; }
        .admin-card { background: #fff; border: 1px solid #E2E8F0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(15,23,42,0.06); }
        @media (max-width: 768px) {
            .admin-sidebar { width: 100%; position: relative; }
            .admin-main { margin-left: 0; }
            .admin-shell { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        {{-- Sidebar --}}
        <aside class="admin-sidebar">
            <div style="padding: 0 1.25rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 0.75rem;">
                <p style="font-size: 1rem; font-weight: 800; color: #fff; margin: 0;">Brandara</p>
                <p style="font-size: 0.7rem; color: #7C3AED; font-weight: 600; margin: 0.125rem 0 0;">Admin Panel</p>
            </div>

            @php $currentRoute = request()->route()?->getName(); @endphp

            <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.workspaces') }}" class="admin-nav-item {{ $currentRoute === 'admin.workspaces' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Workspaces
            </a>
            <a href="{{ route('admin.features') }}" class="admin-nav-item {{ $currentRoute === 'admin.features' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                Features & Tiers
            </a>
            <a href="{{ route('admin.billing') }}" class="admin-nav-item {{ $currentRoute === 'admin.billing' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Billing & Plans
            </a>
            <a href="{{ route('admin.ai') }}" class="admin-nav-item {{ $currentRoute === 'admin.ai' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                AI Settings
            </a>

            <div style="margin-top: auto; padding: 1.25rem; border-top: 1px solid rgba(255,255,255,0.08); position: absolute; bottom: 0; left: 0; right: 0;">
                <a href="{{ route('home') }}" style="font-size: 0.78rem; color: #64748B; text-decoration: none;">← Back to Brandara</a>
            </div>
        </aside>

        {{-- Main content --}}
        <main class="admin-main">
            {{ $slot }}
        </main>
    </div>

    {{-- Toast system --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:show-toast.window="message = $event.detail.message; type = $event.detail.type || 'success'; show = true; setTimeout(() => show = false, 4000)"
         x-show="show" x-transition
         style="position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; max-width:380px;"
         x-cloak>
        <div :style="'padding:0.75rem 1.25rem; border-radius:10px; font-size:0.82rem; font-weight:500; box-shadow:0 4px 12px rgba(0,0,0,0.15);' + (type === 'success' ? 'background:#0F172A; color:#fff;' : 'background:#DC2626; color:#fff;')"
             x-text="message"></div>
    </div>
</body>
</html>
