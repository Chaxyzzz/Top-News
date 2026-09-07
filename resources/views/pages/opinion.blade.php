@extends('layouts.app')

@section('title', 'Kolom Opini & Analisis Redaksi — TopNews')
@section('meta_description', 'Kumpulan pemikiran, analisis independen, dan perspektif mendalam dari para pakar dan jurnalis senior TopNews.')

@section('content')
<div class="tn-container py-8 lg:py-12 space-y-8">
    <div class="space-y-2 border-b border-[#E5E7EB] pb-6">
        <span class="text-xs font-bold text-[#E50914] uppercase tracking-wider">Rubrik Khusus</span>
        <h1 class="font-headline font-black text-3xl sm:text-4xl text-[#111111]">
            Opini & Kolom Redaksi
        </h1>
        <p class="text-xs sm:text-sm text-[#6B7280] max-w-2xl">
            Ruang dialektika ide, ulasan kebijakan publik, dan pandangan kritis terhadap isu-isu penting nasional dan global.
        </p>
    </div>

    @if ($articles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($articles as $art)
                <x-news-card 
                    variant="opinion"
                    :title="$art->title"
                    :url="route('news.show', $art->slug)"
                    :author="$art->author?->name ?? 'Kolumnis TopNews'"
                    :authorAvatar="$art->author?->avatar_url"
                    :date="$art->published_at?->translatedFormat('d F Y')"
                />
            @endforeach
        </div>

        <div class="pt-6">
            {{ $articles->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-[#F9FAFB] rounded-[8px] border border-[#E5E7EB]">
            <p class="text-sm text-[#6B7280]">Belum ada artikel opini yang diterbitkan.</p>
        </div>
    @endif
</div>
@endsection
