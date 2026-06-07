@props([
    'type' => 'submit',
    'wire' => null,         // wire:click target, e.g. "saveDraft"
    'loadingText' => null,  // text to show while loading (defaults to slot)
    'variant' => 'primary', // primary | secondary | ghost
])

@php
    $base = 'display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 1.25rem; font-size:0.875rem; font-weight:600; border-radius:10px; cursor:pointer; transition:opacity 0.15s; border:1px solid transparent;';

    $variantStyle = match ($variant) {
        'secondary' => 'background:#F8FAFC; color:#475569; border-color:#E2E8F0;',
        'ghost'     => 'background:transparent; color:#475569;',
        default     => 'background:linear-gradient(135deg,#7C3AED,#4338CA); color:#fff;',
    };
@endphp

<button
    type="{{ $type }}"
    @if ($wire) wire:click="{{ $wire }}" wire:loading.attr="disabled" wire:target="{{ $wire }}" @endif
    {{ $attributes->merge(['style' => $base.$variantStyle]) }}
>
    @if ($wire)
        {{-- Livewire-driven loading state --}}
        <span wire:loading.remove wire:target="{{ $wire }}" style="display:inline-flex; align-items:center; gap:0.5rem;">
            {{ $slot }}
        </span>
        <span wire:loading wire:target="{{ $wire }}" style="display:none; align-items:center; gap:0.5rem;">
            <span class="btn-spinner"></span>
            {{ $loadingText ?? 'Working...' }}
        </span>
    @else
        {{-- Plain form submit — JS adds .is-submitting and spinner via CSS --}}
        <span class="btn-label" style="display:inline-flex; align-items:center; gap:0.5rem;">
            {{ $slot }}
        </span>
        <span class="btn-loading" style="display:none; align-items:center; gap:0.5rem;">
            <span class="btn-spinner"></span>
            {{ $loadingText ?? 'Working...' }}
        </span>
    @endif
</button>

@once
    @push('styles')
        <style>
            button.is-submitting .btn-label { display: none !important; }
            button.is-submitting .btn-loading { display: inline-flex !important; }
            [wire\:loading][wire\:loading\:display] { display: inline-flex !important; }
        </style>
    @endpush
@endonce
