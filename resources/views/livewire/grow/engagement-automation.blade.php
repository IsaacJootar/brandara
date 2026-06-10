<div>

    {{-- ── DISABLED STATE ──────────────────────────────────────────────────── --}}
    @if(!$engagementEnabled)
        <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:14px; padding:2rem; text-align:center; margin-bottom:1.5rem;">
            <div style="width:44px;height:44px;background:#F1F5F9;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.875rem;">
                <svg style="width:20px;height:20px;color:#94A3B8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.375rem;">Engagement automation is off</p>
            <p style="font-size:0.82rem; color:#64748B; margin:0 0 1.25rem; max-width:380px; margin-left:auto; margin-right:auto; line-height:1.6;">No automated actions are running for this brand. Turn it on in Settings when you're ready — your rules will be waiting.</p>
            <a href="{{ route('settings', ['brand' => \App\Models\Brand::find($brandId)?->slug ?? '']) }}"
               style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.6rem 1.25rem; background:#7C3AED; color:#fff; font-size:0.85rem; font-weight:600; border-radius:10px; text-decoration:none; transition:opacity 0.15s;"
               onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                Go to Settings → Engagement
            </a>
        </div>
    @endif

    {{-- ── HEADER ROW ─────────────────────────────────────────────────────── --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem;">
        <div>
            <p style="font-size:0.82rem; color:#64748B; margin:0; max-width:480px; line-height:1.5;">
                Define rules and Brandara automatically likes or comments in your Brand Voice — staying within platform limits.
            </p>
        </div>
        <button type="button" wire:click="openForm"
            style="display:flex; align-items:center; gap:0.5rem; padding:0.6rem 1.25rem; background:#7C3AED; color:#fff; font-size:0.85rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; transition:opacity 0.15s; white-space:nowrap;"
            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add rule
        </button>
    </div>

    {{-- ── ADD RULE FORM ───────────────────────────────────────────────────── --}}
    @if($showForm)
        <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:14px; padding:1.5rem; margin-bottom:1.75rem;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
                <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0;">New rule</p>
                <button type="button" wire:click="closeForm"
                    style="background:none; border:none; color:#94A3B8; cursor:pointer; font-size:1rem; line-height:1;">✕</button>
            </div>

            @if($formError)
                <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; padding:0.625rem 0.875rem; margin-bottom:1rem; font-size:0.82rem; color:#991B1B;">
                    {{ $formError }}
                </div>
            @endif

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">

                {{-- Rule type --}}
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">What should this rule do?</label>
                    <div style="display:flex; gap:0.375rem;">
                        @foreach(['auto_like' => '👍 Auto-like', 'auto_comment' => '💬 Auto-comment'] as $val => $lbl)
                            <button type="button" wire:click="$set('ruleType', '{{ $val }}')"
                                style="flex:1; padding:0.5rem 0.5rem; border-radius:8px; font-size:0.8rem; font-weight:{{ $ruleType === $val ? '700' : '500' }}; border:{{ $ruleType === $val ? '2px solid #7C3AED' : '1px solid #E2E8F0' }}; background:{{ $ruleType === $val ? '#F5F3FF' : '#fff' }}; color:{{ $ruleType === $val ? '#7C3AED' : '#64748B' }}; cursor:pointer;">
                                {{ $lbl }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Platform --}}
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Platform</label>
                    <select wire:model="platform"
                        style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; background:#fff; outline:none;">
                        <option value="linkedin">LinkedIn</option>
                        <option value="twitter">X (Twitter)</option>
                        <option value="instagram">Instagram</option>
                        <option value="facebook">Facebook</option>
                        <option value="threads">Threads</option>
                    </select>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">

                {{-- Target accounts --}}
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Account handles to watch</label>
                    <p style="font-size:0.72rem; color:#94A3B8; margin:0 0 0.375rem;">Comma-separated. e.g. @johndoe, @janebusiness</p>
                    <input type="text" wire:model="accountsRaw"
                        placeholder="@handle1, @handle2"
                        style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; outline:none;">
                </div>

                {{-- Target keywords --}}
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Keywords to match</label>
                    <p style="font-size:0.72rem; color:#94A3B8; margin:0 0 0.375rem;">Engage when a post contains these words.</p>
                    <input type="text" wire:model="keywordsRaw"
                        placeholder="branding, marketing, Lagos"
                        style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; outline:none;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">

                {{-- Daily limit --}}
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Daily limit</label>
                    <p style="font-size:0.72rem; color:#94A3B8; margin:0 0 0.375rem;">Max actions per day. Stay under 50 to be safe.</p>
                    <input type="number" wire:model="dailyLimit" min="1" max="200"
                        style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; outline:none;">
                    @error('dailyLimit')<p style="color:#EF4444; font-size:0.72rem; margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>

                {{-- Industry --}}
                <div>
                    <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Industry filter <span style="color:#94A3B8; font-weight:400;">(optional)</span></label>
                    <p style="font-size:0.72rem; color:#94A3B8; margin:0 0 0.375rem;">Only engage with posts in this industry.</p>
                    <input type="text" wire:model="industry"
                        placeholder="e.g. HR, Fintech, Consulting"
                        style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; outline:none;">
                </div>
            </div>

            {{-- Auto-comment extras --}}
            @if($ruleType === 'auto_comment')
                <div style="background:#F5F3FF; border:1px solid #DDD6FE; border-radius:10px; padding:1rem; margin-bottom:1rem; display:flex; flex-direction:column; gap:0.875rem;">
                    <p style="font-size:0.78rem; font-weight:700; color:#7C3AED; margin:0;">Auto-comment settings</p>

                    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                        <div>
                            <p style="font-size:0.82rem; font-weight:600; color:#374151; margin:0 0 0.2rem;">Review comments before posting</p>
                            <p style="font-size:0.75rem; color:#64748B; margin:0;">AI writes the comment — you approve it first. Recommended.</p>
                        </div>
                        <button type="button" wire:click="$set('requireReview', {{ $requireReview ? 'false' : 'true' }})"
                            style="width:44px; height:24px; border-radius:99px; border:none; cursor:pointer; transition:background 0.2s; background:{{ $requireReview ? '#7C3AED' : '#CBD5E1' }}; position:relative; flex-shrink:0;">
                            <span style="position:absolute; top:3px; width:18px; height:18px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $requireReview ? '23px' : '3px' }};"></span>
                        </button>
                    </div>

                    <div>
                        <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Comment tone</label>
                        <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                            @foreach(['professional' => 'Professional', 'founder' => 'Founder voice', 'friendly' => 'Friendly', 'african' => 'African business', 'bold' => 'Bold'] as $val => $lbl)
                                <button type="button" wire:click="$set('commentTone', '{{ $val }}')"
                                    style="padding:0.3rem 0.75rem; border-radius:99px; font-size:0.75rem; border:{{ $commentTone === $val ? '2px solid #7C3AED' : '1px solid #E2E8F0' }}; background:{{ $commentTone === $val ? '#7C3AED' : '#fff' }}; color:{{ $commentTone === $val ? '#fff' : '#64748B' }}; cursor:pointer; font-weight:{{ $commentTone === $val ? '600' : '400' }};">
                                    {{ $lbl }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div style="display:flex; gap:0.75rem;">
                <button type="button" wire:click="saveRule"
                    wire:loading.attr="disabled" wire:target="saveRule"
                    style="padding:0.65rem 1.5rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <span wire:loading.remove wire:target="saveRule">Save rule</span>
                    <span wire:loading.flex wire:target="saveRule" style="display:none; align-items:center; gap:0.375rem;"><span class="btn-spinner"></span>Saving…</span>
                </button>
                <button type="button" wire:click="closeForm"
                    style="padding:0.65rem 1rem; background:#F8FAFC; color:#64748B; font-size:0.875rem; font-weight:500; border:1px solid #E2E8F0; border-radius:10px; cursor:pointer;">
                    Cancel
                </button>
            </div>
        </div>
    @endif

    {{-- ── PENDING REVIEW QUEUE ─────────────────────────────────────────────── --}}
    @if($pendingComments->isNotEmpty())
        <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:14px; overflow:hidden; margin-bottom:1.75rem;">
            <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #FDE68A; display:flex; align-items:center; gap:0.5rem;">
                <svg style="width:15px;height:15px;color:#B45309;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span style="font-size:0.82rem; font-weight:700; color:#92400E;">{{ $pendingComments->count() }} comment{{ $pendingComments->count() > 1 ? 's' : '' }} waiting for your approval</span>
            </div>
            @foreach($pendingComments as $action)
                <div style="padding:1rem 1.25rem; {{ !$loop->last ? 'border-bottom:1px solid #FEF3C7;' : '' }}">
                    <div style="font-size:0.72rem; color:#B45309; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.375rem;">
                        {{ ucfirst($action->platform) }} · @{{ $action->target_account }}
                    </div>
                    <p style="font-size:0.8rem; color:#78716C; margin:0 0 0.5rem; font-style:italic; line-height:1.5;">
                        "{{ Str::limit($action->target_post_excerpt, 120) }}"
                    </p>
                    <p style="font-size:0.875rem; color:#0F172A; margin:0 0 0.75rem; line-height:1.6; font-weight:500;">
                        {{ $action->comment_body }}
                    </p>
                    <div style="display:flex; gap:0.5rem;">
                        <button type="button" wire:click="approveComment('{{ $action->id }}')"
                            style="padding:0.35rem 0.875rem; background:#15803D; color:#fff; font-size:0.78rem; font-weight:600; border:none; border-radius:7px; cursor:pointer;">
                            ✓ Post this comment
                        </button>
                        <button type="button" wire:click="skipAction('{{ $action->id }}')"
                            style="padding:0.35rem 0.875rem; background:#F8FAFC; color:#64748B; font-size:0.78rem; border:1px solid #E2E8F0; border-radius:7px; cursor:pointer;">
                            Skip
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── RULES LIST ───────────────────────────────────────────────────────── --}}
    @if($rules->isEmpty())
        <div style="background:#F8FAFC; border:2px dashed #E2E8F0; border-radius:14px; padding:2.5rem; text-align:center;">
            <div style="width:44px;height:44px;background:#F1F5F9;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.875rem;">
                <svg style="width:20px;height:20px;color:#94A3B8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <p style="font-size:0.9rem; font-weight:600; color:#0F172A; margin:0 0 0.375rem;">No rules yet</p>
            <p style="font-size:0.82rem; color:#94A3B8; margin:0 0 1rem;">Add your first rule to start engaging automatically in your Brand Voice.</p>
            <button type="button" wire:click="openForm"
                style="padding:0.6rem 1.25rem; background:#7C3AED; color:#fff; font-size:0.85rem; font-weight:600; border:none; border-radius:10px; cursor:pointer;">
                Add your first rule
            </button>
        </div>
    @else
        <div style="display:flex; flex-direction:column; gap:0.625rem;">
            @foreach($rules as $rule)
                @php
                    $typeLabel = $rule->type === 'auto_like' ? '👍 Auto-like' : '💬 Auto-comment';
                    $platformColors = [
                        'linkedin'  => '#0077B5',
                        'twitter'   => '#000',
                        'instagram' => '#DD2A7B',
                        'facebook'  => '#1877F2',
                        'threads'   => '#333',
                    ];
                    $color = $platformColors[$rule->platform] ?? '#64748B';
                @endphp
                <div style="background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">

                    {{-- Type badge --}}
                    <div style="font-size:0.82rem; font-weight:700; color:#0F172A; min-width:120px;">{{ $typeLabel }}</div>

                    {{-- Platform --}}
                    <span style="font-size:0.75rem; font-weight:600; color:#fff; background:{{ $color }}; padding:0.2rem 0.625rem; border-radius:99px;">
                        {{ ucfirst($rule->platform === 'twitter' ? 'X' : $rule->platform) }}
                    </span>

                    {{-- Targets --}}
                    <div style="flex:1; min-width:0;">
                        @if($rule->target_accounts && count($rule->target_accounts))
                            <span style="font-size:0.75rem; color:#64748B;">
                                Accounts: {{ implode(', ', array_slice($rule->target_accounts, 0, 3)) }}{{ count($rule->target_accounts) > 3 ? ' +'.( count($rule->target_accounts) - 3).' more' : '' }}
                            </span>
                        @endif
                        @if($rule->target_keywords && count($rule->target_keywords))
                            <span style="font-size:0.75rem; color:#64748B; {{ $rule->target_accounts && count($rule->target_accounts) ? 'margin-left:0.75rem;' : '' }}">
                                Keywords: {{ implode(', ', array_slice($rule->target_keywords, 0, 3)) }}{{ count($rule->target_keywords) > 3 ? ' +more' : '' }}
                            </span>
                        @endif
                    </div>

                    {{-- Daily limit --}}
                    <span style="font-size:0.75rem; color:#94A3B8;">{{ $rule->daily_limit }}/day</span>

                    {{-- Review badge --}}
                    @if($rule->type === 'auto_comment')
                        <span style="font-size:0.7rem; color:{{ $rule->require_review ? '#7C3AED' : '#16A34A' }}; background:{{ $rule->require_review ? '#F5F3FF' : '#F0FDF4' }}; padding:0.2rem 0.5rem; border-radius:6px; font-weight:600; white-space:nowrap;">
                            {{ $rule->require_review ? 'Review on' : 'Auto-post' }}
                        </span>
                    @endif

                    {{-- Toggle active --}}
                    <button type="button" wire:click="toggleRule('{{ $rule->id }}')"
                        style="width:38px; height:21px; border-radius:99px; border:none; cursor:pointer; transition:background 0.2s; background:{{ $rule->is_active ? '#7C3AED' : '#CBD5E1' }}; position:relative; flex-shrink:0;"
                        title="{{ $rule->is_active ? 'Pause rule' : 'Activate rule' }}">
                        <span style="position:absolute; top:2px; width:17px; height:17px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $rule->is_active ? '19px' : '2px' }};"></span>
                    </button>

                    {{-- Delete --}}
                    <button type="button" wire:click="deleteRule('{{ $rule->id }}')"
                        wire:confirm="Delete this rule?"
                        style="background:none; border:none; color:#94A3B8; cursor:pointer; font-size:0.85rem; padding:0.25rem 0.375rem; border-radius:6px;"
                        title="Delete rule">✕</button>
                </div>
            @endforeach
        </div>

        {{-- Recent actions log --}}
        @if($recentActions->isNotEmpty())
            <div style="margin-top:1.5rem;">
                <p style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#94A3B8; margin:0 0 0.625rem;">Recent activity</p>
                <div style="display:flex; flex-direction:column; gap:0.375rem;">
                    @foreach($recentActions as $action)
                        <div style="display:flex; align-items:center; gap:0.75rem; font-size:0.8rem; color:#64748B; padding:0.5rem 0.75rem; background:#F8FAFC; border-radius:8px;">
                            <span>{{ $action->type === 'like' ? '👍' : '💬' }}</span>
                            <span style="color:#0F172A; font-weight:500;">{{ ucfirst($action->platform === 'twitter' ? 'X' : $action->platform) }}</span>
                            <span>@{{ $action->target_account }}</span>
                            <span style="margin-left:auto; color:{{ $action->status === 'posted' ? '#16A34A' : '#DC2626' }}; font-weight:600; font-size:0.72rem;">
                                {{ $action->status === 'posted' ? '✓ Sent' : '✕ Failed' }}
                            </span>
                            <span style="color:#CBD5E1; font-size:0.72rem;">{{ $action->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

</div>
