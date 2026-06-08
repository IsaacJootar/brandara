<x-layouts.app>

    {{-- Page header --}}
    <div class="mb-5">
        <h1 class="text-2xl font-bold text-neutral">My Brand</h1>
        <p class="text-base-content/60 mt-1 text-sm">The more you fill in, the better every AI-generated post.</p>
    </div>

    {{-- Completion score --}}
    @livewire('my-brand.completion-score', ['brand' => $currentBrand])

    {{-- Tab nav --}}
    <div x-data="{ tab: 'kit' }">

        <div class="tabs tabs-bordered overflow-x-auto">
            <button class="tab font-semibold" :class="tab === 'kit' ? 'tab-active' : ''" x-on:click="tab = 'kit'">Brand Kit</button>
            <button class="tab font-semibold" :class="tab === 'profile' ? 'tab-active' : ''" x-on:click="tab = 'profile'">Brand Profile</button>
            <button class="tab font-semibold" :class="tab === 'voice' ? 'tab-active' : ''" x-on:click="tab = 'voice'">Brand Voice</button>
        </div>

        <div class="mt-6">

            <div x-show="tab === 'kit'" x-cloak>
                @livewire('my-brand.brand-kit', ['brand' => $currentBrand])
            </div>

            <div x-show="tab === 'profile'" x-cloak>
                @livewire('my-brand.brand-profile', ['brand' => $currentBrand])
            </div>

            <div x-show="tab === 'voice'" x-cloak>
                @livewire('my-brand.brand-voice', ['brand' => $currentBrand])
            </div>

        </div>

    </div>

</x-layouts.app>
