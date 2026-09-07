@props([
    'items' => [], // array of ['label' => string, 'url' => string|null]
])

<nav class="flex items-center gap-2 text-xs text-[#80868B] py-3 select-none" aria-label="Breadcrumb">
    <a href="{{ route('home') }}" class="hover:text-[#111111] transition-colors font-medium">Beranda</a>
    
    @foreach ($items as $item)
        <span class="text-[#CCCCCC] font-normal">/</span>
        @if (!empty($item['url']) && !$loop->last)
            <a href="{{ $item['url'] }}" class="hover:text-[#E50914] transition-colors font-medium">
                {{ $item['label'] }}
            </a>
        @else
            <span class="text-[#111111] font-semibold truncate max-w-[280px] md:max-w-md" aria-current="page">
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</nav>
