<x-layouts.app>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem;">
        <div>
            <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Results</h1>
            <p style="font-size:0.875rem; color:#64748B; margin:0;">How your content is performing across all platforms.</p>
        </div>
    </div>

    @php $onGrowth = in_array(currentPlan(), ['pro', 'agency']); @endphp

    @if(!$onGrowth)
        {{-- ── UPGRADE PROMPT — compelling preview for Basic users ─────────── --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; overflow:hidden; margin-bottom:1.5rem;">

            {{-- Blurred preview --}}
            <div style="position:relative; padding:1.5rem; filter:blur(3px); pointer-events:none; user-select:none;">
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:0.875rem; margin-bottom:1.25rem;">
                    @foreach([['14,820','Total reach'],['1,203','Engagements'],['4.2%','Eng. rate'],['12','Posts']] as $s)
                        <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:1rem;">
                            <p style="font-size:0.7rem; color:#94A3B8; text-transform:uppercase; margin:0 0 4px;">{{ $s[1] }}</p>
                            <p style="font-size:1.5rem; font-weight:700; color:#0F172A; margin:0;">{{ $s[0] }}</p>
                        </div>
                    @endforeach
                </div>
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:1rem; height:100px;"></div>
            </div>

            {{-- Overlay --}}
            <div style="position:absolute; left:0; right:0; margin-top:-13rem; z-index:10; text-align:center; padding:2.5rem 1.5rem;">
                <div style="background:rgba(255,255,255,0.96); border:1px solid #E2E8F0; border-radius:14px; padding:1.75rem 1.5rem; max-width:420px; margin:0 auto; box-shadow:0 4px 24px rgba(0,0,0,0.06);">
                    <div style="width:44px;height:44px;background:#F5F3FF;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.875rem;">
                        <svg style="width:20px;height:20px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <p style="font-size:1rem; font-weight:700; color:#0F172A; margin:0 0 0.5rem;">See exactly what's working</p>
                    <p style="font-size:0.82rem; color:#64748B; line-height:1.6; margin:0 0 1.25rem;">
                        Upgrade to Growth to unlock reach metrics, engagement rates, best posting times, top performing posts, and weekly digest emails sent every Monday.
                    </p>
                    <div style="display:flex; flex-direction:column; gap:0.375rem; margin-bottom:1.25rem; text-align:left;">
                        @foreach(['Reach, likes, comments & shares per post','Best times to post for your audience','Top 5 performing posts this month','Weekly email digest every Monday','Platform breakdown — which channel works best'] as $feat)
                            <p style="font-size:0.8rem; color:#374151; margin:0; display:flex; align-items:center; gap:0.5rem;">
                                <span style="color:#7C3AED; font-weight:700;">✓</span> {{ $feat }}
                            </p>
                        @endforeach
                    </div>
                    <a href="{{ route('home') }}"
                       style="display:block; padding:0.7rem 1.5rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border-radius:10px; text-decoration:none; text-align:center; transition:opacity 0.15s;"
                       onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Upgrade to Growth — $39/month
                    </a>
                    <p style="font-size:0.72rem; color:#CBD5E1; margin:0.625rem 0 0;">Cancel anytime.</p>
                </div>
            </div>
        </div>

    @else
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
            @livewire('analytics.results-dashboard', ['brand' => $currentBrand])
        </div>
    @endif

</x-layouts.app>
