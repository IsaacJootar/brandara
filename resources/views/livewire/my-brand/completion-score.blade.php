@php
    $barColor   = $percentage >= 80 ? '#10B981' : ($percentage >= 40 ? '#F59E0B' : '#EF4444');
    $textColor  = $percentage >= 80 ? '#065F46' : ($percentage >= 40 ? '#92400E' : '#991B1B');
    $bgColor    = $percentage >= 80 ? '#ECFDF5' : ($percentage >= 40 ? '#FFFBEB' : '#FEF2F2');
    $borderColor= $percentage >= 80 ? '#A7F3D0' : ($percentage >= 40 ? '#FDE68A' : '#FECACA');
    $missing    = array_filter($fields, fn($f) => !$f['done']);
@endphp

<div style="background:{{ $bgColor }}; border:1px solid {{ $borderColor }}; border-radius:14px; padding:1rem 1.25rem; margin-bottom:1.5rem;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.625rem; flex-wrap:wrap; gap:0.5rem;">
        <div>
            <span style="font-size:0.85rem; font-weight:700; color:{{ $textColor }};">
                Brand profile {{ $percentage }}% complete
            </span>
            @if($percentage < 100 && count($missing))
                <span style="font-size:0.78rem; color:#94A3B8; margin-left:0.5rem;">— {{ count($missing) }} field{{ count($missing) !== 1 ? 's' : '' }} left</span>
            @endif
        </div>
        @if($percentage === 100)
            <span style="font-size:0.72rem; font-weight:600; color:#065F46; background:#D1FAE5; padding:0.2rem 0.625rem; border-radius:99px;">All done</span>
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
                <span style="font-size:0.72rem; color:#64748B; background:#F1F5F9; border:1px solid #E2E8F0; padding:0.15rem 0.5rem; border-radius:99px;">
                    {{ $field['label'] }}
                </span>
            @endforeach
        </div>
        <p style="font-size:0.75rem; color:#94A3B8; margin:0;">Fill in these fields to improve your AI content quality.</p>
    @else
        <p style="font-size:0.78rem; color:{{ $textColor }}; margin:0;">Your brand is fully set up. Every AI post now has full context.</p>
    @endif

</div>
