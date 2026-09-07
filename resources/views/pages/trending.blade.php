@extends('layouts.app')

@section('title', 'Berita Sedang Tren — TopNews')
@section('meta_description', 'Liputan dan berita yang sedang mendapat akselerasi perhatian pembaca di TopNews.')

@section('content')
<div class="tn-container py-8 lg:py-12 space-y-8">
    <div class="space-y-2 border-b border-[#E5E7EB] pb-6">
        <span class="text-xs font-bold text-[#E50914] uppercase tracking-wider">Akselerasi Tren</span>
        <h1 class="font-headline font-black text-3xl sm:text-4xl text-[#111111]">
            Sedang Tren (Trending)
        </h1>
        <p class="text-xs sm:text-sm text-[#6B7280] max-w-2xl">
            Peringkat berita dengan pergerakan minat pembaca tercepat berdasarkan kombinasi aktualitas dan kecepatan membaca.
        </p>
    </div>

    @if ($articles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($articles as $idx => $art)
                <div class="relative bg-white rounded-[6px] border border-[#E5E7EB] overflow-hidden shadow-subtle hover:border-[#CCCCCC] transition-all p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="font-headline font-black text-2xl text-[#E50914]">0{{ $idx + 1 }}</span>
                            <span class="text-[10px] text-[#E50914] font-bold bg-[#FFF1F2] px-2 py-0.5 rounded">
                                Rising Fast 🔥
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
                        <span>{{ $art->published_at?->diffForHumans() }}</span>
                        <span>{{ $art->reading_time }} mnt baca</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-[#F9FAFB] rounded-[8px] border border-[#E5E7EB]">
            <p class="text-sm text-[#6B7280]">Belum ada data tren yang mencukupi.</p>
        </div>
    @endif
</div>
@endsection
