@extends('layouts.app')

@section('title', 'Pilihan Editor (Editor\'s Choice) — TopNews')
@section('meta_description', 'Kumpulan liputan pilihan, laporan khusus, dan naskah berita unggulan yang direkomendasikan langsung oleh dewan redaksi TopNews.')

@section('content')
<div class="tn-container py-8 lg:py-12 space-y-8">
    <div class="space-y-2 border-b border-[#E5E7EB] pb-6">
        <span class="text-xs font-bold text-[#E50914] uppercase tracking-wider">Kurasi Redaksi</span>
        <h1 class="font-headline font-black text-3xl sm:text-4xl text-[#111111]">
            Pilihan Editor
        </h1>
        <p class="text-xs sm:text-sm text-[#6B7280] max-w-2xl">
            Liputan mendalam, jurnalisme berbobot, dan laporan investigasi yang dipilih secara khusus oleh redaktur utama TopNews.
        </p>
    </div>

    @if ($articles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($articles as $art)
                <x-news-card 
                    variant="standard"
                    :title="$art->title"
                    :url="route('news.show', $art->slug)"
                    :image="$art->featured_image_url"
                    :category="$art->category->name"
                    :categoryUrl="route('category.show', $art->category->slug)"
                    :excerpt="$art->excerpt ?? $art->subtitle"
                    :author="$art->author?->name ?? 'Redaksi'"
                    :date="$art->published_at?->diffForHumans()"
                    :readingTime="$art->reading_time"
                    :isSponsored="$art->is_sponsored"
                />
            @endforeach
        </div>

        <div class="pt-6">
            {{ $articles->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-[#F9FAFB] rounded-[8px] border border-[#E5E7EB]">
            <p class="text-sm text-[#6B7280]">Belum ada artikel pilihan editor yang diterbitkan.</p>
        </div>
    @endif
</div>
@endsection
