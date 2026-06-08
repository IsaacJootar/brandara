<x-layouts.app>

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral">My Brand</h1>
        <p class="text-base-content/60 mt-1 text-sm">Everything about your brand. The more you fill in, the better your AI content.</p>
    </div>

    {{-- Tab nav --}}
    <div x-data="{ tab: 'voice' }">

        <div class="tabs tabs-bordered overflow-x-auto">
            <button class="tab font-medium" :class="tab === 'voice' ? 'tab-active' : ''" x-on:click="tab = 'voice'">Brand Voice</button>
            <button class="tab font-medium" :class="tab === 'kit' ? 'tab-active' : ''" x-on:click="tab = 'kit'">Brand Kit</button>
            <button class="tab font-medium" :class="tab === 'profile' ? 'tab-active' : ''" x-on:click="tab = 'profile'">Brand Profile</button>
        </div>

        <div class="mt-6">

            {{-- Brand Voice --}}
            <div x-show="tab === 'voice'">
                @livewire('my-brand.brand-voice')
            </div>

            {{-- Brand Kit (Module 11) --}}
            <div x-show="tab === 'kit'">
                <div class="rounded-2xl border border-base-300 bg-base-100 p-10 text-center">
                    <p class="text-base-content/40 text-sm">Brand Kit — coming in the next update.</p>
                </div>
            </div>

            {{-- Brand Profile (Module 11) --}}
            <div x-show="tab === 'profile'">
                <div class="rounded-2xl border border-base-300 bg-base-100 p-10 text-center">
                    <p class="text-base-content/40 text-sm">Brand Profile — coming in the next update.</p>
                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
