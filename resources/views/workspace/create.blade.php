<x-layouts.auth>
    <x-slot:title>Create your workspace</x-slot>

    <div style="background:#fff; border-radius:18px; box-shadow:0 4px 24px rgba(15,23,42,0.08); overflow:hidden;">

        {{-- Header --}}
        <div style="padding:2rem 2rem 1.5rem; border-bottom:1px solid #F1F5F9;">
            <h1 style="font-size:1.25rem; font-weight:700; color:#0F172A; margin:0 0 0.375rem;">Start building your brand</h1>
            <p style="font-size:0.83rem; color:#94A3B8; margin:0;">7-day free trial &nbsp;·&nbsp; No credit card needed</p>
        </div>

        <div style="padding:1.5rem 2rem 2rem;">

            @if ($errors->any())
                <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.875rem 1rem; margin-bottom:1.25rem;">
                    <ul style="margin:0; padding:0 0 0 1.125rem; font-size:0.82rem; color:#DC2626;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('workspace.store') }}">
                @csrf

                {{-- Group 1: Account --}}
                <div style="margin-bottom:0.875rem;">
                    <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8; margin-bottom:0.5rem;">Your account</div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <input type="text" name="workspace_name" value="{{ old('workspace_name') }}"
                            placeholder="Business or agency name"
                            class="auth-input" required>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="Your full name"
                            class="auth-input" required>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="Work email address"
                            class="auth-input" required>
                    </div>
                </div>

                {{-- Group 2: First brand --}}
                <div style="margin-bottom:0.875rem;">
                    <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8; margin-bottom:0.5rem;">Your first brand</div>
                    <input type="text" name="brand_name" value="{{ old('brand_name') }}"
                        placeholder="Brand name (e.g. Acme Consulting)"
                        class="auth-input" required>
                    <div style="font-size:0.72rem; color:#94A3B8; margin-top:0.3rem; padding-left:0.25rem;">You can add more brands later</div>
                </div>

                {{-- Group 3: Location & password --}}
                <div style="margin-bottom:1.25rem;">
                    <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#94A3B8; margin-bottom:0.5rem;">Location & password</div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <select name="country" class="auth-input" required style="cursor:pointer;">
                            <option value="" disabled {{ old('country') ? '' : 'selected' }}>Your country</option>
                            <option value="NG" {{ old('country')==='NG'?'selected':'' }}>Nigeria</option>
                            <option value="GH" {{ old('country')==='GH'?'selected':'' }}>Ghana</option>
                            <option value="KE" {{ old('country')==='KE'?'selected':'' }}>Kenya</option>
                            <option value="ZA" {{ old('country')==='ZA'?'selected':'' }}>South Africa</option>
                            <option value="RW" {{ old('country')==='RW'?'selected':'' }}>Rwanda</option>
                            <option value="TZ" {{ old('country')==='TZ'?'selected':'' }}>Tanzania</option>
                            <option value="UG" {{ old('country')==='UG'?'selected':'' }}>Uganda</option>
                            <option value="ET" {{ old('country')==='ET'?'selected':'' }}>Ethiopia</option>
                            <option value="SN" {{ old('country')==='SN'?'selected':'' }}>Senegal</option>
                            <option value="CI" {{ old('country')==='CI'?'selected':'' }}>Côte d'Ivoire</option>
                            <option value="CM" {{ old('country')==='CM'?'selected':'' }}>Cameroon</option>
                            <option value="OTHER" {{ old('country')==='OTHER'?'selected':'' }}>Other</option>
                        </select>
                        <input type="password" name="password"
                            placeholder="Create a password (8+ characters)"
                            class="auth-input" required>
                        <input type="password" name="password_confirmation"
                            placeholder="Confirm your password"
                            class="auth-input" required>
                    </div>
                </div>

                <button type="submit"
                    style="width:100%; padding:0.875rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.9rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; transition:opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Start my free trial →
                </button>

                <p style="text-align:center; font-size:0.8rem; color:#94A3B8; margin:1rem 0 0;">
                    Already have a workspace?
                    <a href="{{ route('login') }}" style="color:#7C3AED; text-decoration:none; font-weight:500;">Log in</a>
                </p>

            </form>
        </div>
    </div>

    <p style="text-align:center; font-size:0.72rem; color:#CBD5E1; margin-top:1.25rem;">
        By signing up you agree to our Terms of Service.
    </p>

</x-layouts.auth>
