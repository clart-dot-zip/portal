@props(['disabled' => false])

@php
	$name = $attributes->get('name');
	$hasError = $name && $errors->has($name);
	$baseClasses = 'fluent-input';
	if ($hasError) {
		$baseClasses .= ' border-fluent-error focus:border-fluent-error focus:ring-0';
	}
@endphp

<input
	@disabled($disabled)
	{{ $attributes->merge(['class' => $baseClasses]) }}
/>
