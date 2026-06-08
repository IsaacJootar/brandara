@php
    $barColor  = $percentage >= 80 ? '#10B981' : ($percentage >= 40 ? '#F59E0B' : '#EF4444');
    $missing   = array_filter($fields, fn($f) => !$f['done']);
@endphp

<div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:14px; padding:1rem 1.25rem; margin-bottom:1.5rem;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.625rem; flex-wrap:wrap; gap:0.5rem;">
        <div style="display:flex; align-items:center; gap:0.75rem;">
            <span style="font-size:0.875rem; font-weight:700; color:#0F172A;">
                Brand profile — {{ $percentage }}% complete
            </span>
            @if($percentage < 100 && count($missing))
                <span style="font-size:0.78rem; color:#64748B;">{{ count($missing) }} field{{ count($missing) !== 1 ? 's' : '' }} left</span>
            @endif
        </div>
        @if($percentage === 100)
            <span style="font-size:0.72rem; font-weight:600; color:#065F46; background:#D1FAE5; padding:0.2rem 0.625rem; border-radius:99px; border:1px solid #A7F3D0;">All done ✓</span>
        @endif
    </div>

    {{-- Progress bar --}}
    <div style="width:100%; background:#E2E8F0; border-radius:99px; height:6px; margin-bottom:0.75rem;">
        <div style="background:{{ $barColor }}; height:6px; border-radius:99px; transition:width 0.5s ease; width:{{ $percentage }}%;"></div>
    </div>

    {{-- Missing field chips --}}
    @if($percentage < 100 && count($missing))
        <div style="display:flex; flex-wrap:wrap; gap:0.375rem; margin-bottom:0.375rem;">
            @foreach($missing as $field)
                <span style="font-size:0.72rem; color:#475569; background:#fff; border:1px solid #CBD5E1; padding:0.2rem 0.625rem; border-radius:99px;">
                    {{ $field['label'] }}
                </span>
            @endforeach
        </div>
        <p style="font-size:0.75rem; color:#94A3B8; margin:0;">Fill in these fields to improve your AI content quality.</p>
    @else
        <p style="font-size:0.78rem; color:#059669; margin:0; font-weight:500;">Your brand is fully set up. Every AI post now has full context.</p>
    @endif

</div>
