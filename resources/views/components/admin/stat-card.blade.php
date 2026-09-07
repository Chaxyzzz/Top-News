@props([
    'label',
    'value',
    'helper' => null,
    'variant' => 'default',
])

@php
    $valueColor = match ($variant) {
        'primary' => 'text-[#E50914]',
        'success' => 'text-[#137333]',
        'warning' => 'text-[#B78103]',
        'danger' => 'text-[#C8102E]',
        default => 'text-[#171717]',
    };

    $labelColor = match ($variant) {
        'primary' => 'text-[#E50914]',
        'success' => 'text-[#137333]',
        'warning' => 'text-[#B78103]',
        default => 'text-[#6B7280]',
    };
@endphp

<div class="bg-white p-5 rounded-[6px] border border-[#E5E7EB] shadow-subtle hover:border-[#CCCCCC] transition-colors flex flex-col justify-between">
    <div class="flex items-center justify-between gap-2 mb-2">
        <span class="text-xs font-bold uppercase tracking-wider {{ $labelColor }}">
            {{ $label }}
        </span>
        @if (isset($icon))
            <div class="text-[#9CA3AF]">
                {{ $icon }}
            </div>
        @endif
    </div>

    <div>
        <span class="font-headline font-black text-3xl tracking-tight {{ $valueColor }}">
            {{ $value }}
        </span>
        @if ($helper)
            <span class="text-[11px] text-[#6B7280] block mt-1 leading-snug">
                {{ $helper }}
            </span>
        @endif
    </div>
</div>
