<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline-secondary btn-sm text-uppercase font-weight-bold']) }}>
    {{ $slot }}
</button>
