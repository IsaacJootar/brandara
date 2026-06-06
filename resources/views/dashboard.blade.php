<x-layouts.app>

    {{-- Page header --}}
    <div style="margin-bottom:1.75rem;">
        <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ explode(' ', auth()->user()->name)[0] }} 👋
        </h1>
        <p style="font-size:0.875rem; color:#64748B; margin:0;">
            Here's what's happening with <strong style="color:#0F172A;">{{ $brand->name }}</strong> today.
        </p>
    </div>

    {{-- Stat cards --}}
    <div class="metric-grid">
        <div class="metric-card metric-violet">
            <div class="metric-label">Posts published</div>
            <div class="metric-value">{{ $postsThisMonth }}</div>
            <div class="metric-sub">This month</div>
        </div>
        <div class="metric-card metric-blue">
            <div class="metric-label">Platforms connected</div>
            <div class="metric-value">{{ $activeConnections }}</div>
            <div class="metric-sub">Active connections</div>
        </div>
        <div class="metric-card metric-amber">
            <div class="metric-label">Warm leads</div>
            <div class="metric-value">{{ $warmLeads }}</div>
            <div class="metric-sub">Tracked this week</div>
        </div>
        <div class="metric-card metric-rose">
            <div class="metric-label">Engagement rate</div>
            <div class="metric-value">—</div>
            <div class="metric-sub">Connect platforms first</div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:1rem; margin-bottom:1.75rem;">
        <a href="{{ route('create', ['brand' => $brand->slug]) }}"
           style="display:block; background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; text-decoration:none; transition:border-color 0.15s, box-shadow 0.15s;"
           onmouseover="this.style.borderColor='#7C3AED'; this.style.boxShadow='0 4px 12px rgba(124,58,237,0.1)'"
           onmouseout="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'">
            <div style="width:40px; height:40px; border-radius:10px; background:#F5F3FF; display:flex; align-items:center; justify-content:center; margin-bottom:0.75rem;">
                <svg width="18" height="18" fill="none" stroke="#7C3AED" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div style="font-size:0.9rem; font-weight:600; color:#0F172A; margin-bottom:0.25rem;">Write a post</div>
            <div style="font-size:0.8rem; color:#64748B;">Generate AI content or write manually</div>
        </a>

        <a href="{{ route('connections', ['brand' => $brand->slug]) }}"
           style="display:block; background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; text-decoration:none; transition:border-color 0.15s, box-shadow 0.15s;"
           onmouseover="this.style.borderColor='#7C3AED'; this.style.boxShadow='0 4px 12px rgba(124,58,237,0.1)'"
           onmouseout="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'">
            <div style="width:40px; height:40px; border-radius:10px; background:#F5F3FF; display:flex; align-items:center; justify-content:center; margin-bottom:0.75rem;">
                <svg width="18" height="18" fill="none" stroke="#7C3AED" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <div style="font-size:0.9rem; font-weight:600; color:#0F172A; margin-bottom:0.25rem;">Connect platforms</div>
            <div style="font-size:0.8rem; color:#64748B;">Link LinkedIn, X, Instagram and more</div>
        </a>

        <a href="{{ route('my-brand', ['brand' => $brand->slug]) }}"
           style="display:block; background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; text-decoration:none; transition:border-color 0.15s, box-shadow 0.15s;"
           onmouseover="this.style.borderColor='#7C3AED'; this.style.boxShadow='0 4px 12px rgba(124,58,237,0.1)'"
           onmouseout="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'">
            <div style="width:40px; height:40px; border-radius:10px; background:#F5F3FF; display:flex; align-items:center; justify-content:center; margin-bottom:0.75rem;">
                <svg width="18" height="18" fill="none" stroke="#7C3AED" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <div style="font-size:0.9rem; font-weight:600; color:#0F172A; margin-bottom:0.25rem;">Set up My Brand</div>
            <div style="font-size:0.8rem; color:#64748B;">Add your voice, colours, and brand profile</div>
        </a>
    </div>

    {{-- Getting started checklist --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid #F1F5F9;">
            <div style="font-size:0.9rem; font-weight:600; color:#0F172A;">Get {{ $brand->name }} ready in 3 steps</div>
        </div>
        <div style="padding:1rem 1.5rem; display:flex; flex-direction:column; gap:0.75rem;">
            @foreach ([
                ['num'=>'1','title'=>'Set up your brand profile','sub'=>'Add your mission, voice, and colours','route'=>'my-brand','btn'=>'Start'],
                ['num'=>'2','title'=>'Connect your social platforms','sub'=>'LinkedIn, X, Instagram, and more','route'=>'connections','btn'=>'Connect'],
                ['num'=>'3','title'=>'Write your first post','sub'=>'Let AI write 3 versions for you','route'=>'create','btn'=>'Write a post'],
            ] as $step)
                <div style="display:flex; align-items:center; gap:0.875rem; padding:0.875rem 1rem; background:#F8FAFC; border-radius:10px;">
                    <div style="width:28px; height:28px; border-radius:50%; border:2px solid #E2E8F0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <span style="font-size:0.72rem; font-weight:700; color:#94A3B8;">{{ $step['num'] }}</span>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.85rem; font-weight:600; color:#0F172A;">{{ $step['title'] }}</div>
                        <div style="font-size:0.78rem; color:#94A3B8;">{{ $step['sub'] }}</div>
                    </div>
                    <a href="{{ route($step['route'], ['brand' => $brand->slug]) }}"
                       style="flex-shrink:0; font-size:0.78rem; font-weight:600; color:#fff; background:#7C3AED; padding:0.375rem 0.875rem; border-radius:7px; text-decoration:none; white-space:nowrap;">
                        {{ $step['btn'] }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>

</x-layouts.app>
