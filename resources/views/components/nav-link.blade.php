@props(['active'])

@php
    $classes = 'inline-flex items-center px-3 py-2 text-sm rounded-md transition-colors ';
    $classes .= ($active ?? false)
        ? 'bg-fluent-brand-10 text-fluent-brand-80 font-semibold'
        : 'text-fluent-neutral-30 hover:bg-fluent-neutral-8';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
