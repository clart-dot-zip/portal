@props(['type' => 'submit'])

<x-fluent-button
    :type="$type"
    variant="primary"
    size="medium"
    {{ $attributes }}
>
    {{ $slot }}
</x-fluent-button>
