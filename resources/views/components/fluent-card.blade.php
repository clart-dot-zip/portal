@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'normal',
    'hover' => false,
    'header' => null,
    'footer' => null,
])

@php
$baseClasses = 'fluent-card bg-white border border-fluent-neutral-14 rounded-lg transition-all duration-200';
$paddingClasses = [
    'none' => '',
    'small' => 'p-3',
    'normal' => 'p-6',
    'large' => 'p-8',
];
$hoverClass = $hover ? 'hover:shadow-depth8 hover:-translate-y-0.5 cursor-pointer' : '';
$classes = $baseClasses . ' ' . $hoverClass;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($header || $title)
        <div class="fluent-card-header {{ $padding !== 'none' ? 'pb-4 border-b border-fluent-neutral-10' : '' }}">
            @if($header)
                {{ $header }}
            @else
                @if($title)
                    <h3 class="text-base font-semibold text-fluent-neutral-30">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-sm text-fluent-neutral-26 mt-1">{{ $subtitle }}</p>
                @endif
            @endif
        </div>
    @endif
    
    <div class="fluent-card-body {{ $padding !== 'none' ? $paddingClasses[$padding] : '' }} {{ ($header || $title) ? 'pt-4' : '' }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="fluent-card-footer pt-4 border-t border-fluent-neutral-10 {{ $padding !== 'none' ? $paddingClasses[$padding] : '' }}">
            {{ $footer }}
        </div>
    @endif
</div>
