@extends('layouts.app')

@section('title', 'Berita Terpopuler & Paling Banyak Dibaca — TopNews')
@section('meta_description', 'Daftar berita paling banyak dibaca di portal TopNews berdasarkan volume pembaca aktual.')

@section('content')
<div class="tn-container py-8 lg:py-12 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-[#E5E7EB] pb-6">
        <div class="space-y-2">
            <span class="text-xs font-bold text-[#E50914] uppercase tracking-wider">Metrik Pembaca</span>
            <h1 class="font-headline font-black text-3xl sm:text-4xl text-[#111111]">
                Paling Banyak Dibaca
            </h1>
            <p class="text-xs sm:text-sm text-[#6B7280]">
                Artikel dengan jumlah kunjungan dan minat pembaca tertinggi.
            </p>
        </div>

        <!-- Timeframe Period Filter Tabs -->
        <div class="flex items-center gap-1 bg-[#F3F4F6] p-1 rounded-[6px] text-xs font-bold font-headline select-none">
            <a href="{{ route('popular.index', ['period' => 'today']) }}" class="px-3 py-1.5 rounded-[4px] transition-colors {{ $currentPeriod === 'today' ? 'bg-white text-[#E50914] shadow-xs' : 'text-[#6B7280] hover:text-[#171717]' }}">
                Hari Ini
            </a>
            <a href="{{ route('popular.index', ['period' => '7days']) }}" class="px-3 py-1.5 rounded-[4px] transition-colors {{ $currentPeriod === '7days' ? 'bg-white text-[#E50914] shadow-xs' : 'text-[#6B7280] hover:text-[#171717]' }}">
                7 Hari
            </a>
            <a href="{{ route('popular.index', ['period' => '30days']) }}" class="px-3 py-1.5 rounded-[4px] transition-colors {{ $currentPeriod === '30days' ? 'bg-white text-[#E50914] shadow-xs' : 'text-[#6B7280] hover:text-[#171717]' }}">
                30 Hari
            </a>
        </div>
    </div>

    @if ($articles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($articles as $idx => $art)
                <div class="relative bg-white rounded-[6px] border border-[#E5E7EB] overflow-hidden shadow-subtle hover:border-[#CCCCCC] transition-all p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="font-headline font-black text-xl text-[#E50914]">#0{{ $idx + 1 }}</span>
                            <span class="text-[10px] text-[#6B7280] font-semibold bg-[#F3F4F6] px-2 py-0.5 rounded">
                                {{ number_format($art->views_count) }} pembaca
                            </span>
                        </div>

                        <span class="font-bold text-[10px] px-1.5 py-0.5 rounded" style="color: {{ $art->category->accent_color ?? '#E50914' }}; background-color: {{ $art->category->accent_color ?? '#E50914' }}15;">
                            {{ $art->category->name }}
                        </span>

                        <h3 class="font-headline font-bold text-base text-[#111111] hover:text-[#E50914] transition-colors block line-clamp-2 leading-snug mt-1.5">
                            <a href="{{ route('news.show', $art->slug) }}">
                                {{ $art->title }}
                            </a>
                        </h3>

                        @if ($art->excerpt)
                            <p class="text-xs text-[#6B7280] line-clamp-2 mt-1.5">
                                {{ $art->excerpt }}
                            </p>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-[#F3F4F6] mt-4 flex items-center justify-between text-[11px] text-[#9CA3AF]">
                        <span>{{ $art->published_at?->translatedFormat('d M Y') }}</span>
                        <span>{{ $art->reading_time }} mnt baca</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-[#F9FAFB] rounded-[8px] border border-[#E5E7EB]">
            <p class="text-sm text-[#6B7280]">Belum ada data keterbacaan untuk periode ini.</p>
        </div>
    @endif
</div>
@endsection
