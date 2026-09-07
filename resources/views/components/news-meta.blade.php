@props([
    'author' => null,
    'authorAvatar' => null,
    'date' => null,
    'readingTime' => null,
    'size' => 'sm', // 'xs', 'sm', 'md'
])

@php
    $textSizes = [
        'xs' => 'text-[11px]',
        'sm' => 'text-xs',
        'md' => 'text-sm',
    ];
    $textSize = $textSizes[$size] ?? $textSizes['sm'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 text-[#80868B] ' . $textSize]) }}>
    @if ($authorAvatar)
        <img src="{{ $authorAvatar }}" alt="{{ $author }}" class="w-5 h-5 rounded-full object-cover">
    @endif

    @if ($author)
        <span class="font-semibold text-[#111111]">{{ $author }}</span>
        <span>•</span>
    @endif

    @if ($date)
        <time>{{ $date }}</time>
    @endif

    @if ($readingTime)
        <span>•</span>
        <span class="inline-flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-[#80868B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $readingTime }} mnt baca
        </span>
    @endif
</div>
