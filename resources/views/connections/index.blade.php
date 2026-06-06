<x-layouts.app>
    <div style="margin-bottom:1.75rem;">
        <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Connections</h1>
        <p style="font-size:0.875rem; color:#64748B; margin:0;">Connect <strong style="color:#0F172A;">{{ $brand->name }}</strong> to your social platforms.</p>
    </div>
    @if (session("success"))
        <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:0.875rem 1.125rem; margin-bottom:1.5rem; font-size:0.875rem; color:#16A34A;">{{ session("success") }}</div>
    @endif
    @if (session("error"))
        <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.875rem 1.125rem; margin-bottom:1.5rem; font-size:0.875rem; color:#DC2626;">{{ session("error") }}</div>
    @endif
    @include("connections.partials.grid")
    <div style="margin-top:1.75rem; padding:1rem 1.25rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; font-size:0.82rem; color:#64748B;">
        <strong style="color:#0F172A;">Before connecting:</strong> Add API credentials to your .env file.
        LinkedIn needs LINKEDIN_CLIENT_ID + LINKEDIN_CLIENT_SECRET.
        X needs TWITTER_CLIENT_ID + TWITTER_CLIENT_SECRET.
        Facebook/Instagram/Threads share META_APP_ID + META_APP_SECRET.
    </div>
</x-layouts.app>