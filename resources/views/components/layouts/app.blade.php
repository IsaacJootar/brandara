<!DOCTYPE html>
<html lang="en" data-theme="brandara">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $currentBrand->name }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-100 min-h-screen font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-20 lg:hidden"></div>

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="sidebar fixed top-0 left-0 h-full w-64 z-30 flex flex-col
                       transition-transform duration-200 lg:translate-x-0 lg:static lg:z-auto"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            {{-- Brand + workspace header --}}
            <div class="flex items-center gap-3 px-5 py-6 border-b border-white/10">
                <div class="logo-mark w-9 h-9 flex items-center justify-center shrink-0">
                    <span class="text-white font-bold text-base">B</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white font-bold text-sm leading-tight truncate">{{ $currentBrand->name }}</div>
                    <div class="text-white/40 text-xs truncate">{{ auth()->user()->workspace->name }}</div>
                </div>
                <button @click="sidebarOpen = false" class="text-white/40 hover:text-white lg:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                @php
                    $slug = $currentBrand->slug;
                    $navItems = [
                        ['route' => 'dashboard',   'icon' => 'home',              'label' => 'Dashboard'],
                        ['route' => 'create',      'icon' => 'pencil-square',     'label' => 'Create'],
                        ['route' => 'plan',        'icon' => 'calendar-days',     'label' => 'Plan'],
                        ['route' => 'schedule',    'icon' => 'clock',             'label' => 'Schedule'],
                        ['route' => 'grow',        'icon' => 'arrow-trending-up', 'label' => 'Grow'],
                        ['route' => 'results',     'icon' => 'chart-bar',         'label' => 'Results'],
                        ['route' => 'my-brand',    'icon' => 'star',              'label' => 'My Brand'],
                        ['route' => 'connections', 'icon' => 'link',              'label' => 'Connections'],
                        ['route' => 'ai-presence', 'icon' => 'cpu-chip',          'label' => 'AI Presence'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route'], ['brand' => $slug]) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ $active ? 'nav-item-active text-white' : 'text-white/45 hover:text-white hover:bg-white/5' }}">
                        <x-heroicon-o-{{ $item['icon'] }} class="w-5 h-5 shrink-0" />
                        {{ $item['label'] }}
                    </a>
                @endforeach

                {{-- Brand switcher for agencies --}}
                @php $otherBrands = auth()->user()->workspace->brands()->where('id', '!=', $currentBrand->id)->get(); @endphp
                @if ($otherBrands->isNotEmpty())
                    <div class="pt-3 mt-3 border-t border-white/10">
                        <p class="px-3 text-white/30 text-xs font-medium uppercase tracking-wider mb-1">Switch brand</p>
                        @foreach ($otherBrands as $other)
                            <a href="{{ route('dashboard', ['brand' => $other->slug]) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-white/45 hover:text-white hover:bg-white/5 transition-colors">
                                <div class="w-5 h-5 rounded bg-white/10 flex items-center justify-center text-xs font-bold text-white/60">
                                    {{ strtoupper(substr($other->name, 0, 1)) }}
                                </div>
                                <span class="truncate">{{ $other->name }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </nav>

            {{-- Trial banner --}}
            @php $workspace = auth()->user()->workspace; @endphp
            @if ($workspace->isTrialing())
                @php $daysLeft = $workspace->trialDaysLeft(); @endphp
                <div class="mx-3 mb-3 p-3 rounded-lg bg-yellow-500/10 border border-yellow-500/20">
                    <p class="text-yellow-400 text-xs font-medium">
                        {{ $daysLeft }} {{ Str::plural('day', $daysLeft) }} left on your trial
                    </p>
                    <p class="text-white/40 text-xs mt-0.5">Upgrade to keep publishing</p>
                </div>
            @endif

            {{-- User / logout --}}
            <div class="border-t border-white/10 px-3 py-4">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-8 h-8 rounded-full bg-[#7C3AED] flex items-center justify-center text-white text-xs font-semibold shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                        <div class="text-white/40 text-xs truncate">{{ auth()->user()->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log out" class="text-white/30 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="lg:hidden flex items-center gap-4 px-4 py-3 border-b border-base-300 sticky top-0 bg-base-100 z-10">
                <button @click="sidebarOpen = true" class="text-neutral">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="font-semibold text-neutral truncate">{{ $currentBrand->name }}</span>
            </header>
            <main class="flex-1 p-6 sm:p-8">{{ $slot }}</main>
        </div>

    </div>
</body>
</html>
