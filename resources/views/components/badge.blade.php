@props([
    'variant' => 'primary', // 'primary', 'breaking', 'dark', 'subtle', 'sponsored'
    'size' => 'sm', // 'xs', 'sm', 'md'
])

@php
    $variants = [
        'primary' => 'bg-[#E50914] text-white font-bold',
        'breaking' => 'bg-[#E50914] text-white font-black animate-pulse uppercase tracking-wider',
        'dark' => 'bg-[#111111] text-white font-semibold',
        'subtle' => 'bg-[#F2F2F2] text-[#5F6368] hover:bg-[#E8E8E8] hover:text-[#111111] font-medium border border-[#E8E8E8]',
        'sponsored' => 'bg-[#FFF8E1] text-[#B78103] font-semibold border border-[#FFE082]',
        'success' => 'bg-[#E6F4EA] text-[#137333] font-semibold',
    ];

    $sizes = [
        'xs' => 'text-[10px] px-1.5 py-0.5 rounded-[2px]',
        'sm' => 'text-xs px-2 py-0.5 rounded-[3px]',
        'md' => 'text-sm px-2.5 py-1 rounded-[4px]',
    ];

    $classes = ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['sm']) . ' inline-flex items-center gap-1 uppercase tracking-tight transition-colors';
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
