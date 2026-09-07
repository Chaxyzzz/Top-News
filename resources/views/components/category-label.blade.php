@props([
    'category' => null,
    'name' => null,
    'slug' => null,
    'url' => null,
    'color' => null,
    'variant' => 'text', // 'text', 'badge'
])

@php
    $label = $name ?? $category ?? 'Berita';
    $targetUrl = $url ?? ($slug ? route('category.show', $slug) : '#');
@endphp

@if ($variant === 'badge')
    <a href="{{ $targetUrl }}" class="inline-block px-2 py-0.5 bg-[#111111] text-white text-[11px] font-bold uppercase tracking-wider rounded-[3px] hover:bg-[#E50914] transition-colors">
        {{ $label }}
    </a>
@else
    <a href="{{ $targetUrl }}" class="inline-block text-xs font-black uppercase tracking-wider hover:text-[#C8102E] transition-colors" style="color: {{ $color ?? '#E50914' }};">
        {{ $label }}
    </a>
@endif
