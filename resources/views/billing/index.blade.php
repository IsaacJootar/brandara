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
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1rem; margin-bottom:2rem;">

            @php
                $planFeatures = [
                    'starter' => ['1 brand','30 AI generations/month','Basic · Facebook, LinkedIn, X','Schedule & publish posts','Media library (500MB)','Content pillars & campaigns'],
                    'pro'     => ['3 brands','Unlimited AI generations','All 7 platforms','Analytics & results dashboard','Lead tracker & engagement','Trends dashboard','2GB media storage','Weekly digest email'],
                    'agency'  => ['Unlimited brands','Unlimited AI generations','All 7 platforms','Everything in Growth','Client workspaces','Approval workflows','AI Visibility monitoring','10GB media storage','Priority support'],
                ];
                $planHighlights = ['starter' => false, 'pro' => true, 'agency' => false];
                $planColors = ['starter' => '#0369A1', 'pro' => '#7C3AED', 'agency' => '#0F766E'];
            @endphp

            @foreach(['starter' => 'Basic', 'pro' => 'Growth', 'agency' => 'Agency'] as $planKey => $planName)
                @php
                    $monthly = $plans['monthly']->firstWhere('plan', $planKey);
                    $yearly  = $plans['yearly']->firstWhere('plan', $planKey);
                    $isCurrent = $workspace->plan === $planKey;
                    $highlight = $planHighlights[$planKey];
                    $color = $planColors[$planKey];
                @endphp
                <div style="background:#fff; border:{{ $highlight ? '2px solid '.$color : '1px solid #E2E8F0' }}; border-radius:16px; overflow:hidden; box-shadow:{{ $highlight ? '0 4px 20px rgba(124,58,237,0.12)' : '0 1px 3px rgba(15,23,42,0.06)' }}; position:relative; display:flex; flex-direction:column;">

                    @if($highlight)
                        <div style="position:absolute; top:0; left:0; right:0; text-align:center; background:{{ $color }}; padding:5px; font-size:0.68rem; font-weight:700; color:#fff; letter-spacing:0.08em; text-transform:uppercase;">
                            Most popular
                        </div>
                    @endif

                    <div style="padding:{{ $highlight ? '2rem 1.5rem 1.5rem' : '1.5rem' }};">

                        {{-- Plan name + price --}}
                        <div style="margin-bottom:1.25rem;">
                            <p style="font-size:0.82rem; font-weight:700; color:{{ $color }}; text-transform:uppercase; letter-spacing:0.07em; margin:0 0 0.375rem;">{{ $planName }}</p>

                            @if($monthly)
                                <div x-show="interval === 'monthly'" style="display:flex; align-items:baseline; gap:0.25rem;">
                                    <span style="font-size:2rem; font-weight:800; color:#0F172A; line-height:1;">{{ $monthly->formattedAmount() }}</span>
                                    <span style="font-size:0.82rem; color:#94A3B8;">/month</span>
                                </div>
                            @endif

                            @if($yearly)
                                <div x-show="interval === 'yearly'" style="display:none; flex-direction:column; gap:0.125rem;">
                                    <div style="display:flex; align-items:baseline; gap:0.25rem;">
                                        <span style="font-size:2rem; font-weight:800; color:#0F172A; line-height:1;">{{ $yearly->formattedAmount() }}</span>
                                        <span style="font-size:0.82rem; color:#94A3B8;">/year</span>
                                    </div>
                                    @if($yearly->yearlySavings())
                                        <p style="font-size:0.75rem; color:#16A34A; font-weight:600; margin:0;">Save {{ $yearly->yearlySavings() }} vs monthly</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- CTA button --}}
                        @if($isCurrent)
                            <div style="width:100%; padding:0.65rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; text-align:center; font-size:0.85rem; font-weight:600; color:#64748B; margin-bottom:1.25rem;">
                                Your current plan
                            </div>
                        @else
                            <button type="button"
                                x-on:click="startCheckout('{{ $planKey }}', interval)"
                                style="width:100%; padding:0.65rem; background:{{ $color }}; color:#fff; border:none; border-radius:10px; font-size:0.875rem; font-weight:600; cursor:pointer; margin-bottom:1.25rem; transition:opacity 0.15s;"
                                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                {{ $workspace->plan === 'agency' ? 'Downgrade to '.$planName : 'Upgrade to '.$planName }}
                            </button>
                        @endif

                        {{-- Features --}}
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            @foreach($planFeatures[$planKey] as $feature)
                                <div style="display:flex; align-items:flex-start; gap:0.5rem;">
                                    <svg style="width:14px; height:14px; color:{{ $color }}; flex-shrink:0; margin-top:2px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span style="font-size:0.82rem; color:#374151;">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
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
