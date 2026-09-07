@extends('layouts.app')

@section('title', $category['name'] . ' — Berita Terkini')
@section('meta_description', $category['description'])

@section('content')
<div class="space-y-6 pb-16">
    <x-breaking-news :items="$breaking" />

    <div class="tn-container pt-2">
        <x-breadcrumb :items="[['label' => 'Kategori', 'url' => route('latest')], ['label' => $category['name'], 'url' => null]]" />

        <!-- Category Header Banner -->
        <div class="bg-[#F7F7F7] border border-[#E8E8E8] rounded-[6px] p-6 mb-8">
            <span class="inline-block px-2.5 py-0.5 bg-[#E50914] text-white text-[11px] font-black uppercase tracking-wider rounded-[3px] mb-2">
                KATEGORI
            </span>
            <h1 class="font-headline font-black text-2xl sm:text-3xl lg:text-4xl text-[#111111] tracking-tight uppercase">
                {{ $category['name'] }}
            </h1>
            <p class="text-sm text-[#5F6368] mt-2 max-w-2xl leading-relaxed">
                {{ $category['description'] }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Main Category Articles (Cols 1-8) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Featured Article for this category -->
                @if ($featured)
                    <div class="mb-6">
                        <x-news-card 
                            variant="hero"
                            :title="$featured['title']"
                            :url="route('news.show', ['slug' => $featured['slug']])"
                            :image="$featured['image']"
                            :category="$category['name']"
                            :excerpt="$featured['excerpt']"
                            :author="$featured['author']"
                            :date="$featured['published_at']"
                            :readingTime="$featured['reading_time']"
                        />
                    </div>
                @endif

                @if (!empty($articles))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach ($articles as $item)
                            <x-news-card 
                                variant="standard"
                                :title="$item['title']"
                                :url="route('news.show', ['slug' => $item['slug']])"
                                :image="$item['image']"
                                :category="$category['name']"
                                :excerpt="$item['excerpt']"
                                :author="$item['author']"
                                :date="$item['published_at']"
                                :readingTime="$item['reading_time']"
                            />
                        @endforeach
                    </div>

                    <!-- Visual Pagination -->
                    <nav class="flex items-center justify-between border-t border-[#E8E8E8] pt-6 select-none" aria-label="Navigasi Halaman">
                        <span class="text-xs text-[#80868B]">Menampilkan arsip kategori {{ $category['name'] }}</span>
                        <div class="flex items-center gap-1">
                            <button type="button" disabled class="px-3 py-1.5 text-xs font-semibold rounded bg-[#F2F2F2] text-[#A0A0A0] cursor-not-allowed">
                                Sebelumnya
                            </button>
                            <span class="px-3 py-1.5 text-xs font-bold rounded bg-[#E50914] text-white">1</span>
                            <button type="button" disabled class="px-3 py-1.5 text-xs font-semibold rounded bg-[#F2F2F2] text-[#A0A0A0] cursor-not-allowed">
                                Selanjutnya
                            </button>
                        </div>
                    </nav>
                @else
                    <!-- Clean Empty State -->
                    <div class="bg-[#F7F7F7] border border-dashed border-[#CCCCCC] rounded-[6px] p-12 text-center">
                        <svg class="w-12 h-12 text-[#A0A0A0] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <h3 class="font-bold text-base text-[#111111] mb-1">Belum Ada Artikel</h3>
                        <p class="text-xs text-[#5F6368] mb-4">Belum ada berita yang dipublikasikan dalam kategori ini.</p>
                        <x-button variant="secondary" size="sm" :href="route('home')">
                            Kembali ke Beranda
                        </x-button>
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
