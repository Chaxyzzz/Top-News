@props([
    'variant' => 'default',
    'size' => 'xs',
])

@php
    $classes = match ($variant) {
        'primary' => 'bg-[#FFF1F2] text-[#E50914] border-[#FECDD3]',
        'success' => 'bg-[#E6F4EA] text-[#137333] border-[#CEEAD6]',
        'warning' => 'bg-[#FFF8E1] text-[#B78103] border-[#FFE082]',
        'danger' => 'bg-[#FDE8E9] text-[#C8102E] border-[#FAD2CF]',
        'dark' => 'bg-[#171717] text-white border-[#171717]',
        default => 'bg-[#F3F4F6] text-[#4B5563] border-[#E5E7EB]',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-2.5 py-1 text-xs',
        default => 'px-2 py-0.5 text-[11px]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-bold tracking-tight rounded-[3px] border {$classes} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
