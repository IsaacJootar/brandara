<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h2 class="text-lg font-bold text-neutral">Brand Voice</h2>
        <p class="text-sm text-base-content/60 mt-0.5">
            Paste 10–20 of your best posts. Your Brand Voice profile is used in every piece of AI-generated content to make it sound like <em>you</em>.
        </p>
    </div>

    {{-- ── TRAINED STATE ─────────────────────────────────────────────────── --}}
    @if ($status === 'trained' && count($profile))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 space-y-4">

            {{-- Status badge --}}
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-sm font-semibold text-emerald-800">Brand Voice active</span>
                    @if($brand->voice_samples_count)
                        <span class="text-xs text-emerald-600">&mdash; trained on {{ $brand->voice_samples_count }} post{{ $brand->voice_samples_count !== 1 ? 's' : '' }}</span>
                    @endif
                </div>
                <button wire:click="retrain" class="btn btn-sm btn-ghost text-emerald-700 border border-emerald-300 hover:bg-emerald-100">
                    Update voice
                </button>
            </div>

            {{-- Writing summary --}}
            @if(!empty($profile['writing_summary']))
                <div class="bg-white rounded-xl p-4 border border-emerald-100">
                    <p class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Writing summary</p>
                    <p class="text-sm text-neutral leading-relaxed">{{ $profile['writing_summary'] }}</p>
                </div>
            @endif

            {{-- Profile grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                @if(!empty($profile['sentence_length']))
                <div class="bg-white rounded-xl p-3 border border-emerald-100">
                    <p class="text-xs text-base-content/50 mb-0.5">Sentence style</p>
                    <p class="text-sm font-medium text-neutral capitalize">{{ $profile['sentence_length'] }} sentences &mdash; {{ $profile['sentence_rhythm'] ?? '' }}</p>
                </div>
                @endif

                @if(!empty($profile['vocabulary_level']))
                <div class="bg-white rounded-xl p-3 border border-emerald-100">
                    <p class="text-xs text-base-content/50 mb-0.5">Vocabulary</p>
                    <p class="text-sm font-medium text-neutral capitalize">{{ $profile['vocabulary_level'] }}</p>
                </div>
                @endif

                @if(!empty($profile['structure_preference']))
                <div class="bg-white rounded-xl p-3 border border-emerald-100">
                    <p class="text-xs text-base-content/50 mb-0.5">Structure</p>
                    <p class="text-sm font-medium text-neutral capitalize">{{ $profile['structure_preference'] }}</p>
                </div>
                @endif

                @if(!empty($profile['emoji_usage']))
                <div class="bg-white rounded-xl p-3 border border-emerald-100">
                    <p class="text-xs text-base-content/50 mb-0.5">Emoji usage</p>
                    <p class="text-sm font-medium text-neutral capitalize">{{ $profile['emoji_usage'] }}</p>
                </div>
                @endif

                @if(!empty($profile['tone_characteristics']))
                <div class="bg-white rounded-xl p-3 border border-emerald-100 sm:col-span-2">
                    <p class="text-xs text-base-content/50 mb-1.5">Tone</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($profile['tone_characteristics'] as $key => $value)
                            <span class="badge badge-sm border border-emerald-200 bg-emerald-50 text-emerald-700 capitalize">
                                {{ str_replace('_', ' ', $key) }}: {{ $value }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            {{-- Signature phrases --}}
            @if(!empty($profile['signature_phrases']) && count($profile['signature_phrases']))
                <div class="bg-white rounded-xl p-3 border border-emerald-100">
                    <p class="text-xs text-base-content/50 mb-1.5">Signature phrases</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($profile['signature_phrases'] as $phrase)
                            <span class="badge badge-ghost text-xs">&ldquo;{{ $phrase }}&rdquo;</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Preferred words --}}
            @if(!empty($profile['preferred_words']) && count($profile['preferred_words']))
                <div class="bg-white rounded-xl p-3 border border-emerald-100">
                    <p class="text-xs text-base-content/50 mb-1.5">Words you use</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($profile['preferred_words'] as $word)
                            <span class="badge badge-sm bg-emerald-100 text-emerald-700 border-0">{{ $word }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    {{-- ── TRAINING STATE ────────────────────────────────────────────────── --}}
    @elseif($status === 'training')
        <div class="rounded-2xl border border-base-300 bg-base-100 p-8 text-center space-y-3">
            <span class="loading loading-spinner loading-md text-primary"></span>
            <p class="text-sm font-medium text-neutral">Analysing your writing style&hellip;</p>
            <p class="text-xs text-base-content/50">This takes about 10–15 seconds.</p>
        </div>

    {{-- ── ERROR STATE ───────────────────────────────────────────────────── --}}
    @elseif($status === 'error')
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 flex gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-red-800">{{ $errorMessage }}</p>
                <button wire:click="retrain" class="text-xs text-red-600 underline mt-1">Try again</button>
            </div>
        </div>

    {{-- ── IDLE STATE (default) ──────────────────────────────────────────── --}}
    @else
        <div class="rounded-2xl border border-base-300 bg-base-100 p-5 space-y-4">

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-amber-800 leading-relaxed">
                    Paste posts you have already written — LinkedIn posts, captions, newsletters, anything. Separate each post with a blank line. The more you add, the more accurate the profile.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral mb-1.5">
                    Your writing samples
                    <span class="text-base-content/40 font-normal">&nbsp;(10–20 posts recommended)</span>
                </label>
                <textarea
                    wire:model="samples"
                    rows="10"
                    placeholder="Paste your posts here, one per paragraph. Separate each post with a blank line.

Example:
Running a business in Lagos is not for the faint-hearted. Three power cuts before 9am and I still closed two deals. Here is what actually keeps me going...

The biggest mistake I see founders make is pricing for survival instead of pricing for value. When you charge what you need to survive, you attract clients who see you as a cost. Charge what you are worth and you attract clients who see you as an investment."
                    class="textarea textarea-bordered w-full text-sm leading-relaxed resize-none focus:outline-none focus:border-primary"
                ></textarea>
            </div>

            <button
                wire:click="train"
                wire:loading.attr="disabled"
                wire:target="train"
                class="btn btn-primary w-full sm:w-auto"
                data-loading-form
            >
                <span wire:loading.remove wire:target="train" class="btn-label flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347a3.5 3.5 0 01-4.95 0l-.347-.347z"/>
                    </svg>
                    Analyse my writing style
                </span>
                <span wire:loading wire:target="train" class="btn-loading flex items-center gap-2">
                    <span class="loading loading-spinner loading-xs"></span>
                    Analysing&hellip;
                </span>
            </button>

        </div>
    @endif

</div>
