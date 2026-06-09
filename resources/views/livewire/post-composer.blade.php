<div style="display:grid; grid-template-columns:1fr 340px; gap:1.5rem; align-items:start;"
     x-data="{ aiPanel: false }">

    {{-- ── LEFT — Main composer ──────────────────────────────────────────── --}}
    <div>

        {{-- Input type tabs — Alpine for instant visual, Livewire persists --}}
        @php
            $tabs = [
                'manual'     => [
                    'label' => 'Write',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213l-4 1 1-4L16.862 3.487z"/>',
                ],
                'topic'      => [
                    'label' => 'From topic',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.355a14.995 14.995 0 01-3.75 0M9.75 9.75a2.25 2.25 0 114.5 0 2.25 2.25 0 01-4.5 0zM12 3v1.5"/>',
                ],
                'transcript' => [
                    'label' => 'Transcript',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/>',
                ],
                'product'    => [
                    'label' => 'Product',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>',
                ],
            ];
        @endphp
        <div x-data="{ activeTab: '{{ $inputType }}' }"
             style="display:flex; gap:0.375rem; margin-bottom:1.25rem; background:#F8FAFC; padding:0.375rem; border-radius:10px; border:1px solid #E2E8F0;">
            @foreach ($tabs as $type => $tab)
                <button type="button"
                    x-on:click="activeTab = '{{ $type }}'; $wire.setInputType('{{ $type }}')"
                    :style="activeTab === '{{ $type }}'
                        ? 'flex:1; display:flex; align-items:center; justify-content:center; gap:0.35rem; padding:0.5rem 0.5rem; border-radius:7px; border:none; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all 0.15s; background:#fff; color:#7C3AED; box-shadow:0 1px 3px rgba(15,23,42,0.08);'
                        : 'flex:1; display:flex; align-items:center; justify-content:center; gap:0.35rem; padding:0.5rem 0.5rem; border-radius:7px; border:none; font-size:0.75rem; font-weight:400; cursor:pointer; transition:all 0.15s; background:transparent; color:#475569;'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0;">{!! $tab['icon'] !!}</svg>
                    <span style="white-space:nowrap;">{{ $tab['label'] }}</span>
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

        {{-- Tone selector — Alpine handles instant visual, Livewire persists --}}
        <div style="margin-bottom:1.25rem;"
             x-data="{ activeTone: '{{ $tone }}' }">
            <label style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8; display:block; margin-bottom:0.5rem;">Tone</label>
            <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                @foreach ($tones as $key => $label)
                    <button type="button"
                        x-on:click="activeTone = '{{ $key }}'; $wire.setTone('{{ $key }}')"
                        :style="activeTone === '{{ $key }}'
                            ? 'padding:0.375rem 0.75rem; border-radius:99px; font-size:0.78rem; font-weight:600; border:1px solid #7C3AED; background:#F5F3FF; color:#7C3AED; cursor:pointer; transition:all 0.15s;'
                            : 'padding:0.375rem 0.75rem; border-radius:99px; font-size:0.78rem; font-weight:400; border:1px solid #E2E8F0; background:#fff; color:#64748B; cursor:pointer; transition:all 0.15s;'">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- AI Variation Picker (non-manual modes) --}}
        @if ($inputType !== 'manual' && strlen(trim($body)) > 0 || $inputType !== 'manual')
            <div style="margin-bottom:1.25rem;">
                @livewire('create.variation-picker', [
                    'brand'       => \App\Models\Brand::find($brandId),
                    'inputType'   => $inputType,
                    'input'       => $body,
                    'platforms'   => $platforms,
                    'tone'        => $tone,
                ], key('variation-picker-'.$inputType))
            </div>
        @endif

        {{-- Pillar selector --}}
        @if($pillars->isNotEmpty())
            <div style="margin-bottom:1rem;">
                <label style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8; display:block; margin-bottom:0.5rem;">Content pillar</label>
                <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                    <button type="button" wire:click="$set('pillarId', null)"
                        style="font-size:0.78rem; font-weight:500; padding:0.3rem 0.75rem; border-radius:99px; cursor:pointer; transition:all 0.15s;
                               {{ is_null($pillarId) ? 'background:#0F172A; color:#fff; border:1px solid #0F172A;' : 'background:#F8FAFC; color:#64748B; border:1px solid #E2E8F0;' }}">
                        None
                    </button>
                    @foreach($pillars as $pillar)
                        <button type="button" wire:click="$set('pillarId', '{{ $pillar->id }}')"
                            style="font-size:0.78rem; font-weight:500; padding:0.3rem 0.75rem; border-radius:99px; cursor:pointer; transition:all 0.15s;
                                   {{ $pillarId === $pillar->id
                                       ? 'background:' . $pillar->color . '; color:#fff; border:1px solid ' . $pillar->color . ';'
                                       : 'background:#F8FAFC; color:#64748B; border:1px solid #E2E8F0;' }}">
                            {{ $pillar->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Media picker + attached thumbnails --}}
        <div style="margin-bottom:1.25rem;">
            @livewire('media.media-picker', ['brand' => \App\Models\Brand::find($brandId)], key('media-picker'))

            @if(count($attachedMedia))
                <div style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:0.75rem;">
                    @foreach($attachedMedia as $media)
                        <div style="position:relative; width:72px; height:72px; border-radius:8px; overflow:hidden; border:1px solid #E2E8F0; flex-shrink:0;">
                            @if(str_starts_with($media['mime'], 'image/'))
                                <img src="{{ $media['url'] }}" alt="{{ $media['name'] }}"
                                    style="width:100%; height:100%; object-fit:cover; display:block;">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#F5F3FF;">
                                    <svg style="width:20px;height:20px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.9L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                    </svg>
                                </div>
                            @endif
                            <button type="button" wire:click="removeMedia('{{ $media['id'] }}')"
                                style="position:absolute;top:2px;right:2px;width:18px;height:18px;background:rgba(0,0,0,0.6);border:none;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:10px;height:10px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Action row --}}
        <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">

            {{-- Save as draft --}}
            <button wire:click="saveDraft" type="button"
                wire:loading.attr="disabled" wire:target="saveDraft"
                style="display:flex; align-items:center; gap:0.5rem; padding:0.75rem 1.5rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; transition:opacity 0.15s;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                @if ($saveStatus === 'saving')
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Saving…
                @elseif ($saveStatus === 'saved')
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Saved as draft
                @else
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Save as draft
                @endif
            </button>

            {{-- Schedule --}}
            <a href="{{ route('schedule', ['brand' => \App\Models\Brand::find($brandId)?->slug ?? '']) }}"
                style="display:flex; align-items:center; gap:0.5rem; padding:0.75rem 1.5rem; background:#F59E0B; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; text-decoration:none; transition:opacity 0.15s;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Schedule
            </a>

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
            <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:0.875rem;">
                <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8;">Publish to</div>
                <div style="font-size:0.68rem; color:#CBD5E1;">Max characters</div>
            </div>

            <div style="display:flex; flex-direction:column; gap:0.375rem;">
                @foreach ($platformNames as $key => $name)
                    @php
                        $allowed  = $this->isPlatformAllowed($key);
                        $selected = $allowed && in_array($key, $platforms);
                        $limit    = $charLimits[$key];
                        $count    = strlen($body);
                        $over     = $count > $limit;
                    @endphp
                    @if(!$allowed)
                        {{-- Locked platform — Growth plan required --}}
                        <div style="display:flex; align-items:center; gap:0.625rem; padding:0.5rem 0.625rem; border-radius:8px; border:1px solid #F1F5F9; background:#FAFBFF; cursor:not-allowed; opacity:0.6;"
                             title="Upgrade to Growth to publish on {{ $name }}">
                            <span style="width:8px; height:8px; border-radius:50%; flex-shrink:0; background:#CBD5E1;"></span>
                            <span style="flex:1; font-size:0.84rem; color:#94A3B8;">{{ $name }}</span>
                            <span style="font-size:0.68rem; color:#CBD5E1; background:#F1F5F9; padding:0.15rem 0.5rem; border-radius:4px; font-weight:600;">Growth</span>
                        </div>
                    @else
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

                        {{-- Char limit --}}
                        <span style="font-size:0.72rem; color:{{ $over ? '#DC2626' : '#94A3B8' }}; font-weight:{{ $over ? '600' : '400' }};">
                            {{ $over ? '⚠ ' : '' }}{{ number_format($limit) }}
                        </span>

                        {{-- Checkmark --}}
                        @if ($selected)
                            <svg width="14" height="14" fill="none" stroke="#7C3AED" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                    @endif
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
