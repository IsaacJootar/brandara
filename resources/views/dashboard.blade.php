<x-layouts.app>

    {{-- ── Page header ──────────────────────────────────────────────────────── --}}
    <div style="margin-bottom:1.75rem;">
        <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ explode(' ', auth()->user()->name)[0] }} 👋
        </h1>
        <p style="font-size:0.875rem; color:#64748B; margin:0;">
            Here's what's happening with <strong style="color:#0F172A;">{{ $brand->name }}</strong> today.
        </p>
    </div>

    {{-- ── Stat cards + activity sections (lazy-loaded) ────────────────────── --}}
    @livewire('dashboard.metrics', ['brand' => $brand])

    {{-- ── Quick actions ────────────────────────────────────────────────────── --}}
    <div style="margin-bottom:1.75rem;">
        <p style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#94A3B8; margin:0 0 0.75rem;">Quick actions</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:0.75rem;">
            @foreach([
                ['label'=>'Write a post',       'sub'=>'AI generates 3 versions',      'route'=>'create',       'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                ['label'=>'Plan content',        'sub'=>'Pillars & campaigns',          'route'=>'plan',         'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['label'=>'Check results',       'sub'=>'Reach & engagement',           'route'=>'results',      'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['label'=>'View trends',         'sub'=>'What\'s hot right now',        'route'=>'trends',       'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ] as $action)
                <a href="{{ route($action['route'], ['brand' => $brand->slug]) }}"
                   style="display:flex; align-items:center; gap:0.75rem; background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:0.875rem 1rem; text-decoration:none; transition:border-color 0.15s, box-shadow 0.15s;"
                   onmouseover="this.style.borderColor='#7C3AED';this.style.boxShadow='0 4px 12px rgba(124,58,237,0.08)'"
                   onmouseout="this.style.borderColor='#E2E8F0';this.style.boxShadow='none'">
                    <div style="width:36px; height:36px; border-radius:9px; background:#F5F3FF; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="16" height="16" fill="none" stroke="#7C3AED" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/></svg>
                    </div>
                    <div>
                        <div style="font-size:0.82rem; font-weight:600; color:#0F172A;">{{ $action['label'] }}</div>
                        <div style="font-size:0.72rem; color:#94A3B8;">{{ $action['sub'] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ── Get started — 3 steps ────────────────────────────────────────────── --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">

        {{-- Header --}}
        <div style="padding:1.125rem 1.5rem; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; gap:0.625rem;">
            <div style="width:28px; height:28px; border-radius:7px; background:#F5F3FF; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="14" height="14" fill="none" stroke="#7C3AED" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <div>
                <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0;">Get {{ $brand->name }} ready in 3 steps</p>
                <p style="font-size:0.75rem; color:#94A3B8; margin:0;">Complete these once and Brandara runs on its own</p>
            </div>
        </div>

        {{-- Steps --}}
        @php
            $steps = [
                [
                    'num'   => '1',
                    'title' => 'Set up your Brand',
                    'sub'   => 'Add your brand\'s voice, colours, mission, and target audience. This trains the AI to write exactly like you.',
                    'route' => 'my-brand',
                    'btn'   => 'Set up brand',
                    'color' => '#7C3AED',
                    'bg'    => '#F5F3FF',
                ],
                [
                    'num'   => '2',
                    'title' => 'Connect your platforms',
                    'sub'   => 'Link LinkedIn, X, Instagram, and more. Brandara schedules and publishes directly — no copy-pasting.',
                    'route' => 'connections',
                    'btn'   => 'Connect now',
                    'color' => '#0369A1',
                    'bg'    => '#EFF6FF',
                ],
                [
                    'num'   => '3',
                    'title' => 'Write your first post',
                    'sub'   => 'Enter a topic and let AI write 3 variations — Authority, Story, and Bold angle. Pick one, edit if you like, publish.',
                    'route' => 'create',
                    'btn'   => 'Write a post',
                    'color' => '#0F766E',
                    'bg'    => '#F0FDFA',
                ],
            ];
        @endphp

        @foreach($steps as $step)
            <div style="display:flex; gap:1.25rem; padding:1.375rem 1.5rem; {{ !$loop->last ? 'border-bottom:1px solid #F1F5F9;' : '' }} align-items:flex-start;">

                {{-- Number badge --}}
                <div style="flex-shrink:0; display:flex; flex-direction:column; align-items:center;">
                    <div style="width:36px; height:36px; border-radius:50%; background:{{ $step['bg'] }}; border:2px solid {{ $step['color'] }}20; display:flex; align-items:center; justify-content:center;">
                        <span style="font-size:0.875rem; font-weight:800; color:{{ $step['color'] }};">{{ $step['num'] }}</span>
                    </div>
                    @if(!$loop->last)
                        <div style="width:2px; height:100%; min-height:20px; background:#F1F5F9; margin-top:6px; border-radius:99px;"></div>
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex:1; min-width:0; padding-top:0.125rem;">
                    <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">{{ $step['title'] }}</p>
                    <p style="font-size:0.82rem; color:#64748B; margin:0 0 0.875rem; line-height:1.6;">{{ $step['sub'] }}</p>
                    <a href="{{ route($step['route'], ['brand' => $brand->slug]) }}"
                       style="display:inline-flex; align-items:center; gap:0.375rem; font-size:0.8rem; font-weight:600; color:{{ $step['color'] }}; background:{{ $step['bg'] }}; padding:0.45rem 1rem; border-radius:8px; text-decoration:none; border:1px solid {{ $step['color'] }}20;">
                        {{ $step['btn'] }}
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</x-layouts.app>
