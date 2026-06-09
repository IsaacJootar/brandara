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
        <span style="font-size:0.8rem; color:#0F172A; font-weight:600;">Carousel &amp; Cards</span>
    </div>

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <div style="display:flex; align-items:center; gap:0.875rem;">
            <div style="width:42px;height:42px;background:linear-gradient(135deg,#7C3AED,#A78BFA);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:22px;height:22px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:1.25rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">Carousel &amp; Cards</h1>
                <p style="font-size:0.82rem; color:#64748B; margin:0;">Carousel slide copy · Quote cards · Testimonials · Motivational graphics</p>
            </div>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem;">
        @livewire('create.carousel-generator', ['brand' => $brand])
    </div>

</x-layouts.app>
