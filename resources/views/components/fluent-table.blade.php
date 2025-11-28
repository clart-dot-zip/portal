@props([
    'headers' => [],
    'compact' => false,
    'hoverable' => true,
])

@php
$baseClasses = 'fluent-table w-full border-collapse';
$rowHoverClass = $hoverable ? 'hover:bg-fluent-neutral-6' : '';
$cellPadding = $compact ? 'px-3 py-2' : 'px-4 py-3';
@endphp

<div class="overflow-x-auto border border-fluent-neutral-14 rounded-lg">
    <table {{ $attributes->merge(['class' => $baseClasses]) }}>
        @if(!empty($headers))
            <thead class="bg-fluent-neutral-6">
                <tr>
                    @foreach($headers as $header)
                        <th class="text-left text-xs font-semibold text-fluent-neutral-30 uppercase tracking-wider {{ $cellPadding }} border-b border-fluent-neutral-14">
                            {{ is_array($header) ? $header['label'] : $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        
        <tbody class="bg-white divide-y divide-fluent-neutral-10">
            {{ $slot }}
        </tbody>
    </table>
</div>
