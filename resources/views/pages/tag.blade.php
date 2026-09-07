@extends('layouts.app')

@section('title', 'Topik #' . $tag->name . ' — TopNews')

@section('content')
<div class="tn-container py-8 lg:py-12 space-y-8">
    <div class="space-y-2 border-b border-[#E5E7EB] pb-6">
        <span class="text-xs font-bold text-[#E50914] uppercase tracking-wider">Topik Tagar</span>
        <h1 class="font-headline font-black text-3xl sm:text-4xl text-[#111111]">
            #{{ $tag->name }}
        </h1>
        <p class="text-xs text-[#6B7280]">
            Kumpulan berita, analisis, dan liputan khusus terkait topik #{{ $tag->name }}.
        </p>
    </div>

    <div class="space-y-6">
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
                            <span>{{ $art->author?->name ?? 'Redaksi' }} • {{ $art->published_at?->translatedFormat('d M Y') }}</span>
                            <span>{{ $art->reading_time }} mnt</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $articles->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-[8px] border border-[#E5E7EB]">
                <p class="text-sm text-[#6B7280]">Belum ada artikel publik yang memiliki tagar ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
