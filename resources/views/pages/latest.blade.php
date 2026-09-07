@extends('layouts.app')

@section('title', 'Berita Terbaru')
@section('meta_description', 'Kumpulan berita terkini, aktual, dan terpercaya yang diperbarui sepanjang hari dari meja redaksi TopNews.')

@section('content')
<div class="space-y-6 pb-16">
    <x-breaking-news :items="$breaking" />

    <div class="tn-container pt-2">
        <x-breadcrumb :items="[['label' => 'Berita Terbaru', 'url' => null]]" />

        <div class="border-b border-[#111111] pb-4 mb-6">
            <h1 class="font-headline font-black text-2xl sm:text-3xl lg:text-4xl text-[#111111] tracking-tight uppercase">
                Berita Terbaru
            </h1>
            <p class="text-sm text-[#5F6368] mt-1">
                Laporan jurnalistik teraktual, terverifikasi, dan diperbarui berkala 24 jam nonstop.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- News Feed (Cols 1-8) -->
            <div class="lg:col-span-8 space-y-6">
                @if($articles->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach ($articles as $item)
                            <x-news-card 
                                variant="standard"
                                :article="$item"
                            />
                        @endforeach
                    </div>

                    <div class="pt-6">
                        {{ $articles->links() }}
                    </div>
                @else
                    <div class="bg-[#F9FAFB] rounded-[8px] p-8 text-center border border-[#E5E7EB]">
                        <p class="text-sm text-[#6B7280]">Belum ada berita terbaru yang diterbitkan.</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar (Cols 9-12) -->
            <aside class="lg:col-span-4 space-y-6">
                <div class="bg-[#F7F7F7] p-5 rounded-[6px] border border-[#E8E8E8]">
                    <div class="flex items-center gap-2 pb-3 mb-4 border-b border-[#E8E8E8]">
                        <span class="w-2.5 h-2.5 bg-[#E50914] rounded-full"></span>
                        <h2 class="font-headline font-bold text-sm uppercase tracking-wider text-[#111111]">
                            Terpopuler Hari Ini
                        </h2>
                    </div>

                    <div class="space-y-4">
                        @foreach ($trending as $trend)
                            <div class="flex items-start gap-3.5 group">
                                <span class="font-headline font-black text-2xl text-[#CCCCCC] group-hover:text-[#E50914] leading-none shrink-0 w-6 transition-colors">
                                    {{ $trend['rank'] }}
                                </span>
                                <div class="flex-1">
                                    <h3 class="font-bold text-xs md:text-sm text-[#111111] leading-snug group-hover:text-[#E50914] transition-colors font-headline">
                                        <a href="{{ route('news.show', ['slug' => $trend['slug']]) }}">
                                            {{ $trend['title'] }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center gap-2 mt-1 text-[10px] text-[#80868B]">
                                        <span>Dibaca {{ $trend['views'] }} kali</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
