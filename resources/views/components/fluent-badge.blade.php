@props([
    'variant' => 'neutral',
    'size' => 'medium',
    'icon' => null,
])

@php
$baseClasses = 'fluent-badge inline-flex items-center gap-1 font-semibold rounded-full whitespace-nowrap';

$variantClasses = [
    'success' => 'fluent-badge-success bg-green-50 text-fluent-success',
    'warning' => 'fluent-badge-warning bg-yellow-50 text-yellow-700',
    'error' => 'fluent-badge-error bg-red-50 text-fluent-error',
    'info' => 'fluent-badge-info bg-blue-50 text-fluent-brand-60',
    'neutral' => 'fluent-badge-neutral bg-fluent-neutral-8 text-fluent-neutral-30',
    'brand' => 'bg-fluent-brand-10 text-fluent-brand-70',
];

$sizeClasses = [
    'small' => 'text-xs px-2 py-0.5',
    'medium' => 'text-xs px-2.5 py-1',
    'large' => 'text-sm px-3 py-1.5',
];

$classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['neutral']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['medium']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <span class="fluent-badge-icon">{!! $icon !!}</span>
    @endif
    {{ $slot }}
</span>
