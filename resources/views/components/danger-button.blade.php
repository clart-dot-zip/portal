<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger btn-sm text-uppercase font-weight-bold']) }}>
    {{ $slot }}
</button>
