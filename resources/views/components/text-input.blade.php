@props(['disabled' => false])

@php
	$inputClasses = 'form-control';
	if ($attributes->get('readonly')) {
		$inputClasses .= ' bg-light';
	}
	if ($errors->has($attributes->get('name'))) {
		$inputClasses .= ' is-invalid';
	}
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => $inputClasses]) }}>
