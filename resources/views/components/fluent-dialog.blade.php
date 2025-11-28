@props([
    'name',
    'title' => null,
    'show' => false,
    'maxWidth' => 'md',
    'closeable' => true,
])

@php
$maxWidthClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '4xl' => 'max-w-4xl',
    'full' => 'max-w-full',
];

$maxWidthClass = $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['md'];
@endphp

<div
    x-data="{
        show: @js($show),
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
            {{ $closeable ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm"
        @if($closeable) @click="show = false" @endif
    ></div>

    <!-- Dialog -->
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white rounded-lg shadow-depth64 w-full {{ $maxWidthClass }} max-h-[90vh] flex flex-col"
        @click.stop
    >
        @if($title || $closeable)
            <div class="flex items-center justify-between px-6 py-4 border-b border-fluent-neutral-10">
                @if($title)
                    <h2 class="text-lg font-semibold text-fluent-neutral-30">{{ $title }}</h2>
                @endif
                
                @if($closeable)
                    <button
                        @click="show = false"
                        type="button"
                        class="ml-auto text-fluent-neutral-26 hover:text-fluent-neutral-30 hover:bg-fluent-neutral-8 rounded p-1 transition-colors"
                        aria-label="Close dialog"
                    >
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8.707 8l4.647-4.646a.5.5 0 00-.708-.708L8 7.293 3.354 2.646a.5.5 0 10-.708.708L7.293 8l-4.647 4.646a.5.5 0 00.708.708L8 8.707l4.646 4.647a.5.5 0 00.708-.708L8.707 8z"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endif

        <div class="px-6 py-4 overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>
