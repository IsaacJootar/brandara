<div>

    @if ($status === 'idle')
        {{-- Generate button --}}
        <button wire:click="generate" type="button"
            style="width:100%; display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.875rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.9rem; font-weight:600; border:none; border-radius:11px; cursor:pointer; transition:opacity 0.15s;">
            ✨ Generate 3 post variations
        </button>

    @elseif ($status === 'generating')
        {{-- Generating state --}}
        <div style="background:#F5F3FF; border:1px solid #EDE9FE; border-radius:12px; padding:2rem; text-align:center;">
            <div style="display:flex; align-items:center; justify-content:center; gap:0.75rem; margin-bottom:0.75rem;">
                <span class="btn-spinner" style="width:20px; height:20px; border-width:3px; border-color:#7C3AED; border-right-color:transparent;"></span>
                <span style="font-size:0.95rem; font-weight:600; color:#5B21B6;">Writing your 3 variations…</span>
            </div>
            <div style="font-size:0.8rem; color:#7C3AED;">This usually takes 10–20 seconds</div>
        </div>

    @elseif ($status === 'error')
        {{-- Error state --}}
        <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:12px; padding:1.25rem;">
            <div style="font-size:0.875rem; font-weight:600; color:#DC2626; margin-bottom:0.35rem;">Could not generate content</div>
            <div style="font-size:0.82rem; color:#DC2626;">{{ $errorMessage }}</div>
            <button wire:click="reset_generator" type="button"
                style="margin-top:0.875rem; font-size:0.8rem; color:#7C3AED; background:none; border:none; cursor:pointer; font-weight:500; text-decoration:underline;">
                Try again
            </button>
        </div>

    @elseif ($status === 'done')
        {{-- 3 Variation cards --}}
        <div style="margin-bottom:1rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.875rem;">
                <div style="font-size:0.875rem; font-weight:600; color:#0F172A;">Pick a variation</div>
                <button wire:click="reset_generator" type="button"
                    style="font-size:0.75rem; color:#94A3B8; background:none; border:none; cursor:pointer;">
                    ↩ Regenerate
                </button>
            </div>

            {{-- Variation cards --}}
            <div style="display:flex; flex-direction:column; gap:0.625rem; margin-bottom:1rem;">
                @foreach ([
                    'authority' => ['label' => 'Authority', 'sub' => 'Expert insight · builds credibility', 'color' => '#7C3AED', 'bg' => '#F5F3FF', 'border' => '#EDE9FE'],
                    'story'     => ['label' => 'Story',     'sub' => 'Narrative · client result or journey', 'color' => '#0369A1', 'bg' => '#EFF6FF', 'border' => '#DBEAFE'],
                    'bold'      => ['label' => 'Bold',      'sub' => 'Strong opinion · drives engagement',   'color' => '#BE123C', 'bg' => '#FFF1F2', 'border' => '#FFE4E6'],
                ] as $key => $meta)
                    @php
                        $isSelected = $selectedVariation === $key;
                        $content = $variations[$key]['platforms'][$previewPlatform] ?? ['body' => '', 'hashtags' => []];
                        $preview = trim(($content['body'] ?? ''));
                    @endphp
                    <div wire:click="selectVariation('{{ $key }}')"
                        style="border:2px solid {{ $isSelected ? $meta['color'] : $meta['border'] }}; background:{{ $isSelected ? $meta['bg'] : '#fff' }}; border-radius:12px; padding:1rem; cursor:pointer; transition:all 0.15s;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <span style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:{{ $meta['color'] }};">{{ $meta['label'] }}</span>
                                <span style="font-size:0.68rem; color:#94A3B8;">{{ $meta['sub'] }}</span>
                            </div>
                            @if ($isSelected)
                                <svg width="16" height="16" fill="{{ $meta['color'] }}" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                        <div style="font-size:0.82rem; color:#374151; line-height:1.6; display:-webkit-box; -webkit-line-clamp:4; -webkit-box-orient:vertical; overflow:hidden;">
                            {{ $preview ?: '—' }}
                        </div>
                        @if (!empty($content['hashtags']))
                            <div style="margin-top:0.5rem; font-size:0.72rem; color:{{ $meta['color'] }}; opacity:0.8;">
                                {{ implode(' ', array_map(fn($h) => "#{$h}", array_slice($content['hashtags'], 0, 4))) }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Platform preview switcher --}}
            @if (count($platforms) > 1)
                <div style="display:flex; gap:0.35rem; margin-bottom:1rem; flex-wrap:wrap;">
                    <span style="font-size:0.7rem; color:#94A3B8; font-weight:600; align-self:center; margin-right:0.25rem;">Preview:</span>
                    @foreach ($platforms as $platform)
                        <button wire:click="setPreviewPlatform('{{ $platform }}')" type="button"
                            style="font-size:0.72rem; padding:0.25rem 0.625rem; border-radius:99px; font-weight:{{ $previewPlatform === $platform ? '600' : '400' }}; border:1px solid {{ $previewPlatform === $platform ? '#7C3AED' : '#E2E8F0' }}; background:{{ $previewPlatform === $platform ? '#F5F3FF' : '#fff' }}; color:{{ $previewPlatform === $platform ? '#7C3AED' : '#64748B' }}; cursor:pointer;">
                            {{ ucfirst($platform === 'twitter' ? 'X' : $platform) }}
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Use this variation --}}
            <button wire:click="useVariation" type="button"
                @if(!$selectedVariation) disabled @endif
                style="width:100%; padding:0.8rem; background:{{ $selectedVariation ? 'linear-gradient(135deg,#7C3AED,#4338CA)' : '#E2E8F0' }}; color:{{ $selectedVariation ? '#fff' : '#94A3B8' }}; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:{{ $selectedVariation ? 'pointer' : 'not-allowed' }}; transition:all 0.15s;">
                {{ $selectedVariation ? '✓ Use this variation' : 'Select a variation above' }}
            </button>
        </div>
    @endif

</div>
