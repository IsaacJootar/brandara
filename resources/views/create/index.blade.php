<x-layouts.app>

    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Create</h1>
            <p style="font-size:0.875rem; color:#64748B; margin:0;">
                Write a post for <strong style="color:#0F172A;">{{ $brand->name }}</strong>
            </p>
        </div>
        @if($brand->brand_voice)
            <a href="{{ route('my-brand', ['brand' => $brand->slug]) }}"
               style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem 0.875rem; background:#ECFDF5; border:1px solid #A7F3D0; border-radius:8px; font-size:0.8rem; color:#059669; font-weight:500; text-decoration:none;">
                <span style="width:8px;height:8px;border-radius:50%;background:#10B981;display:inline-block;"></span>
                Brand Voice active
            </a>
        @else
            <a href="{{ route('my-brand', ['brand' => $brand->slug]) }}"
               style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem 0.875rem; background:#FFFBEB; border:1px solid #FDE68A; border-radius:8px; font-size:0.8rem; color:#D97706; font-weight:500; text-decoration:none;">
                ✨ Set up Brand Voice for better AI content
            </a>
        @endif
    </div>

    @if (session('success'))
        <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:0.875rem 1.125rem; margin-bottom:1.5rem; font-size:0.875rem; color:#16A34A;">{{ session('success') }}</div>
    @endif

    @livewire('create.pillar-alert', ['brand' => $brand])

    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:1.5rem; margin-bottom:1.75rem;">
        @livewire('post-composer', ['brand' => $brand])
    </div>

    {{-- TikTok Toolkit card --}}
    <a href="{{ route('create.tiktok', ['brand' => $brand->slug]) }}"
       style="display:flex; align-items:center; gap:1rem; background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1rem 1.25rem; margin-bottom:1.75rem; text-decoration:none; transition:border-color 0.15s, box-shadow 0.15s;"
       onmouseover="this.style.borderColor='#FE2C55';this.style.boxShadow='0 2px 12px rgba(254,44,85,0.08)'"
       onmouseout="this.style.borderColor='#E2E8F0';this.style.boxShadow='none'">
        <div style="width:40px;height:40px;background:linear-gradient(135deg,#FE2C55,#010101);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:20px;height:20px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.868V15.13a1 1 0 01-1.447.9L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
            </svg>
        </div>
        <div style="flex:1; min-width:0;">
            <div style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">TikTok Toolkit</div>
            <div style="font-size:0.8rem; color:#64748B;">Script · Caption · Overlays · Hashtags · Bio — one click</div>
        </div>
        <svg style="width:16px;height:16px;color:#CBD5E1;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    {{-- Carousel & Cards link --}}
    <a href="{{ route('create.carousel', ['brand' => $brand->slug]) }}"
       style="display:flex; align-items:center; gap:1rem; background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1rem 1.25rem; margin-bottom:1.75rem; text-decoration:none; transition:border-color 0.15s, box-shadow 0.15s;"
       onmouseover="this.style.borderColor='#7C3AED';this.style.boxShadow='0 2px 12px rgba(124,58,237,0.08)'"
       onmouseout="this.style.borderColor='#E2E8F0';this.style.boxShadow='none'">
        <div style="width:40px;height:40px;background:linear-gradient(135deg,#7C3AED,#A78BFA);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:20px;height:20px;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
        <div style="flex:1; min-width:0;">
            <div style="font-size:0.9rem; font-weight:700; color:#0F172A; margin:0 0 0.2rem;">Carousel &amp; Cards</div>
            <div style="font-size:0.8rem; color:#64748B;">Slide copy · Quote cards · Client testimonials · Canva-ready</div>
        </div>
        <svg style="width:16px;height:16px;color:#CBD5E1;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    @if ($drafts->isNotEmpty())
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; overflow:hidden;">
            <div style="padding:1rem 1.5rem; border-bottom:1px solid #F1F5F9; display:flex; align-items:center; justify-content:space-between;">
                <div style="font-size:0.9rem; font-weight:600; color:#0F172A;">Recent drafts</div>
                <a href="{{ route('schedule', ['brand' => $brand->slug]) }}" style="font-size:0.8rem; color:#7C3AED; text-decoration:none; font-weight:500;">View all →</a>
            </div>
            @foreach ($drafts as $draft)
                <div style="display:flex; align-items:center; gap:1rem; padding:0.875rem 1.5rem; {{ !$loop->last ? 'border-bottom:1px solid #F8FAFC;' : '' }}">
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:0.85rem; font-weight:500; color:#0F172A; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ Str::limit($draft->raw_input ?? 'Untitled draft', 80) }}
                        </div>
                        <div style="font-size:0.75rem; color:#94A3B8; margin-top:0.2rem;">
                            {{ $draft->updated_at->diffForHumans() }} · {{ implode(', ', array_keys($draft->platform_contents ?? [])) }}
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
                        <span style="font-size:0.72rem; font-weight:600; color:#64748B; background:#F1F5F9; padding:0.25rem 0.625rem; border-radius:99px;">Not published yet</span>
                        <form method="POST" action="{{ route('post.destroy', ['brand' => $brand->slug, 'post' => $draft->id]) }}">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this draft?')"
                                style="background:none; border:none; color:#94A3B8; cursor:pointer; font-size:0.8rem; padding:0.25rem 0.5rem;" title="Delete">✕</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-layouts.app>
