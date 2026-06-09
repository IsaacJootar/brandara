<x-layouts.app>

    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.75rem; flex-wrap:wrap;">
        <a href="{{ route('create', ['brand' => $brand->slug]) }}"
           style="display:flex; align-items:center; gap:0.375rem; font-size:0.8rem; color:#94A3B8; text-decoration:none; font-weight:500;">
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Create
        </a>
        <span style="color:#E2E8F0; font-size:0.8rem;">/</span>
        <span style="font-size:0.8rem; color:#0F172A; font-weight:600;">WhatsApp Assistant</span>
    </div>

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <div style="display:flex; align-items:center; gap:0.875rem;">
            <div style="width:42px;height:42px;background:#25D366;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:22px;height:22px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:1.25rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">WhatsApp Assistant</h1>
                <p style="font-size:0.82rem; color:#64748B; margin:0;">Broadcast · Status · Promo · Follow-up — copy that feels human</p>
            </div>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
        @livewire('create.whatsapp-assistant', ['brand' => $brand])
    </div>

</x-layouts.app>
