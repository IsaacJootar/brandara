<x-layouts.app>

    @if(session('expired_reason'))
        <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.875rem 1.25rem; margin-bottom:1.5rem; font-size:0.875rem; color:#991B1B; font-weight:500;">
            {{ session('expired_reason') }}
        </div>
    @endif

    @if(session('success'))
        <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:0.875rem 1.25rem; margin-bottom:1.5rem; font-size:0.875rem; color:#166534; font-weight:500;">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Header ───────────────────────────────────────────────────────────── --}}
    <div style="margin-bottom:1.75rem;">
        <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Billing & Plans</h1>
        <p style="font-size:0.875rem; color:#64748B; margin:0;">
            Manage your subscription for <strong style="color:#0F172A;">{{ $workspace->name }}</strong>.
        </p>
    </div>

    {{-- ── Current plan + usage ─────────────────────────────────────────────── --}}
    @php
        $planLabels = ['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'];
        $currentLabel = $planLabels[$workspace->plan] ?? ucfirst($workspace->plan);
    @endphp
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-bottom:2rem;">

        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#94A3B8; margin:0 0 0.375rem;">Current plan</p>
            <p style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 0.25rem;">{{ $currentLabel }}</p>
            <p style="font-size:0.78rem; color:{{ $workspace->isTrialing() ? '#D97706' : '#16A34A' }}; margin:0; font-weight:500;">
                {{ $workspace->isTrialing() ? $workspace->trialDaysLeft().' days left on trial' : 'Active subscription' }}
            </p>
        </div>

        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#94A3B8; margin:0 0 0.375rem;">Brands</p>
            <p style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 0.25rem;">
                {{ $usage['brands_used'] }} / {{ $usage['brands_limit'] === 0 ? '∞' : $usage['brands_limit'] }}
            </p>
            <p style="font-size:0.78rem; color:#64748B; margin:0;">brands used</p>
        </div>

        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
            <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#94A3B8; margin:0 0 0.375rem;">AI generations</p>
            <p style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 0.25rem;">
                {{ $usage['generations_used'] }} / {{ $usage['generations_limit'] === 0 ? '∞' : $usage['generations_limit'] }}
            </p>
            <p style="font-size:0.78rem; color:#64748B; margin:0;">this month</p>
        </div>

        @if($sub)
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; box-shadow:0 1px 3px rgba(15,23,42,0.06);">
                <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:#94A3B8; margin:0 0 0.375rem;">Next renewal</p>
                <p style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 0.25rem;">
                    {{ $sub->current_period_end?->format('d M Y') ?? '—' }}
                </p>
                <p style="font-size:0.78rem; color:#64748B; margin:0;">via {{ ucfirst($sub->provider) }}</p>
            </div>
        @endif
    </div>

    {{-- ── Monthly / Yearly toggle + currency ──────────────────────────────── --}}
    <div x-data="{ interval: 'monthly' }">

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;">
            <div>
                <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Choose your plan</p>
                <p style="font-size:0.8rem; color:#64748B; margin:0;">Prices shown in <strong>{{ $currency }}</strong> · Switch plan anytime</p>
            </div>

            {{-- Interval toggle --}}
            <div style="display:flex; background:#F1F5F9; border-radius:10px; padding:0.25rem; gap:0.25rem;">
                <button type="button" @click="interval = 'monthly'"
                    :style="interval === 'monthly' ? 'background:#fff; color:#0F172A; box-shadow:0 1px 3px rgba(15,23,42,0.08); font-weight:600;' : 'background:transparent; color:#64748B;'"
                    style="padding:0.45rem 1.25rem; border-radius:8px; border:none; cursor:pointer; font-size:0.82rem; transition:all 0.15s;">
                    Monthly
                </button>
                <button type="button" @click="interval = 'yearly'"
                    :style="interval === 'yearly' ? 'background:#fff; color:#0F172A; box-shadow:0 1px 3px rgba(15,23,42,0.08); font-weight:600;' : 'background:transparent; color:#64748B;'"
                    style="padding:0.45rem 1.25rem; border-radius:8px; border:none; cursor:pointer; font-size:0.82rem; transition:all 0.15s;">
                    Yearly
                    <span style="margin-left:6px; font-size:0.68rem; font-weight:700; color:#16A34A; background:#DCFCE7; padding:1px 6px; border-radius:99px;">
                        {{ $settings['yearly_discount_label'] }}
                    </span>
                </button>
            </div>
        </div>

        {{-- ── Plan cards ───────────────────────────────────────────────────── --}}
        @php
            $planConfig = [
                'starter' => [
                    'name'      => 'Basic',
                    'gradient'  => 'linear-gradient(135deg, #0369A1 0%, #0891B2 100%)',
                    'tagline'   => 'For business owners building a consistent content presence with one brand.',
                    'popular'   => false,
                    'sections'  => [
                        ['label' => 'Content & Brand', 'items' => [
                            '1 brand',
                            'Content generation — 3 strategic angles per idea',
                            'Brand Voice training — paste samples, Brandara learns your style',
                            'Brand Kit & Brand Identity',
                            'TikTok Toolkit — script, caption, hashtags',
                            'WhatsApp Assistant — broadcast, promo, follow-up',
                            'Carousel & card copy generator',
                        ]],
                        ['label' => 'Platforms', 'items' => [
                            'Facebook, LinkedIn, X — automatic publishing',
                        ]],
                        ['label' => 'Planning & Publishing', 'items' => [
                            'Content pillars & campaign packs',
                            'Publishing calendar & scheduling',
                            'Media library (500 MB storage)',
                            'Post failure alerts & email notifications',
                            '30 content generations per month',
                        ]],
                    ],
                ],
                'pro' => [
                    'name'      => 'Growth',
                    'gradient'  => 'linear-gradient(135deg, #7C3AED 0%, #4338CA 100%)',
                    'tagline'   => 'For consultants and operators publishing across every channel and tracking real growth.',
                    'popular'   => true,
                    'sections'  => [
                        ['label' => 'Everything in Basic, plus', 'items' => [
                            '3 brands',
                            'All 7 platforms — LinkedIn, X, Facebook, Instagram, WhatsApp, TikTok & Threads',
                            'Unlimited content generations',
                            'Media library (2 GB storage)',
                            'Engagement & lead tracker',
                            'Trend monitoring & insights',
                            'AI Visibility reports — see where your brand appears in ChatGPT, Gemini & Perplexity',
                            'Analytics dashboard',
                        ]],
                    ],
                ],
                'agency' => [
                    'name'      => 'Agency',
                    'gradient'  => 'linear-gradient(135deg, #0F766E 0%, #059669 100%)',
                    'tagline'   => 'For agencies managing multiple client brands from one workspace.',
                    'popular'   => false,
                    'sections'  => [
                        ['label' => 'Everything in Growth, plus', 'items' => [
                            'Unlimited brands',
                            'Media library (10 GB storage)',
                            'Client workspaces — each brand fully isolated',
                            'Content review & client approvals workflow',
                            'White-label reports',
                            'Advanced AI Visibility — competitor tracking',
                            'Client AI visibility dashboards',
                            'Monthly AI visibility summary reports',
                            'Priority support',
                        ]],
                    ],
                ],
            ];
        @endphp

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1rem; margin-bottom:2rem; align-items:start;">
            @foreach($planConfig as $planKey => $plan)
                @php
                    $monthly   = $plans['monthly']->firstWhere('plan', $planKey);
                    $yearly    = $plans['yearly']->firstWhere('plan', $planKey);
                    $isCurrent = $workspace->plan === $planKey;
                @endphp

                <div style="background:{{ $plan['gradient'] }}; border-radius:16px; overflow:hidden; box-shadow:{{ $plan['popular'] ? '0 8px 32px rgba(124,58,237,0.25)' : '0 4px 16px rgba(15,23,42,0.12)' }}; position:relative; display:flex; flex-direction:column;">

                    @if($plan['popular'])
                        <div style="background:rgba(255,255,255,0.15); text-align:center; padding:5px; font-size:0.65rem; font-weight:800; color:#fff; letter-spacing:0.1em; text-transform:uppercase; backdrop-filter:blur(4px);">
                            ★ Most popular
                        </div>
                    @endif

                    <div style="padding:1.5rem;">

                        {{-- Plan name --}}
                        <p style="font-size:0.68rem; font-weight:700; color:rgba(255,255,255,0.65); text-transform:uppercase; letter-spacing:0.1em; margin:0 0 0.25rem;">{{ $plan['name'] }}</p>
                        <p style="font-size:0.78rem; color:rgba(255,255,255,0.7); margin:0 0 1.125rem; line-height:1.5;">{{ $plan['tagline'] }}</p>

                        {{-- Price --}}
                        @if($monthly)
                            <div x-show="interval === 'monthly'" style="margin-bottom:1.25rem;">
                                <div style="display:flex; align-items:baseline; gap:0.25rem;">
                                    <span style="font-size:2.25rem; font-weight:800; color:#fff; line-height:1;">{{ $monthly->formattedAmount() }}</span>
                                    <span style="font-size:0.8rem; color:rgba(255,255,255,0.6);">/month</span>
                                </div>
                            </div>
                        @endif
                        @if($yearly)
                            <div x-show="interval === 'yearly'" style="display:none; margin-bottom:1.25rem;">
                                <div style="display:flex; align-items:baseline; gap:0.25rem;">
                                    <span style="font-size:2.25rem; font-weight:800; color:#fff; line-height:1;">{{ $yearly->formattedAmount() }}</span>
                                    <span style="font-size:0.8rem; color:rgba(255,255,255,0.6);">/year</span>
                                </div>
                                @if($yearly->yearlySavings())
                                    <div style="display:inline-flex; align-items:center; gap:4px; margin-top:4px; background:rgba(255,255,255,0.15); padding:2px 8px; border-radius:99px;">
                                        <span style="font-size:0.72rem; color:#fff; font-weight:600;">Save {{ $yearly->yearlySavings() }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- CTA --}}
                        @if($isCurrent)
                            <div style="width:100%; padding:0.65rem; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); border-radius:10px; text-align:center; font-size:0.85rem; font-weight:700; color:#fff; margin-bottom:1.5rem; letter-spacing:0.01em;">
                                Your current plan ✓
                            </div>
                        @else
                            <button type="button"
                                x-on:click="startCheckout('{{ $planKey }}', interval)"
                                style="width:100%; padding:0.65rem; background:#fff; color:#0F172A; border:none; border-radius:10px; font-size:0.875rem; font-weight:700; cursor:pointer; margin-bottom:1.5rem; transition:opacity 0.15s; letter-spacing:0.01em;"
                                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                                {{ $workspace->plan === 'agency' && $planKey !== 'agency' ? 'Downgrade to '.$plan['name'] : 'Upgrade to '.$plan['name'] }}
                            </button>
                        @endif

                        {{-- Divider --}}
                        <div style="height:1px; background:rgba(255,255,255,0.15); margin-bottom:1.25rem;"></div>

                        {{-- Feature sections --}}
                        @foreach($plan['sections'] as $section)
                            <p style="font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.55); margin:0 0 0.625rem;">{{ $section['label'] }}</p>
                            <div style="display:flex; flex-direction:column; gap:0.5rem; margin-bottom:1rem;">
                                @foreach($section['items'] as $item)
                                    <div style="display:flex; align-items:flex-start; gap:0.5rem;">
                                        <svg style="width:13px; height:13px; color:rgba(255,255,255,0.8); flex-shrink:0; margin-top:2px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span style="font-size:0.8rem; color:rgba(255,255,255,0.85); line-height:1.5;">{{ $item }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <p style="font-size:0.72rem; color:rgba(255,255,255,0.45); margin:0.5rem 0 0; text-align:center;">✦ Cancel anytime · 7-day free trial</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Note about payment ───────────────────────────────────────────── --}}
        <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:1rem 1.25rem; text-align:center; margin-bottom:2rem;">
            <p style="font-size:0.82rem; color:#64748B; margin:0;">
                Payments are processed securely by <strong style="color:#0F172A;">Flutterwave</strong>.
                Cards from any country accepted. Cancel anytime — no questions asked.
            </p>
        </div>

    </div>

    {{-- ── Hidden plan ID map for JS ───────────────────────────────────────── --}}
    @php
        $jsPlanMap = $plans['monthly']->merge($plans['yearly'])
            ->map(function($p) { return ['id' => $p->id, 'plan' => $p->plan, 'interval' => $p->interval]; })
            ->values();
    @endphp
    <script>
    const brandaraPlans = {!! json_encode($jsPlanMap) !!};
    const brandaraProvider = {!! json_encode($provider) !!};
    const brandaraFwPublicKey = {!! json_encode($settings['flutterwave_public_key']) !!};
    const brandaraPsPublicKey = {!! json_encode($settings['paystack_public_key']) !!};
    const brandaraCheckoutUrl = {!! json_encode(route('billing.checkout')) !!};
    const brandaraCsrf = {!! json_encode(csrf_token()) !!};

    function startCheckout(plan, interval) {
        const match = brandaraPlans.find(p => p.plan === plan && p.interval === interval);
        if (!match) { alert('Plan not found. Please refresh and try again.'); return; }

        fetch(brandaraCheckoutUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': brandaraCsrf },
            body: JSON.stringify({ plan_id: match.id })
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) { alert(res.message || 'Could not start checkout. Please try again.'); return; }
            openPaymentPopup(res.data);
        })
        .catch(() => alert('Network error. Please check your connection and try again.'));
    }

    function openPaymentPopup(data) {
        if (brandaraProvider === 'flutterwave') {
            if (typeof FlutterwaveCheckout === 'undefined') {
                alert('Payment popup failed to load. Please refresh the page.');
                return;
            }
            FlutterwaveCheckout({
                public_key:     brandaraFwPublicKey || data.public_key,
                tx_ref:         data.tx_ref,
                amount:         data.amount,
                currency:       data.currency,
                customer:       data.customer,
                meta:           data.meta,
                customizations: data.customizations,
                callback: function(resp) {
                    if (resp.status === 'successful' || resp.status === 'completed') {
                        window.location = '{{ route('billing.verify') }}?tx_ref=' + resp.tx_ref + '&provider=flutterwave';
                    }
                },
                onclose: function() { /* user closed popup */ }
            });
        } else {
            if (typeof PaystackPop === 'undefined') {
                alert('Payment popup failed to load. Please refresh the page.');
                return;
            }
            const handler = PaystackPop.setup({
                key:       brandaraPsPublicKey || data.public_key,
                email:     data.email,
                amount:    data.amount,
                currency:  data.currency,
                ref:       data.tx_ref,
                metadata:  data.metadata,
                callback: function(resp) {
                    window.location = '{{ route('billing.verify') }}?reference=' + resp.reference + '&provider=paystack';
                },
                onClose: function() { /* user closed popup */ }
            });
            handler.openIframe();
        }
    }
    </script>

    {{-- Load payment SDKs --}}
    <script src="https://checkout.flutterwave.com/v3.js" defer></script>
    <script src="https://js.paystack.co/v1/inline.js" defer></script>

</x-layouts.app>
