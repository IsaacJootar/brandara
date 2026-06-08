<div>

    {{-- Header --}}
    <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Brand Voice</h2>
        <p style="font-size:0.83rem; color:#94A3B8; margin:0;">Paste 10–20 of your best posts. The AI learns your exact writing style and uses it in every generated post.</p>
    </div>

    {{-- ── TRAINED STATE ──────────────────────────────────────────────────── --}}
    @if ($status === 'trained' && count($profile))

        <div style="background:#ECFDF5; border:1px solid #A7F3D0; border-radius:16px; padding:1.25rem; display:flex; flex-direction:column; gap:1rem;">

            {{-- Status row --}}
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <span style="width:9px; height:9px; border-radius:50%; background:#10B981; display:inline-block; flex-shrink:0;"></span>
                    <span style="font-size:0.875rem; font-weight:700; color:#065F46;">Brand Voice active</span>
                    @if($brand->voice_samples_count)
                        <span style="font-size:0.78rem; color:#059669;">&mdash; trained on {{ $brand->voice_samples_count }} post{{ $brand->voice_samples_count !== 1 ? 's' : '' }}</span>
                    @endif
                </div>
                <button type="button" wire:click="retrain"
                    style="font-size:0.8rem; font-weight:600; color:#fff; background:#059669; border:none; border-radius:8px; padding:0.5rem 1rem; cursor:pointer; transition:opacity 0.15s; display:flex; align-items:center; gap:0.375rem;"
                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'"
                    title="Paste new writing samples to update your voice profile">
                    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Retrain voice
                </button>
            </div>

            {{-- Writing summary --}}
            @if(!empty($profile['writing_summary']))
                <div style="background:#fff; border:1px solid #D1FAE5; border-radius:12px; padding:1rem;">
                    <p style="font-size:0.7rem; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.06em; margin:0 0 0.375rem;">Writing summary</p>
                    <p style="font-size:0.875rem; color:#0F172A; line-height:1.6; margin:0;">{{ $profile['writing_summary'] }}</p>
                </div>
            @endif

            {{-- Profile grid --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.625rem;">

                @if(!empty($profile['sentence_length']))
                <div style="background:#fff; border:1px solid #D1FAE5; border-radius:10px; padding:0.75rem;">
                    <p style="font-size:0.7rem; color:#94A3B8; margin:0 0 0.25rem;">Sentence style</p>
                    <p style="font-size:0.825rem; font-weight:600; color:#0F172A; margin:0; text-transform:capitalize;">{{ $profile['sentence_length'] }}{{ !empty($profile['sentence_rhythm']) ? ' — ' . $profile['sentence_rhythm'] : '' }}</p>
                </div>
                @endif

                @if(!empty($profile['vocabulary_level']))
                <div style="background:#fff; border:1px solid #D1FAE5; border-radius:10px; padding:0.75rem;">
                    <p style="font-size:0.7rem; color:#94A3B8; margin:0 0 0.25rem;">Vocabulary</p>
                    <p style="font-size:0.825rem; font-weight:600; color:#0F172A; margin:0; text-transform:capitalize;">{{ $profile['vocabulary_level'] }}</p>
                </div>
                @endif

                @if(!empty($profile['structure_preference']))
                <div style="background:#fff; border:1px solid #D1FAE5; border-radius:10px; padding:0.75rem;">
                    <p style="font-size:0.7rem; color:#94A3B8; margin:0 0 0.25rem;">Structure</p>
                    <p style="font-size:0.825rem; font-weight:600; color:#0F172A; margin:0; text-transform:capitalize;">{{ $profile['structure_preference'] }}</p>
                </div>
                @endif

                @if(!empty($profile['emoji_usage']))
                <div style="background:#fff; border:1px solid #D1FAE5; border-radius:10px; padding:0.75rem;">
                    <p style="font-size:0.7rem; color:#94A3B8; margin:0 0 0.25rem;">Emoji usage</p>
                    <p style="font-size:0.825rem; font-weight:600; color:#0F172A; margin:0; text-transform:capitalize;">{{ $profile['emoji_usage'] }}</p>
                </div>
                @endif

            </div>

            {{-- Tone --}}
            @if(!empty($profile['tone_characteristics']))
                <div style="background:#fff; border:1px solid #D1FAE5; border-radius:10px; padding:0.75rem;">
                    <p style="font-size:0.7rem; color:#94A3B8; margin:0 0 0.5rem;">Tone</p>
                    <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                        @foreach($profile['tone_characteristics'] as $key => $value)
                            <span style="font-size:0.72rem; font-weight:500; color:#065F46; background:#D1FAE5; border:1px solid #A7F3D0; padding:0.2rem 0.625rem; border-radius:99px; text-transform:capitalize;">
                                {{ str_replace('_', ' ', $key) }}: {{ $value }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Signature phrases --}}
            @if(!empty($profile['signature_phrases']) && count($profile['signature_phrases']))
                <div style="background:#fff; border:1px solid #D1FAE5; border-radius:10px; padding:0.75rem;">
                    <p style="font-size:0.7rem; color:#94A3B8; margin:0 0 0.5rem;">Signature phrases</p>
                    <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                        @foreach($profile['signature_phrases'] as $phrase)
                            <span style="font-size:0.78rem; color:#374151; background:#F8FAFC; border:1px solid #E2E8F0; padding:0.2rem 0.625rem; border-radius:6px;">&ldquo;{{ $phrase }}&rdquo;</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Preferred words --}}
            @if(!empty($profile['preferred_words']) && count($profile['preferred_words']))
                <div style="background:#fff; border:1px solid #D1FAE5; border-radius:10px; padding:0.75rem;">
                    <p style="font-size:0.7rem; color:#94A3B8; margin:0 0 0.5rem;">Words you use</p>
                    <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                        @foreach($profile['preferred_words'] as $word)
                            <span style="font-size:0.72rem; font-weight:500; color:#065F46; background:#D1FAE5; padding:0.2rem 0.5rem; border-radius:99px;">{{ $word }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Retrain hint --}}
            <p style="font-size:0.75rem; color:#6EE7B7; margin:0; border-top:1px solid #A7F3D0; padding-top:0.75rem;">
                Your writing style evolves — click <strong>Retrain voice</strong> any time you want to update this profile with newer posts.
            </p>

        </div>

    {{-- ── TRAINING STATE ─────────────────────────────────────────────────── --}}
    @elseif($status === 'training')
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:3rem 1.5rem; text-align:center;">
            <div style="width:32px; height:32px; border:3px solid #E2E8F0; border-top-color:#7C3AED; border-radius:50%; animation:spin 0.8s linear infinite; margin:0 auto 1rem;"></div>
            <p style="font-size:0.9rem; font-weight:600; color:#0F172A; margin:0 0 0.25rem;">Analysing your writing style…</p>
            <p style="font-size:0.8rem; color:#94A3B8; margin:0;">This takes about 10–15 seconds.</p>
        </div>

    {{-- ── ERROR STATE ────────────────────────────────────────────────────── --}}
    @elseif($status === 'error')
        <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:12px; padding:1rem; display:flex; gap:0.75rem;">
            <svg style="width:18px; height:18px; color:#EF4444; flex-shrink:0; margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p style="font-size:0.875rem; font-weight:500; color:#991B1B; margin:0 0 0.25rem;">{{ $errorMessage }}</p>
                <button wire:click="retrain" style="font-size:0.8rem; color:#DC2626; background:none; border:none; cursor:pointer; padding:0; text-decoration:underline;">Try again</button>
            </div>
        </div>

    {{-- ── IDLE STATE ──────────────────────────────────────────────────────── --}}
    @else
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">

            <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:10px; padding:0.875rem; display:flex; gap:0.625rem;">
                <svg style="width:16px; height:16px; color:#D97706; flex-shrink:0; margin-top:1px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p style="font-size:0.8rem; color:#92400E; line-height:1.5; margin:0;">
                    Paste posts you have already written — LinkedIn posts, captions, newsletters, anything. Separate each post with a blank line. The more you add, the more accurate your profile.
                </p>
            </div>

            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">
                    Your writing samples
                    <span style="font-weight:400; color:#94A3B8;">&nbsp;(10–20 posts recommended)</span>
                </label>
                <textarea wire:model="samples" rows="10"
                    placeholder="Paste your posts here, one per paragraph. Separate each post with a blank line.

Example:
Running a business in Lagos is not for the faint-hearted. Three power cuts before 9am and I still closed two deals. Here is what actually keeps me going...

The biggest mistake I see founders make is pricing for survival instead of pricing for value. When you charge what you need to survive, you attract clients who see you as a cost. Charge what you are worth and you attract clients who see you as an investment."
                    class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:220px; line-height:1.6;"></textarea>
            </div>

            <div>
                <button type="button" wire:click="train"
                    wire:loading.attr="disabled" wire:target="train"
                    style="padding:0.75rem 1.75rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    {{-- Normal label: hidden while loading --}}
                    <span wire:loading.remove wire:target="train">
                        <span style="display:flex; align-items:center; gap:0.5rem;">
                            <svg style="width:16px; height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.5 3.5 0 01-4.95 0l-.347-.347z"/>
                            </svg>
                            Analyse my writing style
                        </span>
                    </span>
                    {{-- Loading label: only shown while Livewire is processing --}}
                    <span wire:loading.flex wire:target="train" style="display:none; align-items:center; gap:0.5rem;">
                        <span class="btn-spinner"></span> Analysing…
                    </span>
                </button>
            </div>

        </div>
    @endif

</div>
