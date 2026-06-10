<div>

    @php
        $sections = [
            'general'       => ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'General'],
            'engagement'    => ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'Engagement'],
            'publishing'    => ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Publishing'],
            'notifications' => ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'label' => 'Notifications'],
        ];
    @endphp

    {{-- ── SECTION TABS — horizontal scroll on mobile, sidebar on desktop ─── --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:0.375rem; margin-bottom:1.25rem; display:flex; gap:0.25rem; overflow-x:auto; -webkit-overflow-scrolling:touch;">
        @foreach($sections as $key => $section)
            <button type="button" wire:click="setSection('{{ $key }}')"
                style="display:flex; align-items:center; gap:0.5rem; padding:0.55rem 0.875rem; border-radius:9px; border:none; cursor:pointer; font-size:0.82rem; font-weight:{{ $activeSection === $key ? '600' : '400' }}; background:{{ $activeSection === $key ? '#F5F3FF' : 'transparent' }}; color:{{ $activeSection === $key ? '#7C3AED' : '#64748B' }}; white-space:nowrap; transition:all 0.15s; flex-shrink:0;">
                <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $section['icon'] }}"/>
                </svg>
                {{ $section['label'] }}
            </button>
        @endforeach
    </div>

    {{-- ── SECTION CONTENT ──────────────────────────────────────────────────── --}}
    <div>

        {{-- GENERAL ─────────────────────────────────────────────────────────── --}}
        @if($activeSection === 'general')
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                    <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">General</p>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0;">Your brand name, language, tone, and timezone.</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:1.125rem;">

                    <div>
                        <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Brand name</label>
                        <input type="text" wire:model="brandName" class="auth-input" style="font-size:0.875rem;">
                        @error('brandName')<p style="color:#EF4444;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div>
                            <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Default tone</label>
                            <select wire:model="defaultTone" style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; background:#fff; outline:none;">
                                <option value="professional">Professional</option>
                                <option value="founder">Founder voice</option>
                                <option value="african">African business</option>
                                <option value="friendly">Friendly</option>
                                <option value="bold">Bold & direct</option>
                                <option value="educational">Educational</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Language</label>
                            <select wire:model="language" style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; background:#fff; outline:none;">
                                <option value="en">English</option>
                                <option value="fr">French</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">Timezone</label>
                        <select wire:model="timezone" style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.85rem; color:#0F172A; background:#fff; outline:none;">
                            <option value="Africa/Lagos">Africa/Lagos (WAT, UTC+1)</option>
                            <option value="Africa/Accra">Africa/Accra (GMT, UTC+0)</option>
                            <option value="Africa/Nairobi">Africa/Nairobi (EAT, UTC+3)</option>
                            <option value="Africa/Johannesburg">Africa/Johannesburg (SAST, UTC+2)</option>
                            <option value="Africa/Abidjan">Africa/Abidjan (GMT, UTC+0)</option>
                            <option value="Africa/Douala">Africa/Douala (WAT, UTC+1)</option>
                            <option value="Europe/London">Europe/London (GMT/BST)</option>
                            <option value="America/New_York">America/New_York (EST/EDT)</option>
                        </select>
                    </div>

                    <div style="padding-top:0.25rem;">
                        <button type="button" wire:click="saveGeneral"
                            wire:loading.attr="disabled" wire:target="saveGeneral"
                            style="padding:0.6rem 1.375rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <span wire:loading.remove wire:target="saveGeneral">Save</span>
                            <span wire:loading.flex wire:target="saveGeneral" style="display:none; align-items:center; gap:0.375rem;"><span class="btn-spinner"></span>Saving…</span>
                        </button>
                    </div>
                </div>
            </div>

        {{-- ENGAGEMENT ──────────────────────────────────────────────────────── --}}
        @elseif($activeSection === 'engagement')
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                    <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">Engagement Automation</p>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0;">Control whether this brand auto-likes and auto-comments on social media.</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:1.25rem;">

                    {{-- Master toggle --}}
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1rem; background:{{ $engagementEnabled ? '#F0FDF4' : '#F8FAFC' }}; border:1px solid {{ $engagementEnabled ? '#BBF7D0' : '#E2E8F0' }}; border-radius:12px;">
                        <div>
                            <p style="font-size:0.875rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">
                                Engagement automation is {{ $engagementEnabled ? 'on' : 'off' }}
                            </p>
                            <p style="font-size:0.78rem; color:#64748B; margin:0; line-height:1.5;">
                                @if($engagementEnabled)
                                    Brandara is actively running your rules. It will auto-like and auto-comment in your Brand Voice based on the rules you set in Grow.
                                @else
                                    No automated actions will run for this brand. Turn it on when you're ready. All existing rules are saved but paused.
                                @endif
                            </p>
                        </div>
                        <button type="button" wire:click="$set('engagementEnabled', {{ $engagementEnabled ? 'false' : 'true' }})"
                            style="width:48px; height:26px; border-radius:99px; border:none; cursor:pointer; transition:background 0.2s; background:{{ $engagementEnabled ? '#16A34A' : '#CBD5E1' }}; position:relative; flex-shrink:0; margin-top:2px;">
                            <span style="position:absolute; top:3px; width:20px; height:20px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $engagementEnabled ? '25px' : '3px' }};"></span>
                        </button>
                    </div>

                    @if($engagementEnabled)
                        <div>
                            <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Scan frequency</label>
                            <p style="font-size:0.75rem; color:#94A3B8; margin:0 0 0.5rem;">How often Brandara checks for new posts matching your rules.</p>
                            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                @foreach(['daily' => 'Once a day', 'twice_daily' => 'Twice a day', 'weekly' => 'Once a week', 'twice_weekly' => 'Twice a week'] as $val => $lbl)
                                    <button type="button" wire:click="$set('engagementScanFrequency', '{{ $val }}')"
                                        style="padding:0.4rem 0.875rem; border-radius:8px; font-size:0.8rem; border:{{ $engagementScanFrequency === $val ? '2px solid #7C3AED' : '1px solid #E2E8F0' }}; background:{{ $engagementScanFrequency === $val ? '#F5F3FF' : '#fff' }}; color:{{ $engagementScanFrequency === $val ? '#7C3AED' : '#64748B' }}; cursor:pointer; font-weight:{{ $engagementScanFrequency === $val ? '600' : '400' }};">
                                        {{ $lbl }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div style="background:#FFF7ED; border:1px solid #FED7AA; border-radius:10px; padding:0.75rem 1rem; font-size:0.78rem; color:#92400E; line-height:1.5;">
                            <strong>Stay within platform guidelines.</strong> Keep daily limits under 50 actions per platform. Brandara enforces this — but overly aggressive engagement can trigger platform warnings.
                        </div>
                    @endif

                    <div>
                        <button type="button" wire:click="saveEngagement"
                            wire:loading.attr="disabled" wire:target="saveEngagement"
                            style="padding:0.6rem 1.375rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <span wire:loading.remove wire:target="saveEngagement">Save</span>
                            <span wire:loading.flex wire:target="saveEngagement" style="display:none; align-items:center; gap:0.375rem;"><span class="btn-spinner"></span>Saving…</span>
                        </button>
                    </div>
                </div>
            </div>

        {{-- PUBLISHING ──────────────────────────────────────────────────────── --}}
        @elseif($activeSection === 'publishing')
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                    <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">Publishing</p>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0;">Default posting time and content recycling preferences.</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:1.125rem;">

                    <div>
                        <label style="display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.25rem;">Default post time</label>
                        <p style="font-size:0.75rem; color:#94A3B8; margin:0 0 0.5rem;">Used when scheduling posts without a specific time.</p>
                        <input type="time" wire:model="defaultPostTime"
                            style="padding:0.5rem 0.75rem; border:1px solid #E2E8F0; border-radius:8px; font-size:0.875rem; color:#0F172A; outline:none;">
                        @error('defaultPostTime')<p style="color:#EF4444;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>

                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px;">
                        <div>
                            <p style="font-size:0.875rem; font-weight:600; color:#0F172A; margin:0 0 0.2rem;">Evergreen recycling</p>
                            <p style="font-size:0.78rem; color:#64748B; margin:0; line-height:1.5;">Automatically re-queue high-performing posts every 60–90 days.</p>
                        </div>
                        <button type="button" wire:click="$set('evergreenRecycling', {{ $evergreenRecycling ? 'false' : 'true' }})"
                            style="width:48px; height:26px; border-radius:99px; border:none; cursor:pointer; transition:background 0.2s; background:{{ $evergreenRecycling ? '#7C3AED' : '#CBD5E1' }}; position:relative; flex-shrink:0; margin-top:2px;">
                            <span style="position:absolute; top:3px; width:20px; height:20px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $evergreenRecycling ? '25px' : '3px' }};"></span>
                        </button>
                    </div>

                    <div>
                        <button type="button" wire:click="savePublishing"
                            wire:loading.attr="disabled" wire:target="savePublishing"
                            style="padding:0.6rem 1.375rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <span wire:loading.remove wire:target="savePublishing">Save</span>
                            <span wire:loading.flex wire:target="savePublishing" style="display:none; align-items:center; gap:0.375rem;"><span class="btn-spinner"></span>Saving…</span>
                        </button>
                    </div>
                </div>
            </div>

        {{-- NOTIFICATIONS ───────────────────────────────────────────────────── --}}
        @elseif($activeSection === 'notifications')
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #F1F5F9;">
                    <p style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">Notifications</p>
                    <p style="font-size:0.78rem; color:#94A3B8; margin:0;">Choose what Brandara notifies you about for this brand.</p>
                </div>
                <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0;">

                    @php
                        $notifRows = [
                            ['prop' => 'notifyPostFailed',    'label' => 'Post failed to publish',         'desc' => 'Alert when a scheduled post doesn\'t go live.'],
                            ['prop' => 'notifyPostPublished', 'label' => 'Post published successfully',     'desc' => 'Confirmation when a post goes live.'],
                            ['prop' => 'notifyLeadCaptured',  'label' => 'New lead captured',               'desc' => 'When someone engages and is added to your lead tracker.'],
                            ['prop' => 'notifyTrialExpiring', 'label' => 'Trial or plan expiring soon',     'desc' => 'Reminder before your trial or plan ends.'],
                        ];
                    @endphp

                    @foreach($notifRows as $i => $row)
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:0.875rem 0; {{ !$loop->last ? 'border-bottom:1px solid #F1F5F9;' : '' }}">
                            <div>
                                <p style="font-size:0.85rem; font-weight:600; color:#0F172A; margin:0 0 0.15rem;">{{ $row['label'] }}</p>
                                <p style="font-size:0.75rem; color:#94A3B8; margin:0;">{{ $row['desc'] }}</p>
                            </div>
                            <button type="button" wire:click="$set('{{ $row['prop'] }}', {{ $this->{$row['prop']} ? 'false' : 'true' }})"
                                style="width:44px; height:24px; border-radius:99px; border:none; cursor:pointer; transition:background 0.2s; background:{{ $this->{$row['prop']} ? '#7C3AED' : '#CBD5E1' }}; position:relative; flex-shrink:0; margin-top:2px;">
                                <span style="position:absolute; top:3px; width:18px; height:18px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $this->{$row['prop']} ? '22px' : '3px' }};"></span>
                            </button>
                        </div>
                    @endforeach

                    <div style="padding-top:1rem;">
                        <button type="button" wire:click="saveNotifications"
                            wire:loading.attr="disabled" wire:target="saveNotifications"
                            style="padding:0.6rem 1.375rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <span wire:loading.remove wire:target="saveNotifications">Save</span>
                            <span wire:loading.flex wire:target="saveNotifications" style="display:none; align-items:center; gap:0.375rem;"><span class="btn-spinner"></span>Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
