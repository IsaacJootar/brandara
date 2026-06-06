<x-layouts.auth>
    <x-slot:title>Log in</x-slot>

    <div class="card bg-base-100 shadow-sm border border-base-300">
        <div class="card-body p-8">
            @if (session('workspace_created'))
                <div class="alert alert-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Your workspace is ready. Log in to get started.</span>
                </div>
            @endif

            <h1 class="text-2xl font-bold text-neutral mb-1">Welcome back</h1>
            <p class="text-base-content/60 mb-6 text-sm">
                Log in to {{ tenant() ? tenant('name') : config('app.name') }}
            </p>

            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <span class="text-sm">{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.login') }}" class="space-y-4">
                @csrf

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Email address</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="input input-bordered w-full focus:input-primary"
                        required
                        autofocus
                    >
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text font-medium text-neutral">Password</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="input input-bordered w-full focus:input-primary"
                        required
                    >
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-sm">
                        <span class="text-sm text-base-content/70">Keep me logged in</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    Log in
                </button>

                <p class="text-center text-sm text-base-content/50 mt-2">
                    Don't have a workspace yet?
                    <a href="http://localhost:8000/get-started" class="text-primary hover:underline">Create one free</a>
                </p>
            </form>
        </div>
    </div>
</x-layouts.auth>
