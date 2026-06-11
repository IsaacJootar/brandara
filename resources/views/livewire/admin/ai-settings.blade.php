<div>
    <h1 style="font-size:1.25rem; font-weight:800; color:#0F172A; margin:0 0 0.5rem;">AI Settings</h1>
    <p style="font-size:0.82rem; color:#64748B; margin:0 0 1.5rem;">Control which AI provider powers content generation and which providers are enabled for AI presence scans.</p>

    {{-- Default content generation provider --}}
    <div class="admin-card" style="margin-bottom:1.5rem;">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 0.375rem;">Content generation provider</p>
        <p style="font-size:0.78rem; color:#64748B; margin:0 0 1rem;">This provider powers all AI content generation (posts, TikTok scripts, carousel copy, WhatsApp messages).</p>

        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            @foreach([
                ['key' => 'claude', 'label' => 'Claude (Anthropic)', 'desc' => 'Default. High-quality brand-aware content.', 'color' => '#7C3AED'],
                ['key' => 'openai', 'label' => 'OpenAI (GPT-4o)', 'desc' => 'Alternative provider. Good general-purpose output.', 'color' => '#16A34A'],
            ] as $provider)
                <button type="button" wire:click="$set('defaultProvider', '{{ $provider['key'] }}')"
                    style="flex:1; min-width:200px; padding:1rem; border-radius:12px; text-align:left; cursor:pointer; transition:all 0.15s;
                    background:{{ $defaultProvider === $provider['key'] ? $provider['color'].'0D' : '#F8FAFC' }};
                    border:2px solid {{ $defaultProvider === $provider['key'] ? $provider['color'] : '#E2E8F0' }};">
                    <p style="font-size:0.875rem; font-weight:700; color:{{ $defaultProvider === $provider['key'] ? $provider['color'] : '#0F172A' }}; margin:0 0 0.25rem;">
                        {{ $defaultProvider === $provider['key'] ? '● ' : '○ ' }}{{ $provider['label'] }}
                    </p>
                    <p style="font-size:0.75rem; color:#64748B; margin:0;">{{ $provider['desc'] }}</p>
                </button>
            @endforeach
        </div>
    </div>

    {{-- AI presence providers --}}
    <div class="admin-card" style="margin-bottom:1.5rem;">
        <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 0.375rem;">AI Presence scan providers</p>
        <p style="font-size:0.78rem; color:#64748B; margin:0 0 1rem;">Toggle which AI platforms are queried during live presence scans. Requires API keys in .env.</p>

        <div style="display:flex; flex-direction:column; gap:0.75rem;">
            @foreach([
                ['model' => 'claudeEnabled', 'label' => 'Claude', 'desc' => 'Anthropic Claude (claude-haiku-4-5)', 'color' => '#7C3AED', 'key' => 'ANTHROPIC_API_KEY'],
                ['model' => 'chatgptEnabled', 'label' => 'ChatGPT', 'desc' => 'OpenAI GPT-4o Mini', 'color' => '#16A34A', 'key' => 'OPENAI_API_KEY'],
                ['model' => 'geminiEnabled', 'label' => 'Gemini', 'desc' => 'Google Gemini 2.0 Flash', 'color' => '#0369A1', 'key' => 'GEMINI_API_KEY'],
                ['model' => 'perplexityEnabled', 'label' => 'Perplexity', 'desc' => 'Coming soon — API not yet available', 'color' => '#94A3B8', 'key' => null],
            ] as $p)
                <div style="display:flex; align-items:center; gap:1rem; padding:0.875rem 1rem; background:#F8FAFC; border-radius:10px; border:1px solid #E2E8F0;">
                    <button type="button" wire:click="$toggle('{{ $p['model'] }}')"
                        style="width:42px; height:24px; border-radius:99px; border:none; cursor:pointer; position:relative; flex-shrink:0;
                        background:{{ $this->{$p['model']} ? $p['color'] : '#E2E8F0' }};">
                        <span style="position:absolute; top:3px; width:18px; height:18px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $this->{$p['model']} ? '21px' : '3px' }};"></span>
                    </button>
                    <div style="flex:1;">
                        <p style="font-size:0.85rem; font-weight:700; color:{{ $this->{$p['model']} ? $p['color'] : '#94A3B8' }}; margin:0;">{{ $p['label'] }}</p>
                        <p style="font-size:0.72rem; color:#94A3B8; margin:0.125rem 0 0;">{{ $p['desc'] }}</p>
                    </div>
                    @if($p['key'])
                        <span style="font-size:0.68rem; color:{{ config('services.' . strtolower(str_replace('_API_KEY', '', $p['key'])) . '.key') ? '#16A34A' : '#DC2626' }}; font-weight:600;">
                            {{ config('services.' . strtolower(str_replace('_API_KEY', '', $p['key'])) . '.key') ? 'Key set' : 'No key' }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Save --}}
    <button type="button" wire:click="save"
        style="padding:0.6rem 2rem; background:#7C3AED; color:#fff; border:none; border-radius:9px; font-size:0.875rem; font-weight:600; cursor:pointer;">
        Save AI settings
    </button>
</div>
