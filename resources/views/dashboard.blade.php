<x-layouts.app>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-neutral">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ explode(' ', auth()->user()->name)[0] }} 👋
        </h1>
        <p class="text-base-content/60 mt-1">
            Here's what's happening with <strong>{{ $brand->name }}</strong> today.
        </p>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="card bg-[#F5F3FF] border border-[#EDE9FE]">
            <div class="card-body p-5">
                <p class="text-xs font-medium text-base-content/50 uppercase tracking-wide">Posts published</p>
                <p class="text-3xl font-bold text-[#6D28D9] mt-1">{{ $postsThisMonth }}</p>
                <p class="text-xs text-base-content/40 mt-1">This month</p>
            </div>
        </div>
        <div class="card bg-[#EFF6FF] border border-[#DBEAFE]">
            <div class="card-body p-5">
                <p class="text-xs font-medium text-base-content/50 uppercase tracking-wide">Platforms connected</p>
                <p class="text-3xl font-bold text-[#1D4ED8] mt-1">{{ $activeConnections }}</p>
                <p class="text-xs text-base-content/40 mt-1">Active connections</p>
            </div>
        </div>
        <div class="card bg-[#FFFBEB] border border-[#FEF3C7]">
            <div class="card-body p-5">
                <p class="text-xs font-medium text-base-content/50 uppercase tracking-wide">Warm leads</p>
                <p class="text-3xl font-bold text-[#B45309] mt-1">{{ $warmLeads }}</p>
                <p class="text-xs text-base-content/40 mt-1">Tracked this week</p>
            </div>
        </div>
        <div class="card bg-[#FFF1F2] border border-[#FFE4E6]">
            <div class="card-body p-5">
                <p class="text-xs font-medium text-base-content/50 uppercase tracking-wide">Engagement rate</p>
                <p class="text-3xl font-bold text-[#BE123C] mt-1">—</p>
                <p class="text-xs text-base-content/40 mt-1">Connect platforms first</p>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('create', ['brand' => $brand->slug]) }}"
           class="card bg-base-100 border border-base-300 hover:border-primary hover:shadow-sm transition-all group">
            <div class="card-body p-5">
                <div class="w-10 h-10 rounded-lg bg-[#F5F3FF] flex items-center justify-center mb-3">
                    <x-heroicon-o-pencil-square class="w-5 h-5 text-[#7C3AED]" />
                </div>
                <h3 class="font-semibold text-neutral group-hover:text-primary transition-colors">Write a post</h3>
                <p class="text-sm text-base-content/60 mt-1">Generate AI content or write manually</p>
            </div>
        </a>
        <a href="{{ route('connections', ['brand' => $brand->slug]) }}"
           class="card bg-base-100 border border-base-300 hover:border-primary hover:shadow-sm transition-all group">
            <div class="card-body p-5">
                <div class="w-10 h-10 rounded-lg bg-[#F5F3FF] flex items-center justify-center mb-3">
                    <x-heroicon-o-link class="w-5 h-5 text-[#7C3AED]" />
                </div>
                <h3 class="font-semibold text-neutral group-hover:text-primary transition-colors">Connect platforms</h3>
                <p class="text-sm text-base-content/60 mt-1">Link LinkedIn, X, Instagram and more</p>
            </div>
        </a>
        <a href="{{ route('my-brand', ['brand' => $brand->slug]) }}"
           class="card bg-base-100 border border-base-300 hover:border-primary hover:shadow-sm transition-all group">
            <div class="card-body p-5">
                <div class="w-10 h-10 rounded-lg bg-[#F5F3FF] flex items-center justify-center mb-3">
                    <x-heroicon-o-star class="w-5 h-5 text-[#7C3AED]" />
                </div>
                <h3 class="font-semibold text-neutral group-hover:text-primary transition-colors">Set up My Brand</h3>
                <p class="text-sm text-base-content/60 mt-1">Add your voice, colours, and brand profile</p>
            </div>
        </a>
    </div>

    {{-- Getting started checklist --}}
    <div class="card bg-base-100 border border-base-300">
        <div class="card-body p-6">
            <h2 class="font-semibold text-neutral mb-4">Get {{ $brand->name }} ready in 3 steps</h2>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200">
                    <div class="w-6 h-6 rounded-full border-2 border-base-300 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-base-content/40">1</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-neutral">Set up your brand profile</p>
                        <p class="text-xs text-base-content/50">Add your mission, voice, and colours</p>
                    </div>
                    <a href="{{ route('my-brand', ['brand' => $brand->slug]) }}" class="btn btn-xs btn-primary">Start</a>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200">
                    <div class="w-6 h-6 rounded-full border-2 border-base-300 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-base-content/40">2</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-neutral">Connect your social platforms</p>
                        <p class="text-xs text-base-content/50">LinkedIn, X, Instagram, and more</p>
                    </div>
                    <a href="{{ route('connections', ['brand' => $brand->slug]) }}" class="btn btn-xs btn-primary">Connect</a>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg bg-base-200">
                    <div class="w-6 h-6 rounded-full border-2 border-base-300 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-base-content/40">3</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-neutral">Write your first post</p>
                        <p class="text-xs text-base-content/50">Let AI write 3 versions for you</p>
                    </div>
                    <a href="{{ route('create', ['brand' => $brand->slug]) }}" class="btn btn-xs btn-primary">Write a post</a>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
