@props(['active'])

@php
    $classes = 'nav-link';
    if ($active ?? false) {
        $classes .= ' active font-weight-bold';
    }
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
