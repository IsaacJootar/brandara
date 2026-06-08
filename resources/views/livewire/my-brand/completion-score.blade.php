@php
    $color = $percentage >= 80 ? 'bg-emerald-500' : ($percentage >= 40 ? 'bg-amber-400' : 'bg-red-400');
    $textColor = $percentage >= 80 ? 'text-emerald-700' : ($percentage >= 40 ? 'text-amber-700' : 'text-red-600');
    $bgColor = $percentage >= 80 ? 'bg-emerald-50 border-emerald-200' : ($percentage >= 40 ? 'bg-amber-50 border-amber-200' : 'bg-red-50 border-red-200');
    $missing = array_filter($fields, fn($f) => !$f['done']);
@endphp

<div class="rounded-2xl border {{ $bgColor }} p-4 mb-6">
    <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
        <div>
            <span class="text-sm font-semibold {{ $textColor }}">
                Brand profile {{ $percentage }}% complete
            </span>
            @if($percentage < 100 && count($missing))
                <span class="text-xs text-base-content/50 ml-2">— {{ count($missing) }} field{{ count($missing) !== 1 ? 's' : '' }} left</span>
            @endif
        </div>
        @if($percentage === 100)
            <span class="badge badge-success badge-sm">All done</span>
        @endif
    </div>

    {{-- Progress bar --}}
    <div class="w-full bg-base-200 rounded-full h-2 mb-3">
        <div class="{{ $color }} h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
    </div>

    {{-- Missing fields --}}
    @if($percentage < 100 && count($missing))
        <div class="flex flex-wrap gap-1.5">
            @foreach($missing as $key => $field)
                <span class="badge badge-sm badge-ghost text-base-content/50">
                    {{ $field['label'] }}
                </span>
            @endforeach
        </div>
        <p class="text-xs text-base-content/40 mt-2">Fill in these fields to improve your AI content quality.</p>
    @else
        <p class="text-xs {{ $textColor }}">Your brand is fully set up. Every AI post now has full context.</p>
    @endif
</div>
