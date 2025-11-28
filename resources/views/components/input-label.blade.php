@props(['value'])

<label {{ $attributes->merge(['class' => 'fluent-field-label']) }}>
    {{ $value ?? $slot }}
</label>
