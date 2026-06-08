<div style="position:relative;" x-data>

    {{-- Bell button --}}
    <button wire:click="toggle" type="button"
        style="width:34px; height:34px; border-radius:8px; border:1px solid #E2E8F0; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#64748B; position:relative;">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if ($unreadCount > 0)
            <span style="position:absolute; top:-4px; right:-4px; min-width:16px; height:16px; background:#DC2626; color:#fff; font-size:0.6rem; font-weight:700; border-radius:99px; display:flex; align-items:center; justify-content:center; padding:0 3px; line-height:1;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown panel --}}
    @if ($open)
        <div wire:click.outside="toggle"
            style="position:absolute; top:calc(100% + 8px); right:0; width:340px; background:#fff; border:1px solid #E2E8F0; border-radius:12px; box-shadow:0 12px 32px rgba(15,23,42,0.12); z-index:200; overflow:hidden; max-height:480px; display:flex; flex-direction:column;">

            <div style="padding:0.875rem 1rem; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
                <div style="font-size:0.875rem; font-weight:600; color:#0F172A;">Notifications</div>
                @if ($unreadCount > 0)
                    <button wire:click="markAllRead" type="button"
                        style="font-size:0.72rem; color:#7C3AED; background:none; border:none; cursor:pointer; font-weight:500;">
                        Mark all read
                    </button>
                @endif
            </div>

            <div style="overflow-y:auto; flex:1;">
                @forelse ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = is_null($notification->read_at);
                        $icon = match ($data['type'] ?? '') {
                            'post_failed'    => ['bg' => '#FEF2F2', 'color' => '#DC2626', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>'],
                            'trial_expiring' => ['bg' => '#FFFBEB', 'color' => '#D97706', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                            'token_expired'  => ['bg' => '#FEF2F2', 'color' => '#DC2626', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>'],
                            'approval_needed'=> ['bg' => '#F0FDF4', 'color' => '#16A34A', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                            default          => ['bg' => '#F8FAFC', 'color' => '#64748B', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>'],
                        };
                    @endphp
                    <div wire:click="markRead('{{ $notification->id }}')"
                        style="display:flex; gap:0.75rem; padding:0.875rem 1rem; border-bottom:1px solid #F8FAFC; cursor:pointer; background:{{ $isUnread ? '#FAFBFF' : '#fff' }}; transition:background 0.1s;"
                        onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='{{ $isUnread ? '#FAFBFF' : '#fff' }}'">

                        <div style="width:32px; height:32px; border-radius:8px; background:{{ $icon['bg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">
                            <svg width="14" height="14" fill="none" stroke="{{ $icon['color'] }}" stroke-width="2" viewBox="0 0 24 24">{!! $icon['svg'] !!}</svg>
                        </div>

                        <div style="flex:1; min-width:0;">
                            <div style="font-size:0.82rem; font-weight:{{ $isUnread ? '600' : '500' }}; color:#0F172A; line-height:1.4;">{{ $data['title'] ?? 'Notification' }}</div>
                            <div style="font-size:0.75rem; color:#64748B; margin-top:0.15rem; line-height:1.4;">{{ \Illuminate\Support\Str::limit($data['message'] ?? '', 90) }}</div>
                            <div style="font-size:0.68rem; color:#94A3B8; margin-top:0.25rem;">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>

                        @if ($isUnread)
                            <div style="width:7px; height:7px; border-radius:50%; background:#7C3AED; flex-shrink:0; margin-top:6px;"></div>
                        @endif
                    </div>
                @empty
                    <div style="padding:2.5rem 1rem; text-align:center; color:#94A3B8; font-size:0.83rem;">
                        You're all caught up 🎉
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
