<div class="space-y-6">

    <div>
        <h2 class="text-lg font-bold text-neutral">Brand Kit</h2>
        <p class="text-sm text-base-content/60 mt-0.5">Your brand identity. Used in every AI-generated post to keep your content on-brand.</p>
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
            Brand Kit saved
        </div>
    @endif

    <div class="rounded-2xl border border-base-300 bg-base-100 p-5 space-y-5">

        {{-- Name + Tagline --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-neutral mb-1">Brand name <span class="text-error">*</span></label>
                <input wire:model="name" type="text" maxlength="100"
                    placeholder="e.g. Acme Consulting"
                    class="input input-bordered w-full text-sm @error('name') input-error @enderror">
                @error('name')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral mb-1">Tagline</label>
                <input wire:model="tagline" type="text" maxlength="160"
                    placeholder="e.g. We help African founders scale faster"
                    class="input input-bordered w-full text-sm">
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">What your business does</label>
            <p class="text-xs text-base-content/50 mb-1.5">Plain English. The AI uses this to understand your context.</p>
            <textarea wire:model="description" rows="3" maxlength="1000"
                placeholder="e.g. We provide financial audit and advisory services to SMEs in Nigeria and Ghana. We specialise in helping founders get investor-ready."
                class="textarea textarea-bordered w-full text-sm resize-none"></textarea>
        </div>

        {{-- Target audience --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">Target audience</label>
            <p class="text-xs text-base-content/50 mb-1.5">Who are you talking to? Be specific — the AI will write directly to this person.</p>
            <textarea wire:model="targetAudience" rows="2" maxlength="500"
                placeholder="e.g. Nigerian SME founders aged 30–50, annual revenue ₦50M–₦500M, looking to scale or raise funding. Busy, practical, no time for jargon."
                class="textarea textarea-bordered w-full text-sm resize-none"></textarea>
        </div>

        {{-- Colours --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">Brand colours</label>
            <p class="text-xs text-base-content/50 mb-2">Used for visual previews and templates.</p>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <input wire:model.lazy="primaryColor" type="color"
                        class="w-10 h-10 rounded-lg border border-base-300 cursor-pointer p-0.5">
                    <div>
                        <p class="text-xs font-medium text-neutral">Primary</p>
                        <input wire:model.lazy="primaryColor" type="text" maxlength="7"
                            class="input input-bordered input-sm w-28 text-xs font-mono"
                            placeholder="#7C3AED">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input wire:model.lazy="secondaryColor" type="color"
                        class="w-10 h-10 rounded-lg border border-base-300 cursor-pointer p-0.5">
                    <div>
                        <p class="text-xs font-medium text-neutral">Secondary</p>
                        <input wire:model.lazy="secondaryColor" type="text" maxlength="7"
                            class="input input-bordered input-sm w-28 text-xs font-mono"
                            placeholder="#4338CA">
                    </div>
                </div>
            </div>
        </div>

        {{-- Font preference --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">Font preference</label>
            <select wire:model="fontPreference" class="select select-bordered w-full sm:w-64 text-sm">
                <option value="">No preference</option>
                <option value="Inter">Inter — clean, modern</option>
                <option value="Lato">Lato — friendly, readable</option>
                <option value="Playfair Display">Playfair Display — elegant, premium</option>
                <option value="Montserrat">Montserrat — bold, professional</option>
                <option value="Merriweather">Merriweather — editorial, trustworthy</option>
                <option value="Poppins">Poppins — approachable, modern</option>
            </select>
        </div>

        {{-- Logo placeholder --}}
        <div>
            <label class="block text-sm font-medium text-neutral mb-1">Logo</label>
            <div class="rounded-xl border-2 border-dashed border-base-300 p-6 text-center bg-base-50">
                <svg class="w-8 h-8 text-base-content/20 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-base-content/40">Logo upload — coming in the next update</p>
            </div>
        </div>

        {{-- Save --}}
        <div class="flex items-center justify-end pt-2 border-t border-base-200">
            <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                class="btn btn-primary">
                <span wire:loading.remove wire:target="save">Save Brand Kit</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <span class="loading loading-spinner loading-xs"></span> Saving…
                </span>
            </button>
        </div>

    </div>

</div>
