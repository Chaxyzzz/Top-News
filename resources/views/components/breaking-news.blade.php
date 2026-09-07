@props([
    'items' => [],
])

@if (!empty($items))
<section class="bg-[#111111] text-white py-2 overflow-hidden border-b border-[#222222]" aria-label="Berita Utama Terkini">
    <div class="tn-container flex items-center gap-3">
        <div class="shrink-0 flex items-center gap-1.5 bg-[#E50914] text-white px-2.5 py-1 rounded-[3px] text-[11px] font-black uppercase tracking-wider select-none">
            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
            BREAKING NEWS
        </div>
        <div class="overflow-hidden relative w-full flex items-center">
            <div class="tn-ticker-content flex items-center gap-8 text-xs md:text-sm font-medium">
                @foreach ($items as $item)
                    <a href="{{ $item['url'] ?? '#' }}" class="hover:text-[#E50914] transition-colors flex items-center gap-2">
                        <span>{{ $item['headline'] }}</span>
                        @if (!empty($item['time']))
                            <span class="text-[#80868B] text-xs font-normal">({{ $item['time'] }})</span>
                        @endif
                    </a>
                    @if (!$loop->last)
                        <span class="text-[#444444] select-none">•</span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
