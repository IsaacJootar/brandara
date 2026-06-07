<div style="display:grid; grid-template-columns:1fr 340px; gap:1.5rem; align-items:start;"
     x-data="{ aiPanel: false }">

    {{-- ── LEFT — Main composer ──────────────────────────────────────────── --}}
    <div>

        {{-- Input type tabs --}}
        <div style="display:flex; gap:0.375rem; margin-bottom:1.25rem; background:#F8FAFC; padding:0.375rem; border-radius:10px; border:1px solid #E2E8F0;">
            @foreach ([
                'manual'     => ['✏️', 'Write manually'],
                'topic'      => ['💡', 'From a topic'],
                'transcript' => ['🎙', 'From transcript'],
                'product'    => ['📦', 'Product post'],
            ] as $type => [$emoji, $label])
                <button wire:click="$set('inputType', '{{ $type }}')" type="button"
                    style="flex:1; padding:0.5rem 0.625rem; border-radius:7px; border:none; font-size:0.78rem; font-weight:{{ $inputType === $type ? '600' : '400' }}; cursor:pointer; transition:all 0.15s;
                    {{ $inputType === $type
                        ? 'background:#fff; color:#0F172A; box-shadow:0 1px 3px rgba(15,23,42,0.08);'
                        : 'background:transparent; color:#64748B;' }}">
                    {{ $emoji }} {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Input area --}}
        <div style="position:relative; margin-bottom:1rem;">
            <textarea
                wire:model.blur="body"
                placeholder="{{ $inputType === 'manual'
                    ? 'Write your post here...'
                    : ($inputType === 'topic'
                        ? 'What do you want to post about? e.g. \"Why most Nigerian founders undercharge\"'
                        : ($inputType === 'transcript'
                            ? 'Paste your transcript, voice note text, or meeting notes here...'
                            : 'Describe your product, feature, or offer...')) }}"
                style="width:100%; min-height:220px; padding:1rem; border:1.5px solid #E2E8F0; border-radius:12px; font-size:0.9rem; line-height:1.65; color:#0F172A; resize:vertical; font-family:inherit; outline:none; transition:border-color 0.15s; box-sizing:border-box;"
                onfocus="this.style.borderColor='#7C3AED'"
                onblur="this.style.borderColor='#E2E8F0'"
            ></textarea>

            {{-- Character count --}}
            <div style="position:absolute; bottom:0.75rem; right:0.875rem; font-size:0.75rem; color:{{ $charCount > $tightestLimit ? '#DC2626' : ($charCount > $tightestLimit * 0.9 ? '#D97706' : '#94A3B8') }};">
                {{ number_format($charCount) }} / {{ number_format($tightestLimit) }}
            </div>
        </div>

        {{-- Over-limit warning --}}
        @if (! empty($overLimitPlatforms))
            <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; padding:0.625rem 0.875rem; margin-bottom:1rem; font-size:0.8rem; color:#DC2626;">
                Too long for: {{ implode(', ', $overLimitPlatforms) }}. Shorten your post or deselect those platforms.
            </div>
        @endif

        {{-- Tone selector --}}
        <div style="margin-bottom:1.25rem;">
            <label style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8; display:block; margin-bottom:0.5rem;">Tone</label>
            <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                @foreach ($tones as $key => $label)
                    <button wire:click="$set('tone', '{{ $key }}')" type="button"
                        style="padding:0.375rem 0.75rem; border-radius:99px; font-size:0.78rem; font-weight:{{ $tone === $key ? '600' : '400' }}; border:1px solid {{ $tone === $key ? '#7C3AED' : '#E2E8F0' }}; background:{{ $tone === $key ? '#F5F3FF' : '#fff' }}; color:{{ $tone === $key ? '#7C3AED' : '#64748B' }}; cursor:pointer; transition:all 0.15s;">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Action row --}}
        <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">

            {{-- AI Generate button (wired in Module 10) --}}
            @if ($inputType !== 'manual')
                <button type="button"
                    x-on:click="aiPanel = true"
                    style="display:flex; align-items:center; gap:0.5rem; padding:0.75rem 1.25rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer;">
                    ✨ Generate posts
                </button>
            @endif

            {{-- Save as draft --}}
            <button wire:click="saveDraft" type="button"
                style="display:flex; align-items:center; gap:0.5rem; padding:0.75rem 1.25rem; background:#F8FAFC; color:#475569; font-size:0.875rem; font-weight:600; border:1px solid #E2E8F0; border-radius:10px; cursor:pointer;">
                @if ($saveStatus === 'saving')
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Saving...
                @elseif ($saveStatus === 'saved')
                    ✓ Saved as draft
                @else
                    Save as draft
                @endif
            </button>

            {{-- Schedule (placeholder — Module 07) --}}
            <button type="button" disabled
                style="display:flex; align-items:center; gap:0.5rem; padding:0.75rem 1.25rem; background:#F8FAFC; color:#CBD5E1; font-size:0.875rem; font-weight:600; border:1px solid #E2E8F0; border-radius:10px; cursor:not-allowed;"
                title="Complete brand setup first">
                🗓 Schedule
            </button>

            @if ($savedDraftId)
                <button wire:click="clearComposer" type="button"
                    style="font-size:0.8rem; color:#94A3B8; background:none; border:none; cursor:pointer; text-decoration:underline;">
                    Clear
                </button>
            @endif
        </div>

        {{-- Saved draft confirmation --}}
        @if ($saveStatus === 'saved')
            <div style="margin-top:0.875rem; padding:0.625rem 0.875rem; background:#F0FDF4; border:1px solid #BBF7D0; border-radius:8px; font-size:0.82rem; color:#16A34A;">
                ✓ Saved as draft. You can find it in <strong>Schedule → Not published yet</strong>.
            </div>
        @endif

    </div>

    {{-- ── RIGHT — Platform selector & preview ──────────────────────────── --}}
    <div style="position:sticky; top:1.5rem;">

        {{-- Platform selector --}}
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem; margin-bottom:1rem;">
            <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8; margin-bottom:0.875rem;">Publish to</div>

            <div style="display:flex; flex-direction:column; gap:0.375rem;">
                @foreach ($platformNames as $key => $name)
                    @php
                        $selected = in_array($key, $platforms);
                        $limit    = $charLimits[$key];
                        $count    = strlen($body);
                        $over     = $count > $limit;
                    @endphp
                    <button wire:click="togglePlatform('{{ $key }}')" type="button"
                        style="display:flex; align-items:center; gap:0.625rem; padding:0.5rem 0.625rem; border-radius:8px; border:1px solid {{ $selected ? '#7C3AED' : '#E2E8F0' }}; background:{{ $selected ? '#F5F3FF' : '#fff' }}; cursor:pointer; transition:all 0.15s; text-align:left;">

                        {{-- Platform colour dot --}}
                        <span style="width:8px; height:8px; border-radius:50%; flex-shrink:0; background:{{ match($key) {
                            'linkedin'  => '#0077B5',
                            'twitter'   => '#000',
                            'facebook'  => '#1877F2',
                            'instagram' => '#DD2A7B',
                            'threads'   => '#333',
                            'whatsapp'  => '#25D366',
                            'tiktok'    => '#FE2C55',
                        } }};"></span>

                        <span style="flex:1; font-size:0.84rem; font-weight:{{ $selected ? '600' : '400' }}; color:{{ $selected ? '#0F172A' : '#64748B' }};">{{ $name }}</span>

                        {{-- Char count for this platform --}}
                        <span style="font-size:0.72rem; color:{{ $over ? '#DC2626' : '#94A3B8' }};">
                            {{ $over ? '⚠ ' : '' }}{{ number_format($limit) }}
                        </span>

                        {{-- Checkmark --}}
                        @if ($selected)
                            <svg width="14" height="14" fill="none" stroke="#7C3AED" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Live character breakdown --}}
        @if (! empty($platforms) && $charCount > 0)
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.25rem;">
                <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8; margin-bottom:0.75rem;">Character check</div>
                @foreach ($platforms as $platform)
                    @php
                        $limit   = $charLimits[$platform];
                        $pct     = min(100, round(($charCount / $limit) * 100));
                        $barColor = $pct > 100 ? '#DC2626' : ($pct > 90 ? '#D97706' : '#10B981');
                    @endphp
                    <div style="margin-bottom:0.625rem;">
                        <div style="display:flex; justify-content:space-between; font-size:0.75rem; margin-bottom:0.25rem;">
                            <span style="color:#475569; font-weight:500;">{{ $platformNames[$platform] }}</span>
                            <span style="color:{{ $pct > 100 ? '#DC2626' : '#64748B' }};">{{ $charCount }}/{{ number_format($limit) }}</span>
                        </div>
                        <div style="height:4px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $barColor }}; border-radius:99px; transition:width 0.3s;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
@media (max-width: 768px) {
    div[style*="grid-template-columns:1fr 340px"] {
        display: flex !important;
        flex-direction: column !important;
    }
    div[style*="position:sticky"] {
        position: static !important;
    }
}
</style>
