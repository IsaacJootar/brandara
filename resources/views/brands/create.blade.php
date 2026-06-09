<x-layouts.app>

    <div style="max-width:480px; margin:0 auto; padding-top:1rem;">

        <div style="margin-bottom:1.75rem;">
            <a href="{{ route('home') }}"
               style="display:inline-flex; align-items:center; gap:0.375rem; font-size:0.8rem; color:#94A3B8; text-decoration:none; font-weight:500; margin-bottom:1rem;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <h1 style="font-size:1.375rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Add a new brand</h1>
            <p style="font-size:0.875rem; color:#64748B; margin:0;">Each brand has its own content, voice, platforms, and analytics.</p>
        </div>

        @if(session('error'))
            <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.875rem 1rem; margin-bottom:1.25rem; font-size:0.875rem; color:#991B1B;">
                {{ session('error') }}
            </div>
        @endif

        @php
            $workspace = auth()->user()->workspace;
            $plan = app(\App\Services\Plan\PlanFeatureService::class);
            $current = $workspace->brands()->count();
            $limit = $plan->brandLimit($workspace->plan);
            $planLabel = $plan->planLabel($workspace->plan);
        @endphp

        {{-- Brand count indicator --}}
        <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:0.82rem; color:#64748B;">Brands on your plan</span>
            <span style="font-size:0.82rem; font-weight:700; color:#0F172A;">
                {{ $current }} / {{ $limit === 0 ? '∞' : $limit }}
                <span style="font-weight:400; color:#94A3B8; margin-left:0.25rem;">({{ $planLabel }})</span>
            </span>
        </div>

        <form method="POST" action="{{ route('brand.store') }}">
            @csrf
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:1.5rem;">

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">
                        Brand name <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g. Lagos Consulting Group, My Personal Brand"
                        class="auth-input" style="font-size:0.9rem;">
                    @error('name')
                        <p style="color:#EF4444; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                    <p style="font-size:0.75rem; color:#94A3B8; margin-top:0.375rem;">You'll set up your Brand Voice, Kit, and Identity after creating it.</p>
                </div>

                <button type="submit"
                    style="width:100%; padding:0.75rem 1.5rem; background:#7C3AED; color:#fff; font-size:0.875rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Create brand
                </button>
            </div>
        </form>

    </div>

</x-layouts.app>
