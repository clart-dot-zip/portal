@props([
    'name' => '',
    'image' => null,
    'size' => 'medium',
    'status' => null,
])

@php
$sizeClasses = [
    'small' => 'w-6 h-6 text-xs',
    'medium' => 'w-10 h-10 text-sm',
    'large' => 'w-16 h-16 text-lg',
    'xlarge' => 'w-24 h-24 text-2xl',
];

$statusColors = [
    'online' => 'bg-fluent-success',
    'offline' => 'bg-fluent-neutral-22',
    'busy' => 'bg-fluent-error',
    'away' => 'bg-fluent-warning',
];

$sizeClass = $sizeClasses[$size] ?? $sizeClasses['medium'];
$initials = collect(explode(' ', $name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');
@endphp

<div {{ $attributes->merge(['class' => 'relative inline-block']) }}>
    <div class="{{ $sizeClass }} rounded-full overflow-hidden flex items-center justify-center font-semibold {{ $image ? '' : 'bg-fluent-brand-20 text-fluent-brand-70' }}">
        @if($image)
            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover">
        @else
            <span>{{ $initials }}</span>
        @endif
    </div>
    
    @if($status)
        <span class="absolute bottom-0 right-0 block w-3 h-3 rounded-full border-2 border-white {{ $statusColors[$status] ?? $statusColors['offline'] }}"></span>
    @endif
</div>
