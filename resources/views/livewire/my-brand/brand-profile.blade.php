<div class="space-y-6">

    <div>
        <h2 class="text-lg font-bold text-neutral">Brand Profile</h2>
        <p class="text-sm text-base-content/60 mt-0.5">The deeper story behind your brand. This is the context that makes your AI content stand out.</p>
    </div>

    {{-- Saved toast --}}
    @if($saveStatus === 'saved')
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="fixed top-5 right-5 z-50 flex items-center gap-2 bg-emerald-600 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg"
            style="min-width:220px;"
        >
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            Brand Profile saved
        </div>
    @endif

    <div class="rounded-2xl border border-base-300 bg-base-100 p-5 space-y-5">

        {{-- Vision --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">Vision</label>
            <p class="text-xs text-base-content/50 mb-1.5">Where do you want your brand to be in 3 years?</p>
            <textarea wire:model="vision" rows="2" maxlength="500"
                placeholder="e.g. To be the most trusted financial advisory firm for growing businesses across West Africa."
                class="textarea textarea-bordered w-full text-sm resize-none"></textarea>
        </div>

        {{-- Mission --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">Mission</label>
            <p class="text-xs text-base-content/50 mb-1.5">Why does your business exist? What problem are you solving?</p>
            <textarea wire:model="mission" rows="2" maxlength="500"
                placeholder="e.g. We exist to give African founders access to the same quality of financial guidance that global corporations take for granted."
                class="textarea textarea-bordered w-full text-sm resize-none"></textarea>
        </div>

        {{-- Values --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-medium text-neutral">Brand values</label>
                @if(count($values) < 5)
                    <button wire:click="addValue" class="btn btn-xs btn-ghost text-primary">
                        + Add value
                    </button>
                @endif
            </div>
            <p class="text-xs text-base-content/50 mb-3">Up to 5 values. Each one shapes how the AI talks about your brand.</p>

            <div class="space-y-3">
                @foreach($values as $i => $value)
                    <div class="flex gap-2 items-start">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <input wire:model="values.{{ $i }}.title" type="text" maxlength="80"
                                placeholder="Value name (e.g. Integrity)"
                                class="input input-bordered input-sm text-sm">
                            <input wire:model="values.{{ $i }}.description" type="text" maxlength="300"
                                placeholder="What this means for your brand"
                                class="input input-bordered input-sm text-sm sm:col-span-2">
                        </div>
                        @if(count($values) > 1)
                            <button wire:click="removeValue({{ $i }})"
                                class="btn btn-ghost btn-sm btn-square text-base-content/30 hover:text-error mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Negative brief --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">
                Negative brief
                <span class="ml-1 badge badge-sm badge-warning">Most important</span>
            </label>
            <p class="text-xs text-base-content/50 mb-1.5">What your brand <strong>never</strong> says, never sounds like, never does. The AI will avoid everything here.</p>
            <textarea wire:model="negativeBrief" rows="3" maxlength="1000"
                placeholder="e.g. We never use corporate buzzwords like 'synergy' or 'leverage'. We never make promises we can't back up with data. We never talk down to small business owners. We never sound desperate or salesy."
                class="textarea textarea-bordered w-full text-sm resize-none"></textarea>
        </div>

        {{-- Positioning --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">Positioning</label>
            <p class="text-xs text-base-content/50 mb-1.5">How are you different from your competitors? What makes you the obvious choice?</p>
            <textarea wire:model="positioning" rows="2" maxlength="500"
                placeholder="e.g. Unlike the big accountancy firms, we work exclusively with African founders. We combine global standards with deep local market knowledge. We are the only firm that offers flat-rate advisory — no surprise invoices."
                class="textarea textarea-bordered w-full text-sm resize-none"></textarea>
        </div>

        {{-- Save --}}
        <div class="flex items-center justify-end pt-2 border-t border-base-200">
            <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                class="btn btn-primary">
                <span wire:loading.remove wire:target="save">Save Brand Profile</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <span class="loading loading-spinner loading-xs"></span> Saving…
                </span>
            </button>
        </div>

    </div>

</div>
