<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:1rem;">

    @foreach ($platforms as $key => $platform)
        @php
            $conn      = $connections->get($key);
            $connected = $conn && $conn->status === 'connected';
            $expired   = $conn && $conn->status === 'expired';
            $expiring  = $connected && $conn->token_expires_at && $conn->token_expires_at->lessThan(now()->addDays(7));
        @endphp

        <div style="background:#fff; border:1px solid {{ $connected ? '#BBF7D0' : '#E2E8F0' }}; border-radius:16px; padding:1.5rem; position:relative;">

            {{-- Status badge --}}
            <div style="position:absolute; top:1.125rem; right:1.125rem;">
                @if ($connected && !$expiring)
                    <span style="font-size:0.72rem; font-weight:600; color:#16A34A; background:#F0FDF4; padding:0.25rem 0.625rem; border-radius:99px; border:1px solid #BBF7D0;">● Connected</span>
                @elseif ($expiring)
                    <span style="font-size:0.72rem; font-weight:600; color:#D97706; background:#FFFBEB; padding:0.25rem 0.625rem; border-radius:99px; border:1px solid #FDE68A;">⚠ Expiring</span>
                @elseif ($expired)
                    <span style="font-size:0.72rem; font-weight:600; color:#DC2626; background:#FEF2F2; padding:0.25rem 0.625rem; border-radius:99px; border:1px solid #FECACA;">✕ Reconnect</span>
                @else
                    <span style="font-size:0.72rem; color:#94A3B8; background:#F8FAFC; padding:0.25rem 0.625rem; border-radius:99px; border:1px solid #E2E8F0;">Not connected</span>
                @endif
            </div>

            {{-- Platform header --}}
            <div style="display:flex; align-items:center; gap:0.875rem; margin-bottom:1rem; padding-right:7rem;">
                <div style="width:44px; height:44px; border-radius:12px; background:#F8FAFC; border:1px solid #E2E8F0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    @if ($key === 'linkedin')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#0077B5"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    @elseif ($key === 'twitter')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#000"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    @elseif ($key === 'facebook')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    @elseif ($key === 'instagram')
                        <svg width="22" height="22" viewBox="0 0 24 24"><defs><linearGradient id="ig_{{ $loop->index }}" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#F58529"/><stop offset="50%" stop-color="#DD2A7B"/><stop offset="100%" stop-color="#8134AF"/></linearGradient></defs><rect width="20" height="20" x="2" y="2" rx="5" fill="url(#ig_{{ $loop->index }})"/><circle cx="12" cy="12" r="4" stroke="white" stroke-width="1.8" fill="none"/><circle cx="17" cy="7" r="1" fill="white"/></svg>
                    @elseif ($key === 'threads')
                        <svg width="20" height="20" viewBox="0 0 192 192" fill="#000"><path d="M141.537 88.988a66.667 66.667 0 00-2.518-1.143c-1.482-27.307-16.403-42.94-41.457-43.1h-.34c-14.986 0-27.449 6.396-35.12 18.05l13.337 9.15c5.542-8.41 14.243-10.22 21.783-10.22h.23c8.413.053 14.756 2.5 18.869 7.275 2.988 3.5 4.988 8.316 5.974 14.366-7.413-1.258-15.42-1.642-23.993-1.14-24.133 1.39-39.637 15.485-38.57 35.073.545 10.044 5.705 18.698 14.496 24.32 7.417 4.786 16.977 7.13 26.92 6.59 13.12-.722 23.42-5.715 30.6-14.835 5.327-6.807 8.687-15.62 10.168-26.677 6.1 3.677 10.625 8.47 13.207 14.234 4.462 9.964 4.726 26.367-9.18 40.244-12.08 12.05-26.608 17.27-48.594 17.43-24.384-.176-42.881-8.01-54.99-23.287C22.952 138.334 17.717 118.67 17.717 96c0-22.673 5.235-42.333 15.1-56.807 12.109-15.276 30.606-23.11 54.99-23.287 24.521.178 43.376 8.05 56.059 23.395 6.214 7.592 10.871 17.19 13.897 28.456l15.624-4.169c-3.658-13.505-9.517-25.077-17.483-34.444C140.827 13.017 117.8 3.202 88.808 3h-.515c-28.94.198-52.168 10.048-69.012 29.292C5.804 46.858 0 66.24 0 96c0 29.76 5.804 49.14 19.28 63.708 16.844 19.244 40.072 29.094 69.013 29.292h.514c25.633-.174 43.696-6.9 58.517-21.715 19.559-19.553 18.945-44.285 12.52-59.417-4.573-10.196-13.211-18.48-18.307-18.88z"/></svg>
                    @endif
                </div>
                <div>
                    <div style="font-size:0.95rem; font-weight:700; color:#0F172A;">{{ $platform['name'] }}</div>
                    @if ($conn && $conn->platform_username)
                        <div style="font-size:0.78rem; color:#64748B; margin-top:0.1rem;">{{ $conn->platform_username }}</div>
                    @endif
                </div>
            </div>

            {{-- Stats / description --}}
            @if ($connected)
                <div style="display:flex; gap:1.5rem; margin-bottom:1.25rem; padding:0.75rem; background:#F8FAFC; border-radius:10px;">
                    <div>
                        <div style="font-size:0.68rem; color:#94A3B8; text-transform:uppercase; letter-spacing:0.06em; font-weight:600; margin-bottom:0.2rem;">Followers</div>
                        <div style="font-size:1rem; font-weight:700; color:#0F172A;">{{ number_format($conn->follower_count) }}</div>
                    </div>
                    @if ($conn->token_expires_at)
                    <div>
                        <div style="font-size:0.68rem; color:#94A3B8; text-transform:uppercase; letter-spacing:0.06em; font-weight:600; margin-bottom:0.2rem;">Token expires</div>
                        <div style="font-size:1rem; font-weight:700; color:{{ $expiring ? '#D97706' : '#0F172A' }};">{{ $conn->token_expires_at->diffForHumans() }}</div>
                    </div>
                    @endif
                </div>
            @else
                <p style="font-size:0.82rem; color:#94A3B8; margin:0 0 1.25rem; line-height:1.5; min-height:2.5rem;">
                    @if ($key === 'instagram')Requires a Facebook page linked to an Instagram business account.
                    @elseif ($key === 'threads')Connect your Instagram account first — Threads uses the same Meta app.
                    @else Connect your {{ $platform['name'] }} account to start publishing.
                    @endif
                </p>
            @endif

            {{-- Action buttons --}}
            @if ($connected || $expired)
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
                    <a href="{{ route('platform.connect', ['brand' => $brand->slug, 'platform' => $key]) }}"
                       style="display:flex; align-items:center; justify-content:center; gap:0.375rem; padding:0.625rem; font-size:0.8rem; font-weight:600; color:#475569; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; text-decoration:none;">
                        ↻ Reconnect
                    </a>
                    <form method="POST" action="{{ route('platform.disconnect', ['brand' => $brand->slug, 'platform' => $key]) }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Disconnect {{ $platform['name'] }}? Scheduled posts for this platform will fail.')"
                            style="width:100%; padding:0.625rem; font-size:0.8rem; font-weight:600; color:#DC2626; background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; cursor:pointer;">
                            Disconnect
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('platform.connect', ['brand' => $brand->slug, 'platform' => $key]) }}"
                   style="display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.7rem; font-size:0.875rem; font-weight:600; color:#fff; background:linear-gradient(135deg,#7C3AED,#4338CA); border-radius:8px; text-decoration:none;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    Connect {{ $platform['name'] }}
                </a>
            @endif

        </div>
    @endforeach

</div>
