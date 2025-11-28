@props([
    'label' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => '',
    'rows' => 4,
    'hint' => null,
])

@php
$textareaClasses = 'fluent-input w-full text-sm px-3 py-2 rounded border transition-all duration-100
    bg-white text-fluent-neutral-30 border-fluent-neutral-14
    hover:border-fluent-neutral-22 
    focus:outline-none focus:border-fluent-brand-60 focus:ring-1 focus:ring-fluent-brand-60
    disabled:bg-fluent-neutral-8 disabled:cursor-not-allowed resize-y
    ' . ($error ? 'border-fluent-error focus:border-fluent-error focus:ring-fluent-error' : '');
@endphp

<div class="fluent-textarea-container">
    @if($label)
        <label {{ $attributes->only('id')->merge(['for' => $attributes->get('id')]) }} class="block text-sm font-semibold text-fluent-neutral-30 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-fluent-error">*</span>
            @endif
        </label>
    @endif
    
    <textarea
        {{ $attributes->merge(['class' => $textareaClasses]) }}
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @if($disabled) disabled @endif
        @if($required) required @endif
    >{{ $slot }}</textarea>
    
    @if($hint && !$error)
        <p class="mt-1 text-xs text-fluent-neutral-26">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="mt-1 text-xs text-fluent-error flex items-center gap-1">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                <path d="M6 1a5 5 0 110 10A5 5 0 016 1zm.5 3v2.5a.5.5 0 01-1 0V4a.5.5 0 011 0zm-.5 4a.5.5 0 110 1 .5.5 0 010-1z"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
