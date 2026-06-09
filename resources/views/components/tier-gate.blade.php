@props(['feature'])
@php
    use App\Services\Plan\PlanFeatureService;
    $svc          = app(PlanFeatureService::class);
    $workspace    = currentWorkspace();
    $allowed      = $workspace ? $svc->workspaceHas($workspace, $feature) : false;
    $gate         = $svc->gate($feature);
    $upgradeTo    = $gate['upgrade_to'] ?? 'pro';
    $upgradeLabel = $svc->planLabel($upgradeTo);
@endphp

@if($allowed)
    {{ $slot }}
@else
    <div style="background:#FAFBFF; border:2px dashed #E2E8F0; border-radius:16px; padding:2.5rem 2rem; text-align:center;">
        <div style="width:48px;height:48px;background:#F1F5F9;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <svg style="width:22px;height:22px;color:#94A3B8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <p style="font-size:0.95rem;font-weight:700;color:#0F172A;margin:0 0 0.375rem;">{{ $gate['label'] ?? 'This feature' }} is on the {{ $upgradeLabel }} plan</p>
        <p style="font-size:0.85rem;color:#64748B;margin:0 0 1.25rem;max-width:380px;margin-left:auto;margin-right:auto;line-height:1.6;">{{ $gate['description'] ?? 'Upgrade to unlock this feature.' }}</p>
        <a href="{{ route('home') }}"
           style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.375rem;background:#7C3AED;color:#fff;font-size:0.875rem;font-weight:600;border-radius:10px;text-decoration:none;transition:opacity 0.15s;"
           onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            Upgrade to {{ $upgradeLabel }}
            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <p style="font-size:0.75rem;color:#CBD5E1;margin:0.75rem 0 0;">You're on the {{ $svc->planLabel(currentPlan()) }} plan</p>
    </div>
@endif