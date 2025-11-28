@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label text-muted text-uppercase small mb-1']) }}>
    {{ $value ?? $slot }}
</label>
