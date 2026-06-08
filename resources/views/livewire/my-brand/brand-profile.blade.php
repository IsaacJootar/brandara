<div>

    <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Brand Profile</h2>
        <p style="font-size:0.83rem; color:#94A3B8; margin:0;">The deeper story behind your brand. This context makes your AI content stand out.</p>
    </div>

    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.75rem; display:flex; flex-direction:column; gap:1.25rem;">

        {{-- Vision --}}
        <div>
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Vision</label>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.375rem;">Where do you want your brand to be in 3 years?</p>
            <textarea wire:model="vision" rows="2" maxlength="500"
                placeholder="e.g. To be the most trusted financial advisory firm for growing businesses across West Africa."
                class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:60px;"></textarea>
        </div>

        {{-- Mission --}}
        <div>
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Mission</label>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.375rem;">Why does your business exist? What problem are you solving?</p>
            <textarea wire:model="mission" rows="2" maxlength="500"
                placeholder="e.g. We exist to give African founders access to the same quality of financial guidance that global corporations take for granted."
                class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:60px;"></textarea>
        </div>

        {{-- Values --}}
        <div>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.25rem;">
                <label style="font-size:0.8rem; font-weight:600; color:#374151;">Brand values</label>
                @if(count($values) < 5)
                    <button type="button" wire:click="addValue"
                        style="font-size:0.78rem; color:#7C3AED; font-weight:600; background:none; border:1px solid #DDD6FE; border-radius:6px; cursor:pointer; padding:0.2rem 0.625rem; transition:background 0.15s;"
                        onmouseover="this.style.background='#F5F3FF'" onmouseout="this.style.background='none'">
                        + Add value
                    </button>
                @endif
            </div>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.75rem;">Up to 5 values. Each one shapes how the AI talks about your brand.</p>

            <div style="display:flex; flex-direction:column; gap:0.625rem;">
                @foreach($values as $i => $value)
                    <div style="display:flex; gap:0.625rem; align-items:center;">
                        <input wire:model="values.{{ $i }}.title" type="text" maxlength="80"
                            value="{{ $value['title'] ?? '' }}"
                            placeholder="Value name (e.g. Integrity)"
                            class="auth-input" style="flex:1; font-size:0.85rem; padding:0.625rem 0.875rem;">
                        <input wire:model="values.{{ $i }}.description" type="text" maxlength="300"
                            value="{{ $value['description'] ?? '' }}"
                            placeholder="What this means for your brand"
                            class="auth-input" style="flex:2.5; font-size:0.85rem; padding:0.625rem 0.875rem;">
                        @if(count($values) > 1)
                            <button type="button" wire:click="removeValue({{ $i }})"
                                style="flex-shrink:0; background:none; border:none; cursor:pointer; color:#CBD5E1; padding:0.25rem; border-radius:6px; transition:color 0.15s;"
                                onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#CBD5E1'">
                                <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Negative brief — keeps its amber highlight --}}
        <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:12px; padding:1rem;">
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
                <label style="font-size:0.8rem; font-weight:600; color:#374151;">Negative brief</label>
                <span style="font-size:0.7rem; font-weight:600; color:#D97706; background:#FEF3C7; padding:0.15rem 0.5rem; border-radius:99px; border:1px solid #FDE68A;">Most important</span>
            </div>
            <p style="font-size:0.78rem; color:#92400E; margin:0 0 0.5rem;">What your brand <strong>never</strong> says, never sounds like, never does. The AI will avoid everything here.</p>
            <textarea wire:model="negativeBrief" rows="3" maxlength="1000"
                placeholder="e.g. We never use corporate buzzwords like 'synergy' or 'leverage'. We never make promises we can't back up. We never talk down to small business owners."
                class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:80px;"></textarea>
        </div>

        {{-- Positioning --}}
        <div>
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Positioning</label>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.375rem;">How are you different from competitors? What makes you the obvious choice?</p>
            <textarea wire:model="positioning" rows="2" maxlength="500"
                placeholder="e.g. Unlike the big accountancy firms, we work exclusively with African founders. We combine global standards with deep local market knowledge."
                class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:60px;"></textarea>
        </div>

        {{-- Save --}}
        <div style="display:flex; justify-content:flex-end; padding-top:0.5rem; border-top:1px solid #F1F5F9;">
            <button type="button" wire:click="save"
                wire:loading.attr="disabled" wire:loading.class="opacity-60 cursor-not-allowed" wire:target="save"
                style="padding:0.75rem 1.75rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <span wire:loading.remove wire:target="save">Save Brand Profile</span>
                <span wire:loading.flex wire:target="save" style="display:none; align-items:center; gap:0.5rem;">
                    <span class="btn-spinner"></span> Saving…
                </span>
            </button>
        </div>

    </div>

</div>
