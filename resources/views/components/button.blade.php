@props([
    'variant' => 'primary', // 'primary', 'secondary', 'outline', 'ghost', 'danger'
    'size' => 'md', // 'sm', 'md', 'lg'
    'type' => 'button',
    'href' => null,
])

@php
    $baseStyles = 'inline-flex items-center justify-center font-semibold tracking-tight transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer select-none';

    $variants = [
        'primary' => 'bg-[#E50914] text-white hover:bg-[#C8102E] focus:ring-[#E50914] shadow-sm',
        'secondary' => 'bg-[#111111] text-white hover:bg-[#222222] focus:ring-[#111111]',
        'outline' => 'bg-transparent text-[#111111] border border-[#E8E8E8] hover:border-[#111111] hover:bg-[#F7F7F7] focus:ring-[#111111]',
        'ghost' => 'bg-transparent text-[#5F6368] hover:text-[#111111] hover:bg-[#F2F2F2] focus:ring-[#111111]',
        'danger' => 'bg-[#D93025] text-white hover:bg-[#B31D13] focus:ring-[#D93025]',
    ];

    $sizes = [
        'sm' => 'text-xs px-3 py-1.5 rounded-[4px] gap-1.5',
        'md' => 'text-sm px-4 py-2 rounded-[5px] gap-2',
        'lg' => 'text-base px-5 py-2.5 rounded-[6px] gap-2.5',
        'icon' => 'p-2 rounded-[5px]',
    ];

    $classes = $baseStyles . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
