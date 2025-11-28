@props([
    'variant' => 'primary',
    'size' => 'medium',
    'icon' => null,
    'iconPosition' => 'start',
    'loading' => false,
    'disabled' => false,
    'type' => 'button'
])

@php
$baseClasses = 'fluent-button inline-flex items-center justify-center gap-2 font-semibold transition-all duration-100 disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-fluent-brand-60 focus:ring-offset-2';

$variantClasses = [
    'primary' => 'fluent-button-primary bg-fluent-brand-60 text-white hover:bg-fluent-brand-70 active:bg-fluent-brand-80 border border-fluent-brand-60',
    'secondary' => 'fluent-button-secondary bg-white text-fluent-neutral-30 border border-fluent-neutral-14 hover:bg-fluent-neutral-8 active:bg-fluent-neutral-10',
    'subtle' => 'bg-transparent text-fluent-neutral-30 hover:bg-fluent-neutral-8 active:bg-fluent-neutral-10',
    'outline' => 'bg-transparent text-fluent-brand-60 border border-fluent-brand-60 hover:bg-fluent-brand-10 active:bg-fluent-brand-20',
    'danger' => 'bg-fluent-error text-white hover:bg-red-600 active:bg-red-700 border border-fluent-error',
];

$sizeClasses = [
    'small' => 'text-xs px-2 py-1 min-h-[24px] rounded',
    'medium' => 'text-sm px-3 py-1.5 min-h-[32px] rounded',
    'large' => 'text-base px-4 py-2 min-h-[40px] rounded-md',
];

$classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['medium']);
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if($disabled || $loading) disabled @endif
>
    @if($loading)
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @elseif($icon && $iconPosition === 'start')
        <span class="fluent-button-icon">{!! $icon !!}</span>
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'end' && !$loading)
        <span class="fluent-button-icon">{!! $icon !!}</span>
    @endif
</button>
