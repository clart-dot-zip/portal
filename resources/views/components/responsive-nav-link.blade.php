@props(['active'])

@php
    $classes = 'dropdown-item';
    if ($active ?? false) {
        $classes .= ' active font-weight-bold';
    }
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
