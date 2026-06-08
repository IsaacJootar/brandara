<div>
@if($neglected->isNotEmpty())
    <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:12px; padding:0.875rem 1rem; margin-bottom:1.25rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem;">
        <div style="display:flex; gap:0.75rem; align-items:flex-start; flex:1;">
            <svg style="width:16px; height:16px; color:#D97706; flex-shrink:0; margin-top:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <p style="font-size:0.83rem; font-weight:600; color:#92400E; margin:0 0 0.375rem;">Pillars going quiet</p>
                <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                    @foreach($neglected as $item)
                        <span style="display:inline-flex; align-items:center; gap:0.375rem; font-size:0.78rem; color:#78350F; background:#FEF3C7; border:1px solid #FDE68A; padding:0.25rem 0.625rem; border-radius:99px;">
                            <span style="width:8px; height:8px; border-radius:50%; background:{{ $item['pillar']->color }}; flex-shrink:0; display:inline-block;"></span>
                            {{ $item['pillar']->name }}
                            — {{ $item['days'] === 999 ? 'never used' : $item['days'] . ' days ago' }}
                        </span>
                    @endforeach
                </div>
                <p style="font-size:0.75rem; color:#92400E; margin:0.375rem 0 0;">Write a post for one of these pillars to keep your content balanced.</p>
            </div>
        </div>
        <button type="button" wire:click="dismiss"
            style="flex-shrink:0; background:none; border:none; cursor:pointer; color:#D97706; padding:0; font-size:1rem; line-height:1;"
            title="Dismiss">✕</button>
    </div>
@endif
</div>
