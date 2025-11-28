@props(['type' => 'submit'])

<x-fluent-button
    :type="$type"
    variant="danger"
    size="medium"
    {{ $attributes }}
>
    {{ $slot }}
</x-fluent-button>
