<div>

    <div style="margin-bottom:1.5rem;">
        <h2 style="font-size:1rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Brand Kit</h2>
        <p style="font-size:0.83rem; color:#94A3B8; margin:0;">Your brand identity. Used in every AI-generated post to keep your content on-brand.</p>
    </div>

    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.75rem; display:flex; flex-direction:column; gap:1.25rem;">

        {{-- Name + Tagline --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">
                    Brand name <span style="color:#EF4444;">*</span>
                </label>
                <input wire:model="name" type="text" maxlength="100"
                    value="{{ $name }}"
                    placeholder="e.g. Acme Consulting"
                    class="auth-input" style="font-size:0.875rem;">
                @error('name')<p style="color:#EF4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Tagline</label>
                <input wire:model="tagline" type="text" maxlength="160"
                    value="{{ $tagline }}"
                    placeholder="e.g. We help African founders scale faster"
                    class="auth-input" style="font-size:0.875rem;">
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">What your business does</label>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.375rem;">Plain English. The AI uses this to understand your context.</p>
            <textarea wire:model="description" rows="3" maxlength="1000"
                placeholder="e.g. We provide financial audit and advisory services to SMEs. We specialise in helping founders get investor-ready."
                class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:80px;">{{ $description }}</textarea>
        </div>

        {{-- Target audience --}}
        <div>
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Target audience</label>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.375rem;">Who are you talking to? Be specific — the AI will write directly to this person.</p>
            <textarea wire:model="targetAudience" rows="2" maxlength="500"
                placeholder="e.g. SME founders aged 30–50, annual revenue {{ $currency['revenue_range'] }}, looking to scale or raise funding. Busy, practical, no time for jargon."
                class="auth-input" style="font-size:0.875rem; resize:vertical; min-height:60px;">{{ $targetAudience }}</textarea>
        </div>

        {{-- Colours --}}
        <div>
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Brand colours</label>
            <p style="font-size:0.78rem; color:#94A3B8; margin:0 0 0.75rem;">Used for visual previews and templates.</p>
            <div style="display:flex; flex-wrap:wrap; gap:1.25rem;">
                <div style="display:flex; align-items:center; gap:0.625rem;">
                    <input wire:model.lazy="primaryColor" type="color" value="{{ $primaryColor }}"
                        style="width:42px; height:42px; border-radius:8px; border:1px solid #E2E8F0; cursor:pointer; padding:2px; background:#fff;">
                    <div>
                        <p style="font-size:0.75rem; font-weight:600; color:#374151; margin:0 0 0.25rem;">Primary</p>
                        <input wire:model.lazy="primaryColor" type="text" maxlength="7"
                            value="{{ $primaryColor }}"
                            placeholder="#7C3AED"
                            class="auth-input" style="width:110px; font-size:0.8rem; font-family:monospace; padding:0.5rem 0.75rem;">
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:0.625rem;">
                    <input wire:model.lazy="secondaryColor" type="color" value="{{ $secondaryColor }}"
                        style="width:42px; height:42px; border-radius:8px; border:1px solid #E2E8F0; cursor:pointer; padding:2px; background:#fff;">
                    <div>
                        <p style="font-size:0.75rem; font-weight:600; color:#374151; margin:0 0 0.25rem;">Secondary</p>
                        <input wire:model.lazy="secondaryColor" type="text" maxlength="7"
                            value="{{ $secondaryColor }}"
                            placeholder="#4338CA"
                            class="auth-input" style="width:110px; font-size:0.8rem; font-family:monospace; padding:0.5rem 0.75rem;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Font preference --}}
        <div>
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Font preference</label>
            <select wire:model="fontPreference"
                class="auth-input" style="font-size:0.875rem; cursor:pointer; max-width:280px;">
                <option value="">No preference</option>
                <option value="Inter" {{ $fontPreference === 'Inter' ? 'selected' : '' }}>Inter — clean, modern</option>
                <option value="Lato" {{ $fontPreference === 'Lato' ? 'selected' : '' }}>Lato — friendly, readable</option>
                <option value="Playfair Display" {{ $fontPreference === 'Playfair Display' ? 'selected' : '' }}>Playfair Display — elegant, premium</option>
                <option value="Montserrat" {{ $fontPreference === 'Montserrat' ? 'selected' : '' }}>Montserrat — bold, professional</option>
                <option value="Merriweather" {{ $fontPreference === 'Merriweather' ? 'selected' : '' }}>Merriweather — editorial, trustworthy</option>
                <option value="Poppins" {{ $fontPreference === 'Poppins' ? 'selected' : '' }}>Poppins — approachable, modern</option>
            </select>
        </div>

        {{-- Logo placeholder --}}
        <div>
            <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Logo</label>
            <div style="border:2px dashed #E2E8F0; border-radius:12px; padding:1.5rem; text-align:center; background:#FAFBFF;">
                <svg style="width:28px; height:28px; color:#CBD5E1; margin:0 auto 0.5rem; display:block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p style="font-size:0.8rem; color:#94A3B8; margin:0;">Logo upload — coming in the next update</p>
            </div>
        </div>

        {{-- Save --}}
        <div style="display:flex; justify-content:flex-end; padding-top:0.5rem; border-top:1px solid #F1F5F9;">
            <button type="button" wire:click="save"
                wire:loading.attr="disabled" wire:loading.class="opacity-60 cursor-not-allowed" wire:target="save"
                style="padding:0.75rem 1.75rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <span wire:loading.remove wire:target="save">Save Brand Kit</span>
                <span wire:loading.flex wire:target="save" style="display:none; align-items:center; gap:0.5rem;">
                    <span class="btn-spinner"></span> Saving…
                </span>
            </button>
        </div>

    </div>

</div>
