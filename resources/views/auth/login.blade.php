<x-layouts.auth>
    <x-slot:title>Log in</x-slot>

    <div style="background:#fff; border-radius:18px; box-shadow:0 4px 24px rgba(15,23,42,0.08); overflow:hidden;">

        <div style="padding:2rem 2rem 1.5rem; border-bottom:1px solid #F1F5F9;">
            <h1 style="font-size:1.25rem; font-weight:700; color:#0F172A; margin:0 0 0.25rem;">Welcome back</h1>
            <p style="font-size:0.83rem; color:#94A3B8; margin:0;">Log in to your Brandara workspace</p>
        </div>

        <div style="padding:1.5rem 2rem 2rem;">

            @if (session('success'))
                <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.25rem; font-size:0.83rem; color:#16A34A;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:10px; padding:0.75rem 1rem; margin-bottom:1.25rem; font-size:0.83rem; color:#DC2626;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" data-loading-form>
                @csrf

                <div style="display:flex; flex-direction:column; gap:0.625rem; margin-bottom:1rem;">
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="Email address"
                        class="auth-input" required autofocus>
                    <input type="password" name="password"
                        placeholder="Password"
                        class="auth-input" required>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.82rem; color:#64748B;">
                        <input type="checkbox" name="remember" style="accent-color:#7C3AED; width:14px; height:14px;">
                        Keep me logged in
                    </label>
                </div>

                <button type="submit"
                    style="width:100%; padding:0.875rem; background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff; font-size:0.9rem; font-weight:600; border:none; border-radius:10px; cursor:pointer; transition:opacity 0.15s; display:flex; align-items:center; justify-content:center; gap:0.5rem;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <span class="btn-label">Log in</span>
                    <span class="btn-loading" style="display:none; align-items:center; gap:0.5rem;">
                        <span class="btn-spinner"></span> Logging in…
                    </span>
                </button>

                <p style="text-align:center; font-size:0.8rem; color:#94A3B8; margin:1rem 0 0;">
                    Don't have a workspace?
                    <a href="{{ route('workspace.create') }}" style="color:#7C3AED; text-decoration:none; font-weight:500;">Create one free</a>
                </p>

            </form>
        </div>
    </div>

</x-layouts.auth>
