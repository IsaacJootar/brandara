<x-layouts.auth>
    <x-slot:title>Upgrade your plan</x-slot>
    <div class="card bg-base-100 shadow-sm border border-base-300">
        <div class="card-body p-8 text-center">
            <h1 class="text-xl font-bold text-neutral mb-2">Upgrade to keep going</h1>
            <p class="text-base-content/60 text-sm mb-6">{{ $reason ?? 'Your trial has ended.' }}</p>
            <a href="mailto:hello@brandara.co" class="btn btn-primary">Contact us to upgrade</a>
        </div>
    </div>
</x-layouts.auth>
