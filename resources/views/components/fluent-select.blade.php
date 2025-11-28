@props([
    'label' => null,
    'options' => [],
    'error' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Select an option',
])

@php
$selectClasses = 'fluent-input w-full text-sm px-3 py-2 min-h-[32px] rounded border transition-all duration-100
    bg-white text-fluent-neutral-30 border-fluent-neutral-14
    hover:border-fluent-neutral-22 
    focus:outline-none focus:border-fluent-brand-60 focus:ring-1 focus:ring-fluent-brand-60
    disabled:bg-fluent-neutral-8 disabled:cursor-not-allowed
    appearance-none bg-no-repeat bg-right pr-10
    ' . ($error ? 'border-fluent-error focus:border-fluent-error focus:ring-fluent-error' : '');

$bgImage = "data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e";
@endphp

<div class="fluent-select-container">
    @if($label)
        <label {{ $attributes->only('id')->merge(['for' => $attributes->get('id')]) }} class="block text-sm font-semibold text-fluent-neutral-30 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-fluent-error">*</span>
            @endif
        </label>
    @endif
    
    <select
        {{ $attributes->merge(['class' => $selectClasses, 'style' => 'background-image: url(\'' . $bgImage . '\'); background-position: right 0.5rem center; background-size: 1.5em 1.5em;']) }}
        @if($disabled) disabled @endif
        @if($required) required @endif
    >
        @if($placeholder)
            <option value="" disabled selected>{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
        
        {{ $slot }}
    </select>
    
    @if($error)
        <p class="mt-1 text-xs text-fluent-error flex items-center gap-1">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                <path d="M6 1a5 5 0 110 10A5 5 0 016 1zm.5 3v2.5a.5.5 0 01-1 0V4a.5.5 0 011 0zm-.5 4a.5.5 0 110 1 .5.5 0 010-1z"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
