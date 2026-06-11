<div>

{{-- ── WELCOME HERO ───────────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#7C3AED 0%,#4F46E5 50%,#0369A1 100%); border-radius:16px; padding:1.75rem 1.5rem; margin-bottom:1.5rem; color:#fff; position:relative; overflow:hidden;">
    <div style="position:absolute; top:-30px; right:-30px; width:140px; height:140px; background:rgba(255,255,255,0.06); border-radius:50%;"></div>
    <div style="position:absolute; bottom:-20px; right:60px; width:80px; height:80px; background:rgba(255,255,255,0.04); border-radius:50%;"></div>
    <h2 style="font-size:1.25rem; font-weight:800; margin:0 0 0.5rem; position:relative;">Your brand's AI visibility journey</h2>
    <p style="font-size:0.85rem; margin:0 0 0.75rem; line-height:1.65; opacity:0.92; max-width:600px; position:relative;">
        When someone asks ChatGPT, Gemini, or any AI "who's the best {{ strtolower($brand->tagline ? explode(' ', $brand->tagline)[0] ?? 'business' : 'business') }} near me?" — does your brand show up?
        Most businesses don't. Brandara helps you fix that, step by step.
    </p>
    <div style="display:flex; gap:1.25rem; flex-wrap:wrap; font-size:0.78rem; opacity:0.85; position:relative;">
        <span>✓ Scan your website for AI-readiness</span>
        <span>✓ Fix technical gaps with one click</span>
        <span>✓ Track if AI mentions your brand over time</span>
    </div>
</div>

{{-- ── TAB SWITCHER ─────────────────────────────────────────────────────────── --}}
@php
    $tabConfig = [
        'readiness' => ['label' => 'AI Readiness',     'color' => '#7C3AED', 'activeBg' => '#7C3AED', 'lightBg' => '#F5F3FF', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'],
        'entity'    => ['label' => 'Entity Clarity',    'color' => '#0369A1', 'activeBg' => '#0369A1', 'lightBg' => '#F0F9FF', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
        'content'   => ['label' => 'Content Signals',   'color' => '#16A34A', 'activeBg' => '#16A34A', 'lightBg' => '#F0FDF4', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
        'quickfix'  => ['label' => 'Quick-Fix Assets',  'color' => '#D97706', 'activeBg' => '#D97706', 'lightBg' => '#FFFBEB', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>'],
        'presence'  => ['label' => 'Live AI Presence',  'color' => '#DC2626', 'activeBg' => '#DC2626', 'lightBg' => '#FEF2F2', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
    ];
@endphp
<div style="display:flex; gap:0.5rem; margin-bottom:1.5rem; flex-wrap:wrap;">
    @foreach($tabConfig as $tab => $cfg)
        <button type="button" wire:click="setTab('{{ $tab }}')"
            style="flex:1; min-width:130px; padding:0.6rem 0.75rem; border-radius:10px; font-size:0.78rem; font-weight:600; cursor:pointer; transition:all 0.15s; white-space:nowrap; display:flex; align-items:center; justify-content:center; gap:0.4rem;
            @if($activeTab === $tab)
                background:{{ $cfg['activeBg'] }}; color:#fff; border:2px solid {{ $cfg['activeBg'] }}; box-shadow:0 3px 10px {{ $cfg['activeBg'] }}30;
            @else
                background:{{ $cfg['lightBg'] }}; color:{{ $cfg['color'] }}; border:2px solid transparent;
            @endif">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">{!! $cfg['icon'] !!}</svg>
            {{ $cfg['label'] }}
        </button>
    @endforeach
</div>

{{-- ════════════════════════════════════════════════════════════════════════════
     SECTION 1 — AI READINESS SCORE
════════════════════════════════════════════════════════════════════════════ --}}
@if($activeTab === 'readiness')

    {{-- Score cards --}}
    @if($check)
        <div class="metric-grid" style="margin-bottom:1.5rem;">
            <div class="metric-card" style="background:linear-gradient(135deg,{{ $check->readinessColor() }},{{ $check->readinessColor() }}cc);">
                <div class="metric-label">AI Readiness Score</div>
                <div class="metric-value">{{ $check->score }}%</div>
                <div class="metric-sub">{{ $check->readinessLabel() }}</div>
            </div>
            <div class="metric-card metric-blue">
                <div class="metric-label">Technical checks</div>
                <div class="metric-value">{{ $check->tier1_passed + $check->tier2_passed }}</div>
                <div class="metric-sub">out of 20 passed</div>
            </div>
            <div class="metric-card metric-violet">
                <div class="metric-label">Entity checks</div>
                <div class="metric-value">{{ $check->tier3_passed }}</div>
                <div class="metric-sub">out of 5 confirmed</div>
            </div>
            <div class="metric-card metric-amber">
                <div class="metric-label">Last scanned</div>
                <div class="metric-value" style="font-size:1.1rem;">{{ $check->scanned_at?->diffForHumans() ?? '—' }}</div>
                <div class="metric-sub">{{ $check->website_url }}</div>
            </div>
        </div>
    @endif

    {{-- Scan form --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 0.25rem;">{{ $check ? 'Re-scan your website' : 'Scan your website' }}</p>
        <p style="font-size:0.8rem; color:#64748B; margin:0 0 1rem;">Brandara checks 20 signals that AI systems use to find and recommend your business. Enter the full URL including https:// (e.g. https://yourbusiness.com).</p>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <input type="url" wire:model="websiteUrl" placeholder="https://yourbusiness.com"
                style="flex:1; min-width:220px; padding:0.6rem 0.875rem; border:1px solid #E2E8F0; border-radius:9px; font-size:0.875rem; color:#0F172A; background:#F8FAFC; outline:none;"
                wire:keydown.enter="runScan">
            <button type="button" wire:click="runScan" wire:loading.attr="disabled"
                style="padding:0.6rem 1.25rem; background:#7C3AED; color:#fff; border:none; border-radius:9px; font-size:0.875rem; font-weight:600; cursor:pointer; white-space:nowrap;">
                <span wire:loading.remove wire:target="runScan">{{ $check ? 'Re-scan now' : 'Scan my site' }}</span>
                <span wire:loading wire:target="runScan">Scanning…</span>
            </button>
        </div>
        @error('websiteUrl') <p style="font-size:0.78rem; color:#DC2626; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
    </div>

    {{-- No website CTA --}}
    <div style="background:#F5F3FF; border:1px solid #DDD6FE; border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.25rem; display:flex; align-items:flex-start; gap:0.875rem; flex-wrap:wrap;">
        <div style="width:36px; height:36px; border-radius:9px; background:#7C3AED; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        </div>
        <div style="flex:1; min-width:200px;">
            <p style="font-size:0.835rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Don't have a website yet?</p>
            <p style="font-size:0.78rem; color:#64748B; margin:0; line-height:1.6;">A professional website is the foundation of AI visibility. Brandara can build you a modern, mobile-friendly business website that AI systems can find and trust.</p>
        </div>
    </div>

    @if($check)
        {{-- Tier 1 results --}}
        @php
            $tier1Keys = ['has_https','site_loads','has_title_tag','has_meta_description','has_canonical_tag',
                'has_json_ld_schema','has_faq_schema','has_about_page','has_contact_page',
                'has_contact_details_on_site','mentions_city','mentions_industry',
                'has_robots_txt','has_xml_sitemap','has_sameas_links'];
            $tier2Keys = ['page_indexable','ai_bots_allowed','canonical_matches_url','has_mobile_viewport','has_local_business_schema'];
            $results   = $check->results ?? [];
        @endphp

        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; margin-bottom:1rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                <p style="font-size:0.82rem; font-weight:700; color:#0F172A; margin:0;">Tier 1 — Core technical signals <span style="font-size:0.72rem; font-weight:400; color:#94A3B8;">(15 checks)</span></p>
                <p style="font-size:0.75rem; color:#94A3B8; margin:0.125rem 0 0;">These are the basics every AI-visible brand must have.</p>
            </div>
            @foreach($tier1Keys as $key)
                @php $status = $results[$key] ?? 'pending'; $def = $checkDefs[$key] ?? []; @endphp
                <div style="padding:0.875rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:flex-start; gap:0.875rem;">
                    <div style="width:22px; height:22px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; margin-top:1px;
                        background:{{ $status === 'pass' ? '#DCFCE7' : ($status === 'fail' ? '#FEE2E2' : '#F1F5F9') }};">
                        @if($status === 'pass')
                            <svg width="11" height="11" fill="none" stroke="#16A34A" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif($status === 'fail')
                            <svg width="11" height="11" fill="none" stroke="#DC2626" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                            <div style="width:6px;height:6px;border-radius:50%;background:#CBD5E1;"></div>
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:0.835rem; font-weight:{{ $status === 'fail' ? '600' : '500' }}; color:#0F172A; margin:0 0 0.125rem;">{{ $def['label'] ?? $key }}</p>
                        @if($status === 'fail')
                            <p style="font-size:0.78rem; color:#64748B; margin:0 0 0.25rem; line-height:1.5;">{{ $def['why'] ?? '' }}</p>
                            <p style="font-size:0.75rem; color:#7C3AED; font-weight:500; margin:0;">→ {{ $def['fix'] ?? '' }}</p>
                            @if(!empty($def['quick_fix']))
                                <button type="button" wire:click="setTab('quickfix')"
                                    style="margin-top:0.375rem; font-size:0.72rem; color:#7C3AED; background:#F5F3FF; border:1px solid #DDD6FE; border-radius:5px; padding:2px 8px; cursor:pointer; font-weight:600;">
                                    Generate fix in Brandara →
                                </button>
                            @endif
                        @endif
                    </div>
                    <span style="font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:99px; flex-shrink:0;
                        background:{{ $status === 'pass' ? '#DCFCE7' : ($status === 'fail' ? '#FEE2E2' : '#F1F5F9') }};
                        color:{{ $status === 'pass' ? '#16A34A' : ($status === 'fail' ? '#DC2626' : '#94A3B8') }};">
                        {{ strtoupper($status) }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Tier 2 --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                <p style="font-size:0.82rem; font-weight:700; color:#0F172A; margin:0;">Tier 2 — Advanced technical signals <span style="font-size:0.72rem; font-weight:400; color:#94A3B8;">(5 checks)</span></p>
            </div>
            @foreach($tier2Keys as $key)
                @php $status = $results[$key] ?? 'pending'; $def = $checkDefs[$key] ?? []; @endphp
                <div style="padding:0.875rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:flex-start; gap:0.875rem;">
                    <div style="width:22px; height:22px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; margin-top:1px;
                        background:{{ $status === 'pass' ? '#DCFCE7' : ($status === 'fail' ? '#FEE2E2' : '#F1F5F9') }};">
                        @if($status === 'pass') <svg width="11" height="11" fill="none" stroke="#16A34A" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif($status === 'fail') <svg width="11" height="11" fill="none" stroke="#DC2626" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else <div style="width:6px;height:6px;border-radius:50%;background:#CBD5E1;"></div> @endif
                    </div>
                    <div style="flex:1;">
                        <p style="font-size:0.835rem; font-weight:500; color:#0F172A; margin:0 0 0.125rem;">{{ $def['label'] ?? $key }}</p>
                        @if($status === 'fail')
                            <p style="font-size:0.75rem; color:#7C3AED; font-weight:500; margin:0;">→ {{ $def['fix'] ?? '' }}</p>
                        @endif
                    </div>
                    <span style="font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:99px; flex-shrink:0;
                        background:{{ $status === 'pass' ? '#DCFCE7' : ($status === 'fail' ? '#FEE2E2' : '#F1F5F9') }};
                        color:{{ $status === 'pass' ? '#16A34A' : ($status === 'fail' ? '#DC2626' : '#94A3B8') }};">
                        {{ strtoupper($status) }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
@endif

{{-- ════════════════════════════════════════════════════════════════════════════
     SECTION 2 — ENTITY CLARITY
════════════════════════════════════════════════════════════════════════════ --}}
@if($activeTab === 'entity')
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06); margin-bottom:1.25rem;">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9;">
            <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0;">Is AI confused about who you are?</p>
            <p style="font-size:0.78rem; color:#64748B; margin:0.25rem 0 0; line-height:1.5;">AI systems build a picture of your brand from signals across the web. Inconsistency confuses them. Tick each item as you complete it.</p>
        </div>

        @php
            $tier3Keys = [
                'has_google_business_profile' => ['label' => 'Google Business Profile claimed and verified', 'why' => 'The clearest local identity signal. AI answers about local businesses pull heavily from Google Business.', 'fix' => 'Go to business.google.com → claim your listing → verify by phone or postcard.', 'url' => 'https://business.google.com'],
                'nap_consistent'              => ['label' => 'Business name, address & phone are identical everywhere', 'why' => 'If your name is "ABC Ltd" on your website but "ABC Limited" on Google — AI sees two different businesses.', 'fix' => 'Check your website, Google Business, Facebook, LinkedIn, and directories. Make name, address, and phone identical.'],
                'has_ten_plus_reviews'        => ['label' => 'At least 10 genuine reviews online', 'why' => 'Reviews signal that real customers trust you. AI answers prefer businesses with proven credibility.', 'fix' => 'Ask 10 recent customers to leave a Google review. Make it easy — send them a direct link.'],
                'has_three_plus_listings'     => ['label' => 'Listed in 3+ trusted directories', 'why' => 'Multiple directory listings validate that your business is real and established.', 'fix' => 'See the Content Signals tab → directories list for your country.'],
                'social_profiles_linked'      => ['label' => 'Social profiles linked from your website', 'why' => 'Linked profiles help AI tie your website to your business entity across platforms.', 'fix' => 'Add your LinkedIn, Instagram, X, and Facebook links to your homepage footer.'],
            ];
            $manuals = $check?->manual_checks ?? [];
        @endphp

        @foreach($tier3Keys as $key => $def)
            @php $status = $manuals[$key] ?? 'pending'; @endphp
            <div style="padding:1rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:flex-start; gap:1rem;">
                {{-- Toggle --}}
                <div style="flex-shrink:0; padding-top:2px;">
                    <button type="button" wire:click="toggleManualCheck('{{ $key }}', {{ $status === 'pass' ? 'false' : 'true' }})"
                        style="width:36px; height:20px; border-radius:99px; border:none; cursor:pointer; transition:background 0.2s; position:relative; flex-shrink:0;
                               background:{{ $status === 'pass' ? '#7C3AED' : '#E2E8F0' }};">
                        <span style="position:absolute; top:2px; width:16px; height:16px; border-radius:50%; background:#fff; transition:left 0.2s;
                            left:{{ $status === 'pass' ? '18px' : '2px' }};"></span>
                    </button>
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 0.25rem;">{{ $def['label'] }}</p>
                    <p style="font-size:0.78rem; color:#64748B; margin:0 0 0.375rem; line-height:1.5;">{{ $def['why'] }}</p>
                    @if($status !== 'pass')
                        <p style="font-size:0.75rem; color:#7C3AED; font-weight:500; margin:0;">
                            → {{ $def['fix'] }}
                            @if(!empty($def['url']))
                                <a href="{{ $def['url'] }}" target="_blank" rel="noopener" style="color:#7C3AED; margin-left:4px;">Open →</a>
                            @endif
                        </p>
                    @endif
                </div>
                <span style="font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:99px; flex-shrink:0; white-space:nowrap;
                    background:{{ $status === 'pass' ? '#DCFCE7' : '#F1F5F9' }};
                    color:{{ $status === 'pass' ? '#16A34A' : '#94A3B8' }};">
                    {{ $status === 'pass' ? 'DONE' : 'PENDING' }}
                </span>
            </div>
        @endforeach
    </div>

    {{-- NAP reminder --}}
    <div style="background:#FFF7ED; border:1px solid #FED7AA; border-radius:12px; padding:1rem 1.25rem;">
        <p style="font-size:0.82rem; font-weight:600; color:#92400E; margin:0 0 0.25rem;">💡 NAP Consistency tip</p>
        <p style="font-size:0.78rem; color:#92400E; margin:0; line-height:1.6;">
            Your brand name on this platform is <strong>{{ $brand->name }}</strong>. Make sure this exact name appears on your website, Google Business Profile, all social profiles, and every directory listing. Even small differences like "Ltd" vs "Limited" weaken AI entity confidence.
        </p>
    </div>
@endif

{{-- ════════════════════════════════════════════════════════════════════════════
     SECTION 3 — CONTENT SIGNALS
════════════════════════════════════════════════════════════════════════════ --}}
@if($activeTab === 'content')

    {{-- Publishing signal from Brandara --}}
    @php $postCount = \App\Models\Post::where('brand_id', $brand->id)->where('status', 'published')->count(); @endphp
    <div class="metric-grid" style="margin-bottom:1.5rem;">
        <div class="metric-card metric-violet">
            <div class="metric-label">Posts published</div>
            <div class="metric-value">{{ $postCount }}</div>
            <div class="metric-sub">{{ $postCount >= 12 ? 'Strong signal ✓' : 'Publish more to build AI trust' }}</div>
        </div>
        <div class="metric-card metric-teal">
            <div class="metric-label">Content signal</div>
            <div class="metric-value" style="font-size:1.2rem;">{{ $postCount >= 20 ? 'Strong' : ($postCount >= 8 ? 'Building' : 'Weak') }}</div>
            <div class="metric-sub">AI needs consistent content to trust your brand</div>
        </div>
    </div>

    {{-- Content checklist --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06); margin-bottom:1.25rem;">
        <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #F1F5F9;">
            <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0;">Does AI have enough content to recommend you?</p>
            <p style="font-size:0.78rem; color:#64748B; margin:0.25rem 0 0;">Each question checks a signal AI uses to decide if your brand is trustworthy.</p>
        </div>
        @php
            $contentChecks = [
                [
                    'question' => 'Do you have an About page on your website?',
                    'result' => ($check?->results['has_about_page'] ?? 'pending'),
                    'step' => 'Go to the Quick-Fix Assets tab → Generate "About Page Copy" → Paste it on your website\'s About page.',
                    'action' => 'quickfix',
                ],
                [
                    'question' => 'Does your website have an FAQ section?',
                    'result' => ($check?->results['has_faq_schema'] ?? 'pending'),
                    'step' => 'Go to the Quick-Fix Assets tab → Generate "FAQ Schema" → Paste the code into your website\'s <head> tag.',
                    'action' => 'quickfix',
                ],
                [
                    'question' => 'Does your homepage mention your city and industry?',
                    'result' => ($check?->results['mentions_city'] ?? 'pending'),
                    'step' => 'Open your website homepage → Add your city name and industry to the headline or first paragraph. Example: "Digital marketing agency in Lagos".',
                ],
                [
                    'question' => 'Are you publishing at least 4 posts per month?',
                    'result' => ($postCount >= 4 ? 'pass' : 'fail'),
                    'step' => 'Go to Create in Brandara → Generate content → Schedule at least 1 post per week to build consistent AI trust.',
                ],
                [
                    'question' => 'Do you have a brand.md file on your website?',
                    'result' => (isset($assets['brand_markdown']) ? 'pass' : 'pending'),
                    'step' => 'Go to the Quick-Fix Assets tab → Generate "Brand Markdown" → Upload the file to your website root (yourdomain.com/brand.md).',
                    'action' => 'quickfix',
                ],
            ];
        @endphp
        @foreach($contentChecks as $item)
            <div style="padding:1rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F1F5F9;' : '' }} display:flex; align-items:flex-start; gap:0.875rem;">
                {{-- Status icon first --}}
                <div style="width:24px; height:24px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; margin-top:1px;
                    background:{{ $item['result'] === 'pass' ? '#DCFCE7' : ($item['result'] === 'fail' ? '#FEE2E2' : '#F1F5F9') }};">
                    @if($item['result'] === 'pass')
                        <svg width="12" height="12" fill="none" stroke="#16A34A" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @elseif($item['result'] === 'fail')
                        <svg width="12" height="12" fill="none" stroke="#DC2626" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    @else
                        <svg width="12" height="12" fill="none" stroke="#94A3B8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01"/></svg>
                    @endif
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="font-size:0.85rem; font-weight:600; color:#0F172A; margin:0 0 0.25rem;">{{ $item['question'] }}</p>
                    @if($item['result'] === 'pass')
                        <p style="font-size:0.78rem; color:#16A34A; font-weight:500; margin:0;">Yes — this signal is active.</p>
                    @else
                        <p style="font-size:0.78rem; color:#64748B; margin:0 0 0.375rem; line-height:1.6;"><strong style="color:#0F172A;">How to fix:</strong> {{ $item['step'] }}</p>
                        @if(!empty($item['action']))
                            <button type="button" wire:click="setTab('{{ $item['action'] }}')"
                                style="font-size:0.72rem; color:#7C3AED; background:#F5F3FF; border:1px solid #DDD6FE; border-radius:6px; padding:3px 10px; cursor:pointer; font-weight:600;">
                                Fix this now →
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Country directory guide --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #F1F5F9;">
            <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0;">Where to publish to get indexed by AI</p>
            <p style="font-size:0.78rem; color:#64748B; margin:0.25rem 0 0;">Publishing on these platforms increases the chance AI systems discover your brand. Your local directories are listed first — start with Low effort ones.</p>
        </div>
        @foreach($directories as $group)
            <div style="padding:1rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }}">
                <p style="font-size:0.72rem; font-weight:800; text-transform:uppercase; letter-spacing:0.07em; color:#94A3B8; margin:0 0 0.5rem;">{{ $group['category'] }}</p>
                <p style="font-size:0.78rem; color:#64748B; margin:0 0 0.75rem; line-height:1.5;">{{ $group['why'] }}</p>
                <div style="display:flex; flex-direction:column; gap:0.375rem;">
                    @foreach($group['items'] as $item)
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem;">
                            <a href="{{ $item['url'] }}" target="_blank" rel="noopener"
                                style="font-size:0.82rem; color:#0F172A; font-weight:500; text-decoration:none;">
                                {{ $item['name'] }}
                                <svg style="width:10px;height:10px;margin-left:3px;vertical-align:middle;color:#94A3B8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <span style="font-size:0.68rem; font-weight:600; padding:2px 7px; border-radius:99px;
                                background:{{ $item['effort'] === 'low' ? '#DCFCE7' : ($item['effort'] === 'medium' ? '#FEF9C3' : '#FEE2E2') }};
                                color:{{ $item['effort'] === 'low' ? '#16A34A' : ($item['effort'] === 'medium' ? '#D97706' : '#DC2626') }};">
                                {{ ucfirst($item['effort']) }} effort
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- ════════════════════════════════════════════════════════════════════════════
     SECTION 4 — QUICK-FIX ASSETS
════════════════════════════════════════════════════════════════════════════ --}}
@if($activeTab === 'quickfix')

    <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.25rem;">
        <p style="font-size:0.82rem; color:#166534; font-weight:600; margin:0 0 0.25rem;">How this works</p>
        <p style="font-size:0.78rem; color:#166534; margin:0; line-height:1.6;">
            Brandara generates the exact code or copy you need. You paste it on your website. Then come back here, click "Mark as published", and re-run the scan to watch the check turn green.
        </p>
    </div>

    @php
        $assetTypes = [
            'json_ld'               => ['label' => 'JSON-LD Schema', 'desc' => 'Machine-readable identity data. Paste into your website <head> tag. Helps AI understand your business as a defined entity.', 'icon' => '{ }', 'check' => 'has_json_ld_schema'],
            'local_business_schema' => ['label' => 'LocalBusiness Schema', 'desc' => 'More specific local identity markup. Signals your physical location and service area to AI systems.', 'icon' => '📍', 'check' => 'has_local_business_schema'],
            'faq_schema'            => ['label' => 'FAQ Schema', 'desc' => '5 questions and answers about your business in structured format. AI uses this to pull ready-made answers about you.', 'icon' => '❓', 'check' => 'has_faq_schema'],
            'about_copy'            => ['label' => 'About Page Copy', 'desc' => 'A professional About page in your brand voice. Copy this to your website to give AI a clear narrative about who you are.', 'icon' => '📄', 'check' => 'has_about_page'],
            'brand_markdown'        => ['label' => 'Brand Markdown File (brand.md)', 'desc' => 'A structured identity file placed at your website root (/brand.md). AI agents read this to understand your brand instantly.', 'icon' => '#️⃣', 'check' => null],
        ];
    @endphp

    <div style="display:flex; flex-direction:column; gap:1rem;">
        @foreach($assetTypes as $type => $info)
            @php $asset = $assets[$type] ?? null; @endphp
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
                <div style="padding:1rem 1.25rem; border-bottom:{{ $asset ? '1px solid #F1F5F9' : 'none' }}; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                    <div style="display:flex; align-items:flex-start; gap:0.75rem;">
                        <div style="width:36px; height:36px; border-radius:9px; background:#F5F3FF; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:0.9rem;">{{ $info['icon'] }}</div>
                        <div>
                            <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">{{ $info['label'] }}</p>
                            <p style="font-size:0.78rem; color:#64748B; margin:0; line-height:1.5; max-width:480px;">{{ $info['desc'] }}</p>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
                        @if($asset && $asset->status === 'published')
                            <span style="font-size:0.7rem; color:#16A34A; background:#DCFCE7; padding:3px 9px; border-radius:99px; font-weight:700;">Published ✓</span>
                        @endif
                        <button type="button" wire:click="generateAsset('{{ $type }}')" wire:loading.attr="disabled"
                            style="padding:0.45rem 1rem; background:{{ $asset ? '#F5F3FF' : '#7C3AED' }}; color:{{ $asset ? '#7C3AED' : '#fff' }}; border:{{ $asset ? '1px solid #DDD6FE' : 'none' }}; border-radius:8px; font-size:0.8rem; font-weight:600; cursor:pointer; white-space:nowrap;">
                            <span wire:loading.remove wire:target="generateAsset('{{ $type }}')">{{ $asset ? 'Regenerate' : 'Generate' }}</span>
                            <span wire:loading wire:target="generateAsset('{{ $type }}')">Generating…</span>
                        </button>
                    </div>
                </div>

                @if($asset)
                    <div style="padding:1rem 1.25rem;">
                        <div style="position:relative;" class="asset-wrap">
                            <pre style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:0.875rem 1rem; font-size:0.75rem; color:#374151; overflow-x:auto; margin:0; white-space:pre-wrap; word-break:break-all; max-height:220px; overflow-y:auto; font-family:'Courier New',monospace;">{{ $asset->content }}</pre>
                            <button type="button"
                                onclick="navigator.clipboard.writeText(this.closest('.asset-wrap').querySelector('pre').textContent).then(()=>{ this.textContent='Copied ✓'; this.style.background='#DCFCE7'; this.style.color='#16A34A'; this.style.borderColor='#BBF7D0'; setTimeout(()=>{ this.textContent='Copy'; this.style.background='#F5F3FF'; this.style.color='#7C3AED'; this.style.borderColor='#DDD6FE'; },2500); })"
                                style="position:absolute; top:8px; right:8px; font-size:0.72rem; color:#7C3AED; background:#F5F3FF; border:1px solid #DDD6FE; border-radius:5px; padding:2px 8px; cursor:pointer; font-weight:600; transition:all 0.2s;">Copy</button>
                        </div>
                        <div style="margin-top:0.75rem; display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                            <p style="font-size:0.75rem; color:#94A3B8; margin:0; flex:1;">
                                Generated {{ $asset->generated_at?->diffForHumans() }}.
                                Paste this on your website, then mark as published.
                            </p>
                            @if($asset->status !== 'published')
                                <button type="button" wire:click="markAssetPublished('{{ $asset->id }}')"
                                    style="font-size:0.75rem; color:#16A34A; background:#F0FDF4; border:1px solid #BBF7D0; border-radius:6px; padding:4px 10px; cursor:pointer; font-weight:600; white-space:nowrap;">
                                    ✓ Mark as published
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

{{-- ════════════════════════════════════════════════════════════════════════════
     SECTION 5 — LIVE AI PRESENCE
════════════════════════════════════════════════════════════════════════════ --}}
@if($activeTab === 'presence')

    {{-- Provider status --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 1rem;">AI providers</p>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:0.75rem;">
            @foreach([
                ['key' => 'claude',     'label' => 'Claude',      'color' => '#7C3AED'],
                ['key' => 'chatgpt',    'label' => 'ChatGPT',     'color' => '#16A34A'],
                ['key' => 'gemini',     'label' => 'Gemini',      'color' => '#0369A1'],
                ['key' => 'perplexity', 'label' => 'Perplexity',  'color' => '#94A3B8', 'soon' => true],
            ] as $p)
                @php $active = in_array($p['key'], $activeProviders) && empty($p['soon']); @endphp
                <div style="border:1px solid {{ $active ? $p['color'].'44' : '#E2E8F0' }}; border-radius:10px; padding:0.75rem 1rem; background:{{ $active ? $p['color'].'08' : '#F8FAFC' }};">
                    <p style="font-size:0.82rem; font-weight:700; color:{{ $active ? $p['color'] : '#94A3B8' }}; margin:0 0 0.25rem;">{{ $p['label'] }}</p>
                    <p style="font-size:0.72rem; margin:0; color:{{ $active ? '#64748B' : '#CBD5E1' }};">
                        @if(!empty($p['soon'])) Coming soon
                        @elseif($active) Active
                        @else Add API key to activate
                        @endif
                    </p>
                </div>
            @endforeach
        </div>

        @if(!empty($activeProviders))
            <div style="margin-top:1rem; display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
                @if($canScanPresence)
                    <button type="button" wire:click="runPresenceQuery('all')" wire:loading.attr="disabled"
                        style="padding:0.55rem 1.25rem; background:#7C3AED; color:#fff; border:none; border-radius:9px; font-size:0.85rem; font-weight:600; cursor:pointer;">
                        <span wire:loading.remove wire:target="runPresenceQuery">Run AI presence scan</span>
                        <span wire:loading wire:target="runPresenceQuery">Scanning all providers…</span>
                    </button>
                    <span style="font-size:0.75rem; color:#94A3B8;">You get 1 scan per month</span>
                @else
                    <button type="button" disabled
                        style="padding:0.55rem 1.25rem; background:#E2E8F0; color:#94A3B8; border:none; border-radius:9px; font-size:0.85rem; font-weight:600; cursor:not-allowed;">
                        Scan used this month
                    </button>
                    <span style="font-size:0.78rem; color:#64748B;">Next scan available on <strong>{{ $nextPresenceScanDate->format('M j, Y') }}</strong></span>
                @endif
            </div>
        @else
            <div style="margin-top:1rem; background:#FFF7ED; border:1px solid #FED7AA; border-radius:10px; padding:0.75rem 1rem;">
                <p style="font-size:0.82rem; color:#92400E; margin:0;">AI presence scans are not active yet. API keys will be connected automatically when your account is ready.</p>
            </div>
        @endif
    </div>

    {{-- Monitoring advice --}}
    <div style="background:linear-gradient(135deg,#F5F3FF,#EDE9FE); border:1px solid #DDD6FE; border-radius:14px; padding:1.25rem 1.5rem; margin-bottom:1.25rem;">
        <p style="font-size:0.875rem; font-weight:700; color:#5B21B6; margin:0 0 0.5rem;">AI visibility improves over time — not overnight</p>
        <p style="font-size:0.8rem; color:#6D28D9; margin:0 0 0.75rem; line-height:1.65;">
            Every time you publish content, update your website, or get listed in a directory, AI systems learn more about your brand. Your visibility score will grow month by month as you follow the steps in each tab. Let Brandara keep monitoring — run your monthly scan to track your progress.
        </p>
        <div style="display:flex; gap:1.25rem; flex-wrap:wrap; font-size:0.78rem; color:#7C3AED; font-weight:500;">
            <span>→ Publish consistently</span>
            <span>→ Fix technical gaps</span>
            <span>→ Scan monthly to track growth</span>
        </div>
    </div>

    @if($presenceSummary['has_data'])
        {{-- Score cards --}}
        <div class="metric-grid" style="margin-bottom:1.25rem;">
            <div class="metric-card metric-violet">
                <div class="metric-label">Visibility score</div>
                <div class="metric-value">{{ $presenceSummary['score'] }}%</div>
                <div class="metric-sub">{{ $presenceSummary['appeared'] }}/{{ $presenceSummary['total'] }} prompts mention you</div>
            </div>
            @foreach($presenceSummary['by_provider'] as $provider => $data)
                @php
                    $pColors = ['claude'=>'metric-violet','chatgpt'=>'metric-teal','gemini'=>'metric-blue'];
                    $pClass  = $pColors[$provider] ?? 'metric-amber';
                @endphp
                <div class="metric-card {{ $pClass }}">
                    <div class="metric-label">{{ ucfirst($provider) }}</div>
                    <div class="metric-value">{{ $data['score'] }}%</div>
                    <div class="metric-sub">{{ $data['appeared'] }}/{{ $data['total'] }} prompts</div>
                </div>
            @endforeach
        </div>

        {{-- Results list --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #F1F5F9; display:flex; justify-content:space-between; align-items:center;">
                <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0;">Recent AI query results</p>
                <p style="font-size:0.72rem; color:#94A3B8; margin:0;">Last scanned {{ $presenceSummary['last_queried']?->diffForHumans() }}</p>
            </div>
            @foreach($presenceSummary['results'] as $result)
                <div style="padding:0.875rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }} display:flex; align-items:flex-start; gap:0.875rem;">
                    <div style="width:20px; height:20px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; margin-top:2px;
                        background:{{ $result->appeared ? '#DCFCE7' : '#FEE2E2' }};">
                        @if($result->appeared)
                            <svg width="10" height="10" fill="none" stroke="#16A34A" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg width="10" height="10" fill="none" stroke="#DC2626" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:0.82rem; color:#0F172A; font-weight:500; margin:0 0 0.125rem;">{{ $result->prompt }}</p>
                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                            @php $pColors2 = ['claude'=>'#7C3AED','chatgpt'=>'#16A34A','gemini'=>'#0369A1']; @endphp
                            <span style="font-size:0.68rem; color:#fff; background:{{ $pColors2[$result->provider] ?? '#94A3B8' }}; padding:1px 7px; border-radius:99px; font-weight:600;">{{ ucfirst($result->provider) }}</span>
                            <span style="font-size:0.68rem; color:#94A3B8;">{{ ucfirst(str_replace('_',' ',$result->prompt_category)) }}</span>
                            @if($result->appeared)
                                <span style="font-size:0.68rem; color:{{ $result->sentiment === 'positive' ? '#16A34A' : ($result->sentiment === 'negative' ? '#DC2626' : '#64748B') }}; font-weight:600;">
                                    {{ ucfirst($result->sentiment) }}
                                </span>
                            @else
                                <span style="font-size:0.68rem; color:#94A3B8;">Not mentioned</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <div style="background:#F8FAFC; border:2px dashed #E2E8F0; border-radius:14px; padding:3rem 2rem; text-align:center;">
            <p style="font-size:0.95rem; font-weight:700; color:#0F172A; margin:0 0 0.5rem;">No AI presence data yet</p>
            <p style="font-size:0.82rem; color:#64748B; margin:0; line-height:1.6; max-width:380px; margin-left:auto; margin-right:auto;">
                Run a scan above to see whether {{ $brand->name }} appears when people ask AI about your industry.
            </p>
        </div>
    @endif
@endif

</div>
