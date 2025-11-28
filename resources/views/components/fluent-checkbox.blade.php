@props([
    'label' => null,
    'checked' => false,
    'disabled' => false,
])

<div class="fluent-checkbox-container flex items-start">
    <div class="flex items-center h-5">
        <input
            type="checkbox"
            {{ $attributes->merge(['class' => 'fluent-checkbox w-4 h-4 rounded border-fluent-neutral-14 text-fluent-brand-60 focus:ring-2 focus:ring-fluent-brand-60 focus:ring-offset-0 transition-colors disabled:opacity-40 disabled:cursor-not-allowed']) }}
            @if($checked) checked @endif
            @if($disabled) disabled @endif
        >
    </div>
    
    @if($label || $slot->isNotEmpty())
        <div class="ml-2 text-sm">
            <label {{ $attributes->only('id')->merge(['for' => $attributes->get('id')]) }} class="font-medium text-fluent-neutral-30 cursor-pointer">
                {{ $label ?? $slot }}
            </label>
        </div>
    @endif
</div>
