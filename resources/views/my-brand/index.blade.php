<x-layouts.app>

    {{-- Page header --}}
    <div class="mb-5">
        <h1 class="text-2xl font-bold text-neutral">My Brand</h1>
        <p class="text-base-content/60 mt-1 text-sm">The more you fill in, the better every AI-generated post.</p>
    </div>

    {{-- Completion score --}}
    @livewire('my-brand.completion-score', ['brand' => $currentBrand])

    {{-- Tabs --}}
    <div x-data="{ tab: 'kit' }">

        {{-- Tab buttons --}}
        <div class="flex gap-0 border-b border-base-300 mb-6 overflow-x-auto">
            <button
                x-on:click="tab = 'kit'"
                :class="tab === 'kit' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-base-content/50 hover:text-neutral'"
                class="px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors"
            >Brand Kit</button>
            <button
                x-on:click="tab = 'profile'"
                :class="tab === 'profile' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-base-content/50 hover:text-neutral'"
                class="px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors"
            >Brand Profile</button>
            <button
                x-on:click="tab = 'voice'"
                :class="tab === 'voice' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-base-content/50 hover:text-neutral'"
                class="px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors"
            >Brand Voice</button>
        </div>

        {{-- Tab panels --}}
        <div x-show="tab === 'kit'">
            @livewire('my-brand.brand-kit', ['brand' => $currentBrand])
        </div>

        <div x-show="tab === 'profile'">
            @livewire('my-brand.brand-profile', ['brand' => $currentBrand])
        </div>

        <div x-show="tab === 'voice'">
            @livewire('my-brand.brand-voice', ['brand' => $currentBrand])
        </div>

    </div>

</x-layouts.app>
