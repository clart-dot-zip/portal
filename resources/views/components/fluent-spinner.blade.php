@props([
    'size' => 'medium',
    'label' => null,
])

@php
$sizeClasses = [
    'small' => 'w-4 h-4',
    'medium' => 'w-6 h-6',
    'large' => 'w-8 h-8',
    'xlarge' => 'w-12 h-12',
];

$sizeClass = $sizeClasses[$size] ?? $sizeClasses['medium'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-2']) }}>
    <div class="fluent-spinner {{ $sizeClass }} border-2 border-fluent-neutral-14 border-t-fluent-brand-60 rounded-full animate-spin"></div>
    
    @if($label)
        <p class="text-sm text-fluent-neutral-26">{{ $label }}</p>
    @endif
</div>
