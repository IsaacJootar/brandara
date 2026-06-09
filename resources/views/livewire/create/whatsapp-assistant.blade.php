<div>

    {{-- ── TYPE SELECTOR ────────────────────────────────────────────────────── --}}
    <div style="margin-bottom:1.75rem;">
        <p style="font-size:0.8rem; font-weight:600; color:#374151; margin:0 0 0.625rem;">What do you need to send?</p>
        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:0.5rem;">

            @php
                $typeDetails = [
                    'broadcast' => ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'hint' => 'Update your whole contact list'],
                    'status'    => ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'hint' => '24-hour visibility post'],
                    'promo'     => ['icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'hint' => 'Offer, sale, or new product'],
                    'follow_up' => ['icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'hint' => 'After a chat or purchase'],
                ];
            @endphp

            @foreach($types as $key => $label)
                @php
                    $detail  = $typeDetails[$key];
                    $active  = $type === $key;
                @endphp
                <button type="button"
                    wire:click="setType('{{ $key }}')"
                    style="padding:0.75rem 1rem; border-radius:12px; border:{{ $active ? '2px solid #25D366' : '1px solid #E2E8F0' }}; background:{{ $active ? '#F0FDF4' : '#fff' }}; cursor:pointer; text-align:left; display:flex; align-items:flex-start; gap:0.625rem; transition:border-color 0.15s, background 0.15s;">
                    <div style="width:32px;height:32px;border-radius:8px;background:{{ $active ? '#25D366' : '#F1F5F9' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background 0.15s;">
                        <svg style="width:16px;height:16px;color:{{ $active ? '#fff' : '#64748B' }};" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $detail['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:0.82rem;font-weight:{{ $active ? '700' : '600' }};color:{{ $active ? '#15803D' : '#374151' }};margin:0 0 0.125rem;">{{ $label }}</p>
                        <p style="font-size:0.72rem;color:#94A3B8;margin:0;">{{ $detail['hint'] }}</p>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── IDLE / ERROR ─────────────────────────────────────────────────────── --}}
    @if($status === 'idle' || $status === 'error')

        @if($status === 'error')
            <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.875rem 1rem; margin-bottom:1.25rem; font-size:0.85rem; color:#991B1B;">
                {{ $errorMessage }}
            </div>
        @endif

        {{-- Context banner based on type --}}
        @php
            $banners = [
                'broadcast' => ['text' => 'Write like you\'re personally updating someone who trusts you — not blasting a flyer.', 'color' => '#15803D', 'bg' => '#F0FDF4', 'border' => '#BBF7D0'],
                'status'    => ['text' => 'Status copy needs to stop the scroll in 3 seconds. One idea, one hook, done.', 'color' => '#0369A1', 'bg' => '#F0F9FF', 'border' => '#BAE6FD'],
                'promo'     => ['text' => 'Make the offer clear, make the deadline real, make the action obvious. No vague "DM me".', 'color' => '#B45309', 'bg' => '#FFFBEB', 'border' => '#FDE68A'],
                'follow_up' => ['text' => 'The goal is warmth + one soft next step. Do not be pushy. Do not write an essay.', 'color' => '#7C3AED', 'bg' => '#F5F3FF', 'border' => '#DDD6FE'],
            ];
            $banner = $banners[$type];
        @endphp
        <div style="background:{{ $banner['bg'] }}; border:1px solid {{ $banner['border'] }}; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.25rem; display:flex; gap:0.5rem; align-items:flex-start;">
            <svg style="width:14px;height:14px;color:{{ $banner['color'] }};flex-shrink:0;margin-top:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size:0.78rem; color:{{ $banner['color'] }}; margin:0; line-height:1.5;">{{ $banner['text'] }}</p>
        </div>

        {{-- Context input --}}
        @php
            $placeholders = [
                'broadcast'  => "e.g. We just launched a new 6-week consulting programme for Lagos-based HR managers. Starting 1st July. 5 spots only.\ne.g. Sharing a client win — we helped a client 3x their revenue in 4 months.",
                'status'     => "e.g. The one thing I wish someone told me before I started freelancing in Nigeria.\ne.g. We just hit 100 clients. Here's what we learned.",
                'promo'      => "e.g. 30% off our brand strategy session this week only. Usually ₦150,000, now ₦105,000. 3 slots left.\ne.g. New product: social media content pack for small businesses. ₦25,000/month.",
                'follow_up'  => "e.g. Someone enquired about our HR training yesterday. Want to follow up and share the programme details.\ne.g. Client just completed our 3-month strategy programme. Want to check in and offer next steps.",
            ];
        @endphp
        <div style="margin-bottom:1.25rem;">
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">
                Give Brandara a brief <span style="color:#EF4444;">*</span>
            </label>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.5rem;">Tell us what the message is about — your offer, news, or context. Brandara writes the actual WhatsApp message for you.</p>
            <textarea wire:model="context" rows="4" maxlength="1000"
                placeholder="{{ $placeholders[$type] }}"
                class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:90px;"></textarea>
            @error('context')<p style="color:#EF4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
        </div>

        <button type="button" wire:click="generate"
            wire:loading.attr="disabled" wire:target="generate"
            style="padding:0.75rem 1.75rem; background:#25D366; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <span wire:loading.remove wire:target="generate" style="display:flex; align-items:center; gap:0.5rem;">
                <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Write my WhatsApp message
            </span>
            <span wire:loading.flex wire:target="generate" style="display:none; align-items:center; gap:0.5rem;">
                <span class="btn-spinner"></span> Writing…
            </span>
        </button>

    {{-- ── GENERATING ───────────────────────────────────────────────────────── --}}
    @elseif($status === 'generating')
        <div style="text-align:center; padding:3rem 1.5rem;">
            <div style="width:48px;height:48px;background:#F0FDF4;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg style="width:24px;height:24px;color:#25D366;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <p style="font-size:0.95rem; font-weight:600; color:#0F172A; margin:0 0 0.375rem;">Writing your message…</p>
            <p style="font-size:0.82rem; color:#94A3B8; margin:0;">Keeping it personal and direct. About 8 seconds.</p>
        </div>

    {{-- ── DONE ─────────────────────────────────────────────────────────────── --}}
    @elseif($status === 'done' && count($result))

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem;">
            <p style="font-size:0.82rem; color:#64748B; margin:0;">2 variations ready — tap <strong style="color:#15803D;">Send on WhatsApp</strong> to open it pre-filled</p>
            <button type="button" wire:click="startOver"
                style="font-size:0.8rem; color:#64748B; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:0.4rem 0.875rem; cursor:pointer; display:flex; align-items:center; gap:0.375rem;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Write another
            </button>
        </div>

        {{-- Message variations --}}
        <div style="display:flex; flex-direction:column; gap:0.875rem; margin-bottom:1.5rem;" x-data>
            @foreach(($result['messages'] ?? []) as $i => $message)
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                    {{-- Card header --}}
                    <div style="background:#F0FDF4; border-bottom:1px solid #DCFCE7; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between; gap:0.5rem;">
                        <span style="font-size:0.78rem; font-weight:700; color:#15803D;">{{ $message['label'] ?? 'Variation '.($i+1) }}</span>
                        <button type="button"
                            x-on:click="navigator.clipboard.writeText('{{ addslashes($message['body'] ?? '') }}'); $dispatch('show-toast', { message: 'Message copied' })"
                            style="font-size:0.72rem; color:#64748B; background:#fff; border:1px solid #E2E8F0; border-radius:6px; padding:0.2rem 0.625rem; cursor:pointer; font-weight:500; white-space:nowrap; display:flex; align-items:center; gap:0.3rem;">
                            <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copy
                        </button>
                    </div>

                    {{-- Message body --}}
                    <div style="padding:1rem 1.125rem;">
                        <p style="font-size:0.9rem; color:#0F172A; line-height:1.7; margin:0; white-space:pre-line;">{{ $message['body'] ?? '' }}</p>
                        @if(!empty($message['emoji_note']))
                            <p style="font-size:0.72rem; color:#94A3B8; margin:0.625rem 0 0; font-style:italic;">
                                😊 {{ $message['emoji_note'] }}
                            </p>
                        @endif
                    </div>

                    {{-- Send on WhatsApp button --}}
                    <div style="padding:0.75rem 1.125rem; border-top:1px solid #F1F5F9;">
                        <a href="https://wa.me/?text={{ urlencode($message['body'] ?? '') }}"
                           target="_blank" rel="noopener"
                           style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.55rem 1.125rem; background:#25D366; color:#fff; font-size:0.85rem; font-weight:600; border-radius:8px; text-decoration:none; transition:opacity 0.15s;"
                           onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <svg style="width:16px;height:16px;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Send on WhatsApp
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Do's and don'ts --}}
        @if(!empty($result['do_tips']) || !empty($result['dont_tips']))
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">

                @if(!empty($result['do_tips']))
                    <div style="background:#F0FDF4; border:1px solid #DCFCE7; border-radius:12px; padding:0.875rem 1rem;">
                        <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#15803D; margin:0 0 0.5rem;">✓ Do this</p>
                        @foreach($result['do_tips'] as $tip)
                            <p style="font-size:0.8rem; color:#166534; margin:0 0 0.375rem; line-height:1.5;">• {{ $tip }}</p>
                        @endforeach
                    </div>
                @endif

                @if(!empty($result['dont_tips']))
                    <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:12px; padding:0.875rem 1rem;">
                        <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#DC2626; margin:0 0 0.5rem;">✕ Avoid this</p>
                        @foreach($result['dont_tips'] as $tip)
                            <p style="font-size:0.8rem; color:#991B1B; margin:0 0 0.375rem; line-height:1.5;">• {{ $tip }}</p>
                        @endforeach
                    </div>
                @endif

            </div>
        @endif

    @endif

</div>
