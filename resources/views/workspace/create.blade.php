<x-layouts.auth>
    <x-slot:title>Create your workspace</x-slot>

    <div class="card bg-base-100 shadow-sm border border-base-300">
        <div class="card-body p-8">
            <h1 class="text-2xl font-bold text-neutral mb-1">Start building your brand</h1>
            <p class="text-base-content/60 mb-6 text-sm">7-day free trial · No credit card needed</p>

            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('workspace.store') }}" class="space-y-4">
                @csrf

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Your business or agency name</span>
                    </label>
                    <input type="text" name="workspace_name" value="{{ old('workspace_name') }}"
                        placeholder="e.g. Acme Agency"
                        class="input input-bordered w-full focus:input-primary @error('workspace_name') input-error @enderror"
                        required>
                    <span class="label-text-alt text-base-content/40 mt-1 ml-1">This is your account name</span>
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Your first brand name</span>
                    </label>
                    <input type="text" name="brand_name" value="{{ old('brand_name') }}"
                        placeholder="e.g. Acme Consulting"
                        class="input input-bordered w-full focus:input-primary @error('brand_name') input-error @enderror"
                        required>
                    <span class="label-text-alt text-base-content/40 mt-1 ml-1">You can add more brands later</span>
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Your full name</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="e.g. Amara Okafor"
                        class="input input-bordered w-full focus:input-primary @error('name') input-error @enderror"
                        required>
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Work email address</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="you@company.com"
                        class="input input-bordered w-full focus:input-primary @error('email') input-error @enderror"
                        required>
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Your country</span>
                    </label>
                    <select name="country" class="select select-bordered w-full focus:select-primary @error('country') select-error @enderror" required>
                        <option value="" disabled {{ old('country') ? '' : 'selected' }}>Choose your country</option>
                        <option value="NG" {{ old('country') === 'NG' ? 'selected' : '' }}>Nigeria</option>
                        <option value="GH" {{ old('country') === 'GH' ? 'selected' : '' }}>Ghana</option>
                        <option value="KE" {{ old('country') === 'KE' ? 'selected' : '' }}>Kenya</option>
                        <option value="ZA" {{ old('country') === 'ZA' ? 'selected' : '' }}>South Africa</option>
                        <option value="RW" {{ old('country') === 'RW' ? 'selected' : '' }}>Rwanda</option>
                        <option value="TZ" {{ old('country') === 'TZ' ? 'selected' : '' }}>Tanzania</option>
                        <option value="UG" {{ old('country') === 'UG' ? 'selected' : '' }}>Uganda</option>
                        <option value="ET" {{ old('country') === 'ET' ? 'selected' : '' }}>Ethiopia</option>
                        <option value="SN" {{ old('country') === 'SN' ? 'selected' : '' }}>Senegal</option>
                        <option value="CI" {{ old('country') === 'CI' ? 'selected' : '' }}>Côte d'Ivoire</option>
                        <option value="CM" {{ old('country') === 'CM' ? 'selected' : '' }}>Cameroon</option>
                        <option value="OTHER" {{ old('country') === 'OTHER' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Create a password</span>
                    </label>
                    <input type="password" name="password" placeholder="At least 8 characters"
                        class="input input-bordered w-full focus:input-primary @error('password') input-error @enderror"
                        required>
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Confirm your password</span>
                    </label>
                    <input type="password" name="password_confirmation" placeholder="Repeat your password"
                        class="input input-bordered w-full focus:input-primary" required>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-2">
                    Start my free trial
                </button>

                <p class="text-center text-sm text-base-content/50 mt-2">
                    Already have a workspace?
                    <a href="{{ route('login') }}" class="text-primary hover:underline">Log in</a>
                </p>
            </form>
        </div>
    </div>

    <p class="text-center text-xs text-base-content/40 mt-4">
        By signing up you agree to our Terms of Service.
    </p>
</x-layouts.auth>
