<div>

    {{-- ── IDLE / INPUT STATE ──────────────────────────────────────────────── --}}
    @if($status === 'idle' || $status === 'error')

        @if($status === 'error')
            <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.875rem 1rem; margin-bottom:1rem; display:flex; gap:0.5rem; align-items:flex-start;">
                <svg style="width:16px;height:16px;color:#EF4444;flex-shrink:0;margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-size:0.85rem; color:#991B1B; margin:0;">{{ $errorMessage }}</p>
            </div>
        @endif

        <div style="display:flex; flex-direction:column; gap:1.25rem;">

            {{-- Topic --}}
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">
                    What is your video about? <span style="color:#EF4444;">*</span>
                </label>
                <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.5rem;">A topic, product, tip, story, or question you want to talk about.</p>
                <textarea wire:model="topic" rows="3" maxlength="500"
                    placeholder="e.g. The biggest financial mistake Nigerian founders make when pricing their services&#10;e.g. How we grew from 0 to 100 clients in 12 months without ads&#10;e.g. Why you should invoice in USD even if you are based in Lagos"
                    class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:80px;"></textarea>
                @error('topic')<p style="color:#EF4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>

            {{-- Tone --}}
            <div x-data="{ activeTone: '{{ $tone }}' }">
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.5rem;">Tone</label>
                <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                    @foreach($tones as $key => $label)
                        <button type="button"
                            x-on:click="activeTone = '{{ $key }}'; $wire.setTone('{{ $key }}')"
                            :style="activeTone === '{{ $key }}'
                                ? 'padding:0.35rem 0.875rem; border-radius:99px; font-size:0.78rem; border:1px solid; cursor:pointer; transition:all 0.15s; background:#0F172A; color:#fff; border-color:#0F172A; font-weight:600;'
                                : 'padding:0.35rem 0.875rem; border-radius:99px; font-size:0.78rem; border:1px solid; cursor:pointer; transition:all 0.15s; background:#F8FAFC; color:#64748B; border-color:#E2E8F0;'">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Generate --}}
            <div>
                <button type="button" wire:click="generate"
                    wire:loading.attr="disabled" wire:target="generate"
                    style="padding:0.75rem 1.75rem; background:linear-gradient(135deg,#FE2C55,#EE1D52); color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <span wire:loading.remove wire:target="generate" style="display:flex; align-items:center; gap:0.5rem;">
                        <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.9L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                        Generate TikTok content
                    </span>
                    <span wire:loading.flex wire:target="generate" style="display:none; align-items:center; gap:0.5rem;">
                        <span class="btn-spinner"></span> Generating…
                    </span>
                </button>
            </div>

        </div>

    {{-- ── GENERATING STATE ────────────────────────────────────────────────── --}}
    @elseif($status === 'generating')
        <div style="text-align:center; padding:3rem 1.5rem;">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#FE2C55,#EE1D52);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg style="width:24px;height:24px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.9L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                </svg>
            </div>
            <p style="font-size:0.95rem; font-weight:600; color:#0F172A; margin:0 0 0.375rem;">Writing your TikTok content…</p>
            <p style="font-size:0.82rem; color:#94A3B8; margin:0;">Script, caption, overlays, and hashtags. About 10 seconds.</p>
        </div>

    {{-- ── DONE STATE ──────────────────────────────────────────────────────── --}}
    @elseif($status === 'done' && count($result))

        {{-- Regenerate button --}}
        <div style="display:flex; justify-content:flex-end; margin-bottom:1.25rem;">
            <button type="button" wire:click="startOver"
                style="font-size:0.8rem; font-weight:500; color:#64748B; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:0.4rem 0.875rem; cursor:pointer; display:flex; align-items:center; gap:0.375rem;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Generate again
            </button>
        </div>

        <div style="display:flex; flex-direction:column; gap:1rem;">

            {{-- Caption --}}
            @if(!empty($result['caption']))
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                    <div style="background:#FE2C551A; border-bottom:1px solid #FE2C5533; padding:0.625rem 1rem; display:flex; align-items:center; gap:0.5rem;">
                        <svg style="width:14px;height:14px;color:#EE1D52;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <span style="font-size:0.78rem; font-weight:700; color:#EE1D52;">Caption</span>
                    </div>
                    <div style="padding:1rem;" x-data>
                        <p style="font-size:0.9rem; color:#0F172A; line-height:1.6; margin:0 0 0.75rem;">{{ $result['caption'] }}</p>
                        <button type="button"
                            x-on:click="navigator.clipboard.writeText('{{ addslashes($result['caption']) }}'); $dispatch('show-toast', { message: 'Caption copied' })"
                            style="font-size:0.75rem; color:#64748B; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:0.3rem 0.75rem; cursor:pointer; display:flex; align-items:center; gap:0.3rem;">
                            <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copy caption
                        </button>
                    </div>
                </div>
            @endif

            {{-- Hashtags --}}
            @if(!empty($result['hashtags']) && count($result['hashtags']))
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                    <div style="background:#FE2C551A; border-bottom:1px solid #FE2C5533; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between; gap:0.5rem;">
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <svg style="width:14px;height:14px;color:#EE1D52;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            <span style="font-size:0.78rem; font-weight:700; color:#EE1D52;">Hashtags</span>
                        </div>
                        <button type="button" x-data
                            x-on:click="navigator.clipboard.writeText('{{ implode(' ', $result['hashtags']) }}'); $dispatch('show-toast', { message: 'Hashtags copied' })"
                            style="font-size:0.72rem; color:#64748B; background:none; border:none; cursor:pointer; text-decoration:underline;">
                            Copy all
                        </button>
                    </div>
                    <div style="padding:0.875rem 1rem; display:flex; flex-wrap:wrap; gap:0.375rem;">
                        @foreach($result['hashtags'] as $tag)
                            <span style="font-size:0.8rem; font-weight:500; color:#EE1D52; background:#FE2C551A; padding:0.25rem 0.625rem; border-radius:99px; border:1px solid #FE2C5533;">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Script --}}
            @if(!empty($result['script']))
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                    <div style="background:#FE2C551A; border-bottom:1px solid #FE2C5533; padding:0.625rem 1rem; display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <svg style="width:14px;height:14px;color:#EE1D52;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.9L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                            </svg>
                            <span style="font-size:0.78rem; font-weight:700; color:#EE1D52;">Video Script</span>
                        </div>
                        @if(!empty($result['script']['total_duration']))
                            <span style="font-size:0.72rem; color:#94A3B8;">⏱ {{ $result['script']['total_duration'] }}</span>
                        @endif
                    </div>
                    <div style="padding:1rem; display:flex; flex-direction:column; gap:0.875rem;" x-data>

                        @if(!empty($result['script']['hook_seconds_1_to_3']))
                            <div style="background:#FFF7ED; border:1px solid #FED7AA; border-radius:10px; padding:0.75rem 0.875rem;">
                                <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#EA580C; margin-bottom:0.375rem;">🎣 Hook (0–3 seconds)</div>
                                <p style="font-size:0.875rem; color:#0F172A; line-height:1.6; margin:0; font-style:italic;">"{{ $result['script']['hook_seconds_1_to_3'] }}"</p>
                            </div>
                        @endif

                        @if(!empty($result['script']['content_body']))
                            <div>
                                <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#64748B; margin-bottom:0.375rem;">📣 Main content</div>
                                <p style="font-size:0.875rem; color:#374151; line-height:1.7; margin:0; white-space:pre-line;">{{ $result['script']['content_body'] }}</p>
                            </div>
                        @endif

                        @if(!empty($result['script']['cta_closing']))
                            <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:0.75rem 0.875rem;">
                                <div style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#16A34A; margin-bottom:0.375rem;">✅ Call to action</div>
                                <p style="font-size:0.875rem; color:#0F172A; line-height:1.6; margin:0; font-style:italic;">"{{ $result['script']['cta_closing'] }}"</p>
                            </div>
                        @endif

                        <button type="button"
                            x-on:click="navigator.clipboard.writeText(`{{ addslashes(($result['script']['hook_seconds_1_to_3'] ?? '') . '\n\n' . ($result['script']['content_body'] ?? '') . '\n\n' . ($result['script']['cta_closing'] ?? '')) }}`); $dispatch('show-toast', { message: 'Script copied' })"
                            style="align-self:flex-start; font-size:0.75rem; color:#64748B; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:0.3rem 0.75rem; cursor:pointer; display:flex; align-items:center; gap:0.3rem;">
                            <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copy full script
                        </button>

                    </div>
                </div>
            @endif

            {{-- Text overlays --}}
            @if(!empty($result['text_overlays']) && count($result['text_overlays']))
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                    <div style="background:#FE2C551A; border-bottom:1px solid #FE2C5533; padding:0.625rem 1rem; display:flex; align-items:center; gap:0.5rem;">
                        <svg style="width:14px;height:14px;color:#EE1D52;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                        </svg>
                        <span style="font-size:0.78rem; font-weight:700; color:#EE1D52;">Text Overlays</span>
                        <span style="font-size:0.7rem; color:#94A3B8;">— what to show on screen while you speak</span>
                    </div>
                    <div style="padding:0.875rem 1rem;">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#94A3B8; padding:0 0 0.5rem; width:100px;">Timing</th>
                                    <th style="text-align:left; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#94A3B8; padding:0 0 0.5rem;">Overlay text</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result['text_overlays'] as $overlay)
                                    <tr style="border-top:1px solid #F1F5F9;">
                                        <td style="padding:0.5rem 0.5rem 0.5rem 0; font-size:0.75rem; color:#94A3B8; white-space:nowrap; font-family:monospace;">{{ $overlay['timing'] ?? '' }}</td>
                                        <td style="padding:0.5rem 0; font-size:0.875rem; font-weight:600; color:#0F172A;">{{ $overlay['text'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Bio copy --}}
            @if(!empty($result['bio_copy']))
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                    <div style="background:#FE2C551A; border-bottom:1px solid #FE2C5533; padding:0.625rem 1rem; display:flex; align-items:center; gap:0.5rem;">
                        <svg style="width:14px;height:14px;color:#EE1D52;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span style="font-size:0.78rem; font-weight:700; color:#EE1D52;">Bio Copy</span>
                        <span style="font-size:0.7rem; color:#94A3B8;">— optimised for TikTok search</span>
                    </div>
                    <div style="padding:1rem;" x-data>
                        <p style="font-size:0.9rem; color:#0F172A; margin:0 0 0.75rem; font-weight:500;">{{ $result['bio_copy'] }}</p>
                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <span style="font-size:0.72rem; color:#94A3B8;">{{ strlen($result['bio_copy']) }}/80 characters</span>
                            <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ addslashes($result['bio_copy']) }}'); $dispatch('show-toast', { message: 'Bio copied' })"
                                style="font-size:0.75rem; color:#64748B; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:0.3rem 0.75rem; cursor:pointer; display:flex; align-items:center; gap:0.3rem;">
                                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copy bio
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Filming tips --}}
            @if(!empty($result['content_tips']))
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:1rem; display:flex; gap:0.625rem;">
                    <svg style="width:16px;height:16px;color:#7C3AED;flex-shrink:0;margin-top:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.5 3.5 0 01-4.95 0l-.347-.347z"/>
                    </svg>
                    <div>
                        <p style="font-size:0.78rem; font-weight:700; color:#374151; margin:0 0 0.25rem;">Filming tips</p>
                        <p style="font-size:0.82rem; color:#475569; margin:0; line-height:1.6; white-space:pre-line;">{{ $result['content_tips'] }}</p>
                    </div>
                </div>
            @endif

        </div>
    @endif

</div>
