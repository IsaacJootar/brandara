<div>

    {{-- ── MODE TOGGLE ─────────────────────────────────────────────────────── --}}
    <div x-data="{ activeMode: '{{ $mode }}' }"
         style="display:flex; gap:0.375rem; background:#F8FAFC; padding:0.375rem; border-radius:10px; border:1px solid #E2E8F0; margin-bottom:1.75rem; width:fit-content;">
        <button type="button"
            x-on:click="activeMode = 'carousel'; $wire.set('mode', 'carousel')"
            :style="activeMode === 'carousel'
                ? 'padding:0.4rem 1rem; border-radius:7px; font-size:0.8rem; font-weight:600; border:none; cursor:pointer; background:#fff; color:#7C3AED; box-shadow:0 1px 3px rgba(0,0,0,0.08);'
                : 'padding:0.4rem 1rem; border-radius:7px; font-size:0.8rem; font-weight:400; border:none; cursor:pointer; background:transparent; color:#475569;'">
            Carousel slides
        </button>
        <button type="button"
            x-on:click="activeMode = 'quote'; $wire.set('mode', 'quote')"
            :style="activeMode === 'quote'
                ? 'padding:0.4rem 1rem; border-radius:7px; font-size:0.8rem; font-weight:600; border:none; cursor:pointer; background:#fff; color:#7C3AED; box-shadow:0 1px 3px rgba(0,0,0,0.08);'
                : 'padding:0.4rem 1rem; border-radius:7px; font-size:0.8rem; font-weight:400; border:none; cursor:pointer; background:transparent; color:#475569;'">
            Quote &amp; cards
        </button>
    </div>

    {{-- ── IDLE / ERROR STATE ───────────────────────────────────────────────── --}}
    @if($status === 'idle' || $status === 'error')

        @if($status === 'error')
            <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.875rem 1rem; margin-bottom:1.25rem; font-size:0.85rem; color:#991B1B;">
                {{ $errorMessage }}
            </div>
        @endif

        {{-- CAROUSEL MODE --}}
        @if($mode === 'carousel')

            {{-- Context banner --}}
            <div style="background:#F5F3FF; border:1px solid #DDD6FE; border-radius:12px; padding:0.875rem 1rem; margin-bottom:1.5rem; display:flex; gap:0.75rem; align-items:flex-start;">
                <svg style="width:16px;height:16px;color:#7C3AED;flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p style="font-size:0.82rem; font-weight:600; color:#6D28D9; margin:0 0 0.2rem;">Carousels get the most reach on LinkedIn and Instagram</p>
                    <p style="font-size:0.78rem; color:#7C3AED; margin:0;">Write your topic below. Brandara writes every slide — you design it in Canva and post.</p>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:1.375rem;">

                {{-- Topic --}}
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">
                        What is your carousel about? <span style="color:#EF4444;">*</span>
                    </label>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.5rem;">A tip, insight, guide, client result, or framework your audience needs to hear.</p>
                    <textarea wire:model="topic" rows="3" maxlength="500"
                        placeholder="e.g. 5 pricing mistakes Nigerian consultants make — and how to fix them&#10;e.g. How we grew from 3 to 47 clients in 8 months"
                        class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:80px;"></textarea>
                    @error('topic')<p style="color:#EF4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                {{-- Platform + Structure --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">

                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Where are you posting?</label>
                        <p style="font-size:0.75rem; color:#94A3B8; margin:0 0 0.5rem;">Affects slide count and tone.</p>
                        <div x-data="{ active: '{{ $platform }}' }" style="display:flex; flex-direction:column; gap:0.3rem;">
                            @php
                                $platformHints = [
                                    'linkedin'  => '8–12 slides',
                                    'instagram' => '5–8 slides',
                                    'facebook'  => '6–10 slides',
                                ];
                            @endphp
                            @foreach($platforms as $key => $label)
                                <button type="button"
                                    x-on:click="active = '{{ $key }}'; $wire.set('platform', '{{ $key }}')"
                                    :style="active === '{{ $key }}'
                                        ? 'padding:0.45rem 0.875rem; border-radius:8px; font-size:0.8rem; font-weight:600; border:1px solid #7C3AED; background:#F5F3FF; color:#7C3AED; cursor:pointer; text-align:left; display:flex; align-items:center; justify-content:space-between;'
                                        : 'padding:0.45rem 0.875rem; border-radius:8px; font-size:0.8rem; border:1px solid #E2E8F0; background:#fff; color:#64748B; cursor:pointer; text-align:left; display:flex; align-items:center; justify-content:space-between;'">
                                    <span>{{ $label }}</span>
                                    <span style="font-size:0.68rem; opacity:0.6;">{{ $platformHints[$key] ?? '' }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">How should it flow?</label>
                        <p style="font-size:0.75rem; color:#94A3B8; margin:0 0 0.5rem;">Pick the story structure that fits.</p>
                        <div x-data="{ active: '{{ $structure }}' }" style="display:flex; flex-direction:column; gap:0.3rem;">
                            @php
                                $structureHints = [
                                    'problem-solution' => 'Pain point → fix',
                                    'step-by-step'     => 'How-to guide',
                                    'listicle'         => 'Numbered tips',
                                    'before-after'     => 'Transformation',
                                    'case-study'       => 'Client result',
                                ];
                            @endphp
                            @foreach($structures as $key => $label)
                                <button type="button"
                                    x-on:click="active = '{{ $key }}'; $wire.set('structure', '{{ $key }}')"
                                    :style="active === '{{ $key }}'
                                        ? 'padding:0.45rem 0.875rem; border-radius:8px; font-size:0.8rem; font-weight:600; border:1px solid #7C3AED; background:#F5F3FF; color:#7C3AED; cursor:pointer; text-align:left; display:flex; align-items:center; justify-content:space-between;'
                                        : 'padding:0.45rem 0.875rem; border-radius:8px; font-size:0.8rem; border:1px solid #E2E8F0; background:#fff; color:#64748B; cursor:pointer; text-align:left; display:flex; align-items:center; justify-content:space-between;'">
                                    <span>{{ $label }}</span>
                                    <span style="font-size:0.68rem; opacity:0.6;">{{ $structureHints[$key] ?? '' }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <button type="button" wire:click="generate"
                        wire:loading.attr="disabled" wire:target="generate"
                        style="padding:0.75rem 1.75rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <span wire:loading.remove wire:target="generate">Write my carousel slides</span>
                        <span wire:loading wire:target="generate" style="display:flex; align-items:center; gap:0.5rem;"><span class="btn-spinner"></span> Writing slides…</span>
                    </button>
                </div>
            </div>

        {{-- QUOTE / CARD MODE --}}
        @else

            {{-- Context banner --}}
            <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:12px; padding:0.875rem 1rem; margin-bottom:1.5rem; display:flex; gap:0.75rem; align-items:flex-start;">
                <svg style="width:16px;height:16px;color:#B45309;flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p style="font-size:0.82rem; font-weight:600; color:#92400E; margin:0 0 0.2rem;">Turn any text into 3 ready-to-design visual cards</p>
                    <p style="font-size:0.78rem; color:#B45309; margin:0;">Paste a post you wrote, a client result, or feedback you received. Brandara pulls out the most shareable copy for each card type.</p>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:1.375rem;">

                {{-- What you get preview --}}
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <span style="font-size:0.75rem; color:#7C3AED; background:#F5F3FF; border:1px solid #DDD6FE; padding:0.25rem 0.625rem; border-radius:99px; font-weight:500;">Quote card</span>
                    <span style="font-size:0.75rem; color:#16A34A; background:#F0FDF4; border:1px solid #BBF7D0; padding:0.25rem 0.625rem; border-radius:99px; font-weight:500;">Client testimonial</span>
                    <span style="font-size:0.75rem; color:#B45309; background:#FFFBEB; border:1px solid #FDE68A; padding:0.25rem 0.625rem; border-radius:99px; font-weight:500;">Motivational graphic</span>
                </div>

                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">
                        Paste your text <span style="color:#EF4444;">*</span>
                    </label>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.5rem;">A post, a client's feedback, a result you achieved, or words you want on a visual card.</p>
                    <textarea wire:model="quoteInput" rows="5" maxlength="2000"
                        placeholder="e.g. We helped a Lagos-based HR firm go from 3 to 47 retainer clients in 8 months by fixing one thing — their pricing model was invisible to the clients they actually wanted."
                        class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:100px;"></textarea>
                    @error('quoteInput')<p style="color:#EF4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <button type="button" wire:click="generate"
                        wire:loading.attr="disabled" wire:target="generate"
                        style="padding:0.75rem 1.75rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <span wire:loading.remove wire:target="generate">Create my cards</span>
                        <span wire:loading wire:target="generate" style="display:flex; align-items:center; gap:0.5rem;"><span class="btn-spinner"></span> Writing cards…</span>
                    </button>
                </div>
            </div>
        @endif

    {{-- ── GENERATING ───────────────────────────────────────────────────────── --}}
    @elseif($status === 'generating')
        <div style="text-align:center; padding:3rem 1.5rem;">
            <div style="width:48px;height:48px;background:#F5F3FF;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg style="width:24px;height:24px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <p style="font-size:0.95rem; font-weight:600; color:#0F172A; margin:0 0 0.375rem;">
                {{ $mode === 'carousel' ? 'Writing your slides…' : 'Creating your cards…' }}
            </p>
            <p style="font-size:0.82rem; color:#94A3B8; margin:0;">About 10–15 seconds.</p>
        </div>

    {{-- ── DONE ─────────────────────────────────────────────────────────────── --}}
    @elseif($status === 'done' && count($result))

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem;">
            @if($mode === 'carousel' && !empty($result['slides']))
                <p style="font-size:0.82rem; color:#64748B; margin:0;">
                    {{ $result['total_slides'] ?? count($result['slides']) }} slides ready
                    <span style="color:#CBD5E1; margin:0 0.375rem;">·</span>
                    Copy each slide, then design in Canva
                </p>
            @else
                <p style="font-size:0.82rem; color:#64748B; margin:0;">3 cards ready — copy the one you want to design</p>
            @endif
            <button type="button" wire:click="startOver"
                style="font-size:0.8rem; color:#64748B; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:0.4rem 0.875rem; cursor:pointer; display:flex; align-items:center; gap:0.375rem;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Start again
            </button>
        </div>

        {{-- CAROUSEL RESULTS --}}
        @if($mode === 'carousel' && !empty($result['slides']))

            <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:1.25rem;" x-data>
                @foreach($result['slides'] as $slide)
                    @php
                        $typeColor = match($slide['type'] ?? 'content') {
                            'hook' => ['bg' => '#FFF7ED', 'border' => '#FED7AA', 'label' => '#EA580C', 'badge' => '🎣 Hook — stops the scroll'],
                            'cta'  => ['bg' => '#F0FDF4', 'border' => '#BBF7D0', 'label' => '#16A34A', 'badge' => '✅ Call to action'],
                            default => ['bg' => '#F8FAFC', 'border' => '#E2E8F0', 'label' => '#64748B', 'badge' => 'Slide '.($slide['slide'] ?? '')],
                        };
                    @endphp
                    <div style="background:{{ $typeColor['bg'] }}; border:1px solid {{ $typeColor['border'] }}; border-radius:12px; padding:1rem 1.125rem;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                            <span style="font-size:0.7rem; font-weight:700; color:{{ $typeColor['label'] }};">
                                {{ $typeColor['badge'] }}
                            </span>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes(($slide['headline'] ?? '').((!empty($slide['body'])) ? '\n\n'.$slide['body'] : '')) }}'); $dispatch('show-toast', { message: 'Slide copied' })"
                                style="font-size:0.72rem; color:#7C3AED; background:#F5F3FF; border:1px solid #DDD6FE; border-radius:6px; padding:0.2rem 0.625rem; cursor:pointer; font-weight:500;">
                                Copy text
                            </button>
                        </div>
                        @if(!empty($slide['headline']))
                            <p style="font-size:0.925rem; font-weight:700; color:#0F172A; margin:0 0 0.375rem; line-height:1.4;">{{ $slide['headline'] }}</p>
                        @endif
                        @if(!empty($slide['body']))
                            <p style="font-size:0.85rem; color:#374151; margin:0 0 0.5rem; line-height:1.6; white-space:pre-line;">{{ $slide['body'] }}</p>
                        @endif
                        @if(!empty($slide['visual_note']))
                            <p style="font-size:0.72rem; color:#94A3B8; margin:0; font-style:italic;">
                                🎨 Canva design note: {{ $slide['visual_note'] }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!empty($result['canva_tip']))
                <div style="background:#F5F3FF; border:1px solid #DDD6FE; border-radius:10px; padding:0.875rem 1rem; display:flex; gap:0.625rem; margin-bottom:1.25rem;">
                    <svg style="width:15px;height:15px;color:#7C3AED;flex-shrink:0;margin-top:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.5 3.5 0 01-4.95 0l-.347-.347z"/>
                    </svg>
                    <p style="font-size:0.82rem; color:#6D28D9; margin:0; line-height:1.5;"><strong>Design tip:</strong> {{ $result['canva_tip'] }}</p>
                </div>
            @endif

            {{-- Design in Canva CTA --}}
            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
                <div>
                    <p style="font-size:0.85rem; font-weight:600; color:#0F172A; margin:0 0 0.2rem;">Ready to design?</p>
                    <p style="font-size:0.78rem; color:#64748B; margin:0;">Copy your slide text above → open Canva → paste into your template → post.</p>
                </div>
                <a href="https://www.canva.com/design/create?type=social_media" target="_blank" rel="noopener"
                   style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.65rem 1.25rem; background:#7D2AE8; color:#fff; font-size:0.85rem; font-weight:600; border-radius:9px; text-decoration:none; white-space:nowrap; transition:opacity 0.15s; flex-shrink:0;"
                   onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Open Canva
                    <svg style="width:12px;height:12px;opacity:0.8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>

        {{-- QUOTE CARD RESULTS --}}
        @elseif($mode === 'quote')
            <div style="display:flex; flex-direction:column; gap:1rem;" x-data>

                {{-- Founder quote --}}
                @if(!empty($result['quote_card']))
                    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                        <div style="background:#F5F3FF; border-bottom:1px solid #DDD6FE; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between;">
                            <div>
                                <span style="font-size:0.78rem; font-weight:700; color:#7C3AED;">Founder Quote Card</span>
                                <span style="font-size:0.72rem; color:#A78BFA; margin-left:0.5rem;">Your best line — put this on a branded image</span>
                            </div>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes('"'.($result['quote_card']['quote'] ?? '').'" — '.($result['quote_card']['attribution'] ?? '')) }}'); $dispatch('show-toast', { message: 'Copied' })"
                                style="font-size:0.72rem; color:#7C3AED; background:#EDE9FE; border:none; border-radius:6px; padding:0.2rem 0.625rem; cursor:pointer; font-weight:500; white-space:nowrap;">Copy text</button>
                        </div>
                        <div style="padding:1rem;">
                            <p style="font-size:1rem; font-weight:700; color:#0F172A; line-height:1.5; margin:0 0 0.5rem; font-style:italic;">"{{ $result['quote_card']['quote'] ?? '' }}"</p>
                            @if(!empty($result['quote_card']['attribution']))
                                <p style="font-size:0.8rem; color:#64748B; margin:0 0 0.5rem;">— {{ $result['quote_card']['attribution'] }}</p>
                            @endif
                            @if(!empty($result['quote_card']['visual_note']))
                                <p style="font-size:0.72rem; color:#94A3B8; margin:0; font-style:italic;">🎨 Canva: {{ $result['quote_card']['visual_note'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Testimonial card --}}
                @if(!empty($result['testimonial_card']))
                    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                        <div style="background:#F0FDF4; border-bottom:1px solid #BBF7D0; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between;">
                            <div>
                                <span style="font-size:0.78rem; font-weight:700; color:#16A34A;">Client Testimonial Card</span>
                                <span style="font-size:0.72rem; color:#4ADE80; margin-left:0.5rem;">Social proof — builds trust fast</span>
                            </div>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes('"'.($result['testimonial_card']['quote'] ?? '').'" — '.($result['testimonial_card']['name'] ?? '').'. '.($result['testimonial_card']['result'] ?? '')) }}'); $dispatch('show-toast', { message: 'Copied' })"
                                style="font-size:0.72rem; color:#16A34A; background:#DCFCE7; border:none; border-radius:6px; padding:0.2rem 0.625rem; cursor:pointer; font-weight:500; white-space:nowrap;">Copy text</button>
                        </div>
                        <div style="padding:1rem;">
                            <p style="font-size:0.95rem; color:#0F172A; line-height:1.55; margin:0 0 0.375rem; font-style:italic;">"{{ $result['testimonial_card']['quote'] ?? '' }}"</p>
                            @if(!empty($result['testimonial_card']['name']))
                                <p style="font-size:0.8rem; color:#64748B; margin:0 0 0.25rem;">— {{ $result['testimonial_card']['name'] }}</p>
                            @endif
                            @if(!empty($result['testimonial_card']['result']))
                                <p style="font-size:0.78rem; font-weight:600; color:#16A34A; margin:0 0 0.5rem;">✓ {{ $result['testimonial_card']['result'] }}</p>
                            @endif
                            @if(!empty($result['testimonial_card']['visual_note']))
                                <p style="font-size:0.72rem; color:#94A3B8; margin:0; font-style:italic;">🎨 Canva: {{ $result['testimonial_card']['visual_note'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Motivational card --}}
                @if(!empty($result['motivational_card']))
                    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                        <div style="background:#FFFBEB; border-bottom:1px solid #FDE68A; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between;">
                            <div>
                                <span style="font-size:0.78rem; font-weight:700; color:#B45309;">Motivational Graphic</span>
                                <span style="font-size:0.72rem; color:#D97706; margin-left:0.5rem;">Short, bold — designed to be shared</span>
                            </div>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes($result['motivational_card']['quote'] ?? '') }}'); $dispatch('show-toast', { message: 'Copied' })"
                                style="font-size:0.72rem; color:#B45309; background:#FEF3C7; border:none; border-radius:6px; padding:0.2rem 0.625rem; cursor:pointer; font-weight:500; white-space:nowrap;">Copy text</button>
                        </div>
                        <div style="padding:1rem;">
                            <p style="font-size:1.05rem; font-weight:800; color:#0F172A; line-height:1.4; margin:0 0 0.5rem;">{{ $result['motivational_card']['quote'] ?? '' }}</p>
                            @if(!empty($result['motivational_card']['visual_note']))
                                <p style="font-size:0.72rem; color:#94A3B8; margin:0; font-style:italic;">🎨 Canva: {{ $result['motivational_card']['visual_note'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Canva CTA --}}
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:0.875rem 1.25rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-top:0.25rem;">
                    <p style="font-size:0.78rem; color:#64748B; margin:0;">Copy the card text above → open Canva → paste into your design → post.</p>
                    <a href="https://www.canva.com/design/create?type=social_media" target="_blank" rel="noopener"
                       style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.55rem 1.1rem; background:#7D2AE8; color:#fff; font-size:0.82rem; font-weight:600; border-radius:8px; text-decoration:none; white-space:nowrap; transition:opacity 0.15s; flex-shrink:0;"
                       onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        Open Canva
                        <svg style="width:11px;height:11px;opacity:0.8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>

            </div>
        @endif
    @endif

</div>
