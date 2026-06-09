<div>

    {{-- ── MODE TOGGLE ─────────────────────────────────────────────────────── --}}
    <div x-data="{ activeMode: '{{ $mode }}' }"
         style="display:flex; gap:0.375rem; background:#F8FAFC; padding:0.375rem; border-radius:10px; border:1px solid #E2E8F0; margin-bottom:1.5rem; width:fit-content;">
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
            Quote &amp; testimonial cards
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
            <div style="display:flex; flex-direction:column; gap:1.25rem;">

                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">
                        What is your carousel about? <span style="color:#EF4444;">*</span>
                    </label>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.5rem;">Topic, insight, guide, story, or framework you want to share.</p>
                    <textarea wire:model="topic" rows="3" maxlength="500"
                        placeholder="e.g. 5 pricing mistakes Nigerian consultants make — and how to fix them&#10;e.g. How we increased our client retention from 40% to 85% in one year"
                        class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:80px;"></textarea>
                    @error('topic')<p style="color:#EF4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Platform</label>
                        <div x-data="{ active: '{{ $platform }}' }" style="display:flex; flex-direction:column; gap:0.3rem;">
                            @foreach($platforms as $key => $label)
                                <button type="button"
                                    x-on:click="active = '{{ $key }}'; $wire.set('platform', '{{ $key }}')"
                                    :style="active === '{{ $key }}'
                                        ? 'padding:0.4rem 0.875rem; border-radius:8px; font-size:0.8rem; font-weight:600; border:1px solid #7C3AED; background:#F5F3FF; color:#7C3AED; cursor:pointer; text-align:left;'
                                        : 'padding:0.4rem 0.875rem; border-radius:8px; font-size:0.8rem; border:1px solid #E2E8F0; background:#fff; color:#64748B; cursor:pointer; text-align:left;'">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Structure</label>
                        <div x-data="{ active: '{{ $structure }}' }" style="display:flex; flex-direction:column; gap:0.3rem;">
                            @foreach($structures as $key => $label)
                                <button type="button"
                                    x-on:click="active = '{{ $key }}'; $wire.set('structure', '{{ $key }}')"
                                    :style="active === '{{ $key }}'
                                        ? 'padding:0.4rem 0.875rem; border-radius:8px; font-size:0.8rem; font-weight:600; border:1px solid #7C3AED; background:#F5F3FF; color:#7C3AED; cursor:pointer; text-align:left;'
                                        : 'padding:0.4rem 0.875rem; border-radius:8px; font-size:0.8rem; border:1px solid #E2E8F0; background:#fff; color:#64748B; cursor:pointer; text-align:left;'">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <button type="button" wire:click="generate"
                        wire:loading.attr="disabled" wire:target="generate"
                        style="padding:0.75rem 1.75rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem;">
                        <span wire:loading.remove wire:target="generate">Generate carousel slides</span>
                        <span wire:loading wire:target="generate" style="display:flex; align-items:center; gap:0.5rem;"><span class="btn-spinner"></span> Generating…</span>
                    </button>
                </div>
            </div>

        {{-- QUOTE MODE --}}
        @else
            <div style="display:flex; flex-direction:column; gap:1.25rem;">
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">
                        Paste your content <span style="color:#EF4444;">*</span>
                    </label>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.5rem;">A post, client feedback, your own words, or anything you want turned into shareable visual copy.</p>
                    <textarea wire:model="quoteInput" rows="5" maxlength="2000"
                        placeholder="e.g. We helped a Lagos-based HR firm go from 3 to 47 retainer clients in 8 months by fixing one thing..."
                        class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:100px;"></textarea>
                    @error('quoteInput')<p style="color:#EF4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <button type="button" wire:click="generate"
                        wire:loading.attr="disabled" wire:target="generate"
                        style="padding:0.75rem 1.75rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem;">
                        <span wire:loading.remove wire:target="generate">Generate card copy</span>
                        <span wire:loading wire:target="generate" style="display:flex; align-items:center; gap:0.5rem;"><span class="btn-spinner"></span> Generating…</span>
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
            <p style="font-size:0.95rem; font-weight:600; color:#0F172A; margin:0 0 0.375rem;">Writing your slides…</p>
            <p style="font-size:0.82rem; color:#94A3B8; margin:0;">One slide at a time. About 10–15 seconds.</p>
        </div>

    {{-- ── DONE ─────────────────────────────────────────────────────────────── --}}
    @elseif($status === 'done' && count($result))

        <div style="display:flex; justify-content:flex-end; margin-bottom:1.25rem;">
            <button type="button" wire:click="startOver"
                style="font-size:0.8rem; color:#64748B; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:0.4rem 0.875rem; cursor:pointer; display:flex; align-items:center; gap:0.375rem;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Generate again
            </button>
        </div>

        {{-- CAROUSEL RESULTS --}}
        @if($mode === 'carousel' && !empty($result['slides']))

            <div style="margin-bottom:0.875rem; display:flex; align-items:center; gap:0.75rem;">
                <span style="font-size:0.82rem; color:#64748B;">{{ $result['total_slides'] ?? count($result['slides']) }} slides</span>
                <span style="font-size:0.75rem; color:#94A3B8;">·</span>
                <span style="font-size:0.82rem; color:#64748B; text-transform:capitalize;">{{ $platforms[$result['platform']] ?? $result['platform'] }}</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:1.25rem;" x-data>
                @foreach($result['slides'] as $slide)
                    @php
                        $typeColor = match($slide['type'] ?? 'content') {
                            'hook' => ['bg' => '#FFF7ED', 'border' => '#FED7AA', 'label' => '#EA580C', 'badge' => 'Hook'],
                            'cta'  => ['bg' => '#F0FDF4', 'border' => '#BBF7D0', 'label' => '#16A34A', 'badge' => 'CTA'],
                            default => ['bg' => '#F8FAFC', 'border' => '#E2E8F0', 'label' => '#64748B', 'badge' => 'Slide '.($slide['slide'] ?? '')],
                        };
                    @endphp
                    <div style="background:{{ $typeColor['bg'] }}; border:1px solid {{ $typeColor['border'] }}; border-radius:12px; padding:1rem 1.125rem;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                            <span style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:{{ $typeColor['label'] }};">
                                {{ $typeColor['badge'] }}
                            </span>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes(($slide['headline'] ?? '').((!empty($slide['body'])) ? '\n\n'.$slide['body'] : '')) }}'); $dispatch('show-toast', { message: 'Slide copied' })"
                                style="font-size:0.7rem; color:#94A3B8; background:none; border:none; cursor:pointer; text-decoration:underline;">
                                Copy
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
                                <svg style="width:11px;height:11px;display:inline;margin-right:3px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Canva: {{ $slide['visual_note'] }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!empty($result['canva_tip']))
                <div style="background:#F5F3FF; border:1px solid #DDD6FE; border-radius:10px; padding:0.875rem 1rem; display:flex; gap:0.625rem; margin-bottom:1rem;">
                    <svg style="width:15px;height:15px;color:#7C3AED;flex-shrink:0;margin-top:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.5 3.5 0 01-4.95 0l-.347-.347z"/>
                    </svg>
                    <p style="font-size:0.82rem; color:#6D28D9; margin:0; line-height:1.5;"><strong>Canva tip:</strong> {{ $result['canva_tip'] }}</p>
                </div>
            @endif

            {{-- Design in Canva button --}}
            @php
                $allText = collect($result['slides'] ?? [])->map(fn($s) => ($s['headline'] ?? '')."\n".($s['body'] ?? ''))->implode("\n\n");
            @endphp
            <a href="https://www.canva.com/design/create?type=social_media" target="_blank" rel="noopener"
               style="display:inline-flex; align-items:center; gap:0.625rem; padding:0.7rem 1.25rem; background:#7D2AE8; color:#fff; font-size:0.875rem; font-weight:600; border-radius:10px; text-decoration:none; transition:opacity 0.15s;"
               onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <svg style="width:16px;height:16px;" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                </svg>
                Design in Canva
                <svg style="width:12px;height:12px;opacity:0.7;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
            <p style="font-size:0.72rem; color:#94A3B8; margin:0.375rem 0 0;">Opens Canva. Copy your slides text above, then paste into your design.</p>

        {{-- QUOTE CARD RESULTS --}}
        @elseif($mode === 'quote')
            <div style="display:flex; flex-direction:column; gap:1rem;" x-data>

                {{-- Founder quote --}}
                @if(!empty($result['quote_card']))
                    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                        <div style="background:#F5F3FF; border-bottom:1px solid #DDD6FE; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-size:0.78rem; font-weight:700; color:#7C3AED;">Founder Quote Card</span>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes('"'.($result['quote_card']['quote'] ?? '').'" — '.($result['quote_card']['attribution'] ?? '')) }}'); $dispatch('show-toast', { message: 'Quote copied' })"
                                style="font-size:0.72rem; color:#64748B; background:none; border:none; cursor:pointer; text-decoration:underline;">Copy</button>
                        </div>
                        <div style="padding:1rem;">
                            <p style="font-size:1rem; font-weight:700; color:#0F172A; line-height:1.5; margin:0 0 0.5rem; font-style:italic;">"{{ $result['quote_card']['quote'] ?? '' }}"</p>
                            @if(!empty($result['quote_card']['attribution']))
                                <p style="font-size:0.8rem; color:#64748B; margin:0 0 0.5rem;">— {{ $result['quote_card']['attribution'] }}</p>
                            @endif
                            @if(!empty($result['quote_card']['visual_note']))
                                <p style="font-size:0.72rem; color:#94A3B8; margin:0; font-style:italic;">Canva: {{ $result['quote_card']['visual_note'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Testimonial card --}}
                @if(!empty($result['testimonial_card']))
                    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                        <div style="background:#F0FDF4; border-bottom:1px solid #BBF7D0; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-size:0.78rem; font-weight:700; color:#16A34A;">Client Testimonial Card</span>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes('"'.($result['testimonial_card']['quote'] ?? '').'" — '.($result['testimonial_card']['name'] ?? '').'. Result: '.($result['testimonial_card']['result'] ?? '')) }}'); $dispatch('show-toast', { message: 'Testimonial copied' })"
                                style="font-size:0.72rem; color:#64748B; background:none; border:none; cursor:pointer; text-decoration:underline;">Copy</button>
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
                                <p style="font-size:0.72rem; color:#94A3B8; margin:0; font-style:italic;">Canva: {{ $result['testimonial_card']['visual_note'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Motivational card --}}
                @if(!empty($result['motivational_card']))
                    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                        <div style="background:#FFFBEB; border-bottom:1px solid #FDE68A; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-size:0.78rem; font-weight:700; color:#B45309;">Motivational Graphic</span>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes($result['motivational_card']['quote'] ?? '') }}'); $dispatch('show-toast', { message: 'Quote copied' })"
                                style="font-size:0.72rem; color:#64748B; background:none; border:none; cursor:pointer; text-decoration:underline;">Copy</button>
                        </div>
                        <div style="padding:1rem;">
                            <p style="font-size:1.05rem; font-weight:800; color:#0F172A; line-height:1.4; margin:0 0 0.5rem;">{{ $result['motivational_card']['quote'] ?? '' }}</p>
                            @if(!empty($result['motivational_card']['visual_note']))
                                <p style="font-size:0.72rem; color:#94A3B8; margin:0; font-style:italic;">Canva: {{ $result['motivational_card']['visual_note'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        @endif
    @endif

</div>
