@extends('layouts.app')

@section('title', 'Penulis: ' . $author->name . ' — TopNews')

@section('content')
<div class="tn-container py-8 lg:py-12 space-y-8">
    <!-- Author Profile Header -->
    <div class="bg-white p-6 sm:p-8 rounded-[8px] border border-[#E5E7EB] shadow-subtle flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
        @if ($author->avatar_url)
            <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" class="w-20 h-20 rounded-full object-cover border-2 border-[#E50914] shrink-0">
        @else
            <div class="w-20 h-20 rounded-full bg-[#171717] text-white flex items-center justify-center font-bold text-2xl shrink-0">
                {{ $author->initials }}
            </div>
        @endif

        <div class="space-y-2 min-w-0">
            <span class="inline-block px-2.5 py-0.5 bg-[#FFF1F2] text-[#E50914] font-bold text-xs rounded uppercase tracking-wider">
                Jurnalis / Kontributor
            </span>
            <h1 class="font-headline font-black text-2xl sm:text-3xl text-[#111111]">
                {{ $author->name }}
            </h1>
            <p class="text-xs sm:text-sm text-[#6B7280] max-w-xl">
                Jurnalis redaksi TopNews yang meliput perkembangan peristiwa terkini dengan dedikasi pada akurasi dan keberimbangan fakta.
            </p>
            <div class="text-xs text-[#9CA3AF] pt-1">
                Total Berita Terbit: <strong class="text-[#111111]">{{ $articles->total() }}</strong> artikel
            </div>
        </div>
    </div>

    <!-- Author's Published Articles -->
    <div class="space-y-6">
        <x-section-heading :title="'Kumpulan Berita oleh ' . $author->name" />

        @if ($articles->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($articles as $art)
                    <div class="bg-white rounded-[6px] border border-[#E5E7EB] overflow-hidden shadow-subtle hover:border-[#CCCCCC] transition-colors p-4 flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="font-bold text-[10px] px-1.5 py-0.5 rounded" style="color: {{ $art->category->accent_color ?? '#E50914' }}; background-color: {{ $art->category->accent_color ?? '#E50914' }}15;">
                                {{ $art->category->name }}
                            </span>
                            <a href="{{ route('news.show', $art->slug) }}" class="font-headline font-bold text-base text-[#111111] hover:text-[#E50914] transition-colors block line-clamp-2 leading-snug">
                                {{ $art->title }}
                            </a>
                            @if ($art->excerpt)
                                <p class="text-xs text-[#6B7280] line-clamp-2">
                                    {{ $art->excerpt }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-[#F3F4F6] mt-4 flex items-center justify-between text-[11px] text-[#9CA3AF]">
                            <span>{{ $art->published_at?->translatedFormat('d F Y') }}</span>
                            <span>{{ $art->reading_time }} mnt baca</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $articles->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-[8px] border border-[#E5E7EB]">
                <p class="text-sm text-[#6B7280]">Belum ada artikel publik yang diterbitkan oleh penulis ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
