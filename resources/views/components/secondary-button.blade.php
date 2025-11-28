@props(['type' => 'button'])

<x-fluent-button
    :type="$type"
    variant="secondary"
    size="medium"
    {{ $attributes }}
>
    {{ $slot }}
</x-fluent-button>
