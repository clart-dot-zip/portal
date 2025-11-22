<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-portal-primary text-uppercase font-weight-bold btn-sm']) }}>
    {{ $slot }}
</button>
