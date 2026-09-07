@extends('layouts.app')

@section('title', 'TopNews — Berita Terkini, Akurat & Terpercaya')
@section('meta_description', 'TopNews adalah portal berita digital independen menyajikan liputan peristiwa terkini, investigasi mendalam, dan jurnalisme terpercaya.')

@section('content')
<div class="space-y-8 pb-16">
    {{-- Editorial Preview Mode Indicator --}}
    @if ($isPreview ?? false)
        <div class="bg-amber-500 text-black py-2.5 px-4 text-xs font-bold font-headline sticky top-0 z-50 shadow-md">
            <div class="tn-container flex items-center justify-between w-full">
                <span>⚠️ MODE PRATINJAU REDAKSI: Anda sedang melihat komposisi tata letak beranda sesuai konfigurasi CMS. Metrik impresi iklan dinonaktifkan.</span>
                <a href="{{ route('admin.homepage.index') }}" class="px-2.5 py-1 bg-black text-white rounded text-[11px] hover:bg-neutral-800 transition-colors">Kembali ke CMS</a>
            </div>
        </div>
    @endif

    <!-- 1. Breaking News Bar Component -->
    @if (!empty($breakingItems) && $breakingItems->isNotEmpty())
        <div class="bg-[#111111] text-white py-2 border-b border-[#E50914]">
            <div class="tn-container flex items-center gap-3">
                <span class="px-2 py-0.5 bg-[#E50914] text-white font-headline font-black text-[10px] uppercase tracking-wider rounded-[2px] shrink-0">
                    BREAKING NEWS
                </span>
                <div class="overflow-x-auto whitespace-nowrap scrollbar-none flex items-center gap-6 text-xs font-semibold">
                    @foreach ($breakingItems as $item)
                        <a href="{{ $item->getDestinationUrl() ?? '#' }}" class="hover:text-[#E50914] transition-colors flex items-center gap-2" {{ $item->external_url ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                            <span>{{ $item->headline }}</span>
                            @if($item->starts_at)
                                <span class="text-[10px] text-[#9CA3AF]">({{ $item->starts_at->diffForHumans() }})</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @elseif ($breakingNews->isNotEmpty())
        <div class="bg-[#111111] text-white py-2 border-b border-[#E50914]">
            <div class="tn-container flex items-center gap-3">
                <span class="px-2 py-0.5 bg-[#E50914] text-white font-headline font-black text-[10px] uppercase tracking-wider rounded-[2px] shrink-0">
                    BREAKING NEWS
                </span>
                <div class="overflow-x-auto whitespace-nowrap scrollbar-none flex items-center gap-6 text-xs font-semibold">
                    @foreach ($breakingNews as $breaking)
                        <a href="{{ route('news.show', $breaking->slug) }}" class="hover:text-[#E50914] transition-colors flex items-center gap-2">
                            <span>{{ $breaking->title }}</span>
                            <span class="text-[10px] text-[#9CA3AF]">({{ $breaking->published_at?->diffForHumans() }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- 2. Hero Section: Dominant Lead Story + Supporting Top Stories -->
    @if ($leadStory)
        <section class="tn-container pt-2" aria-label="Berita Utama">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                <!-- Lead Story (Cols 1-7, 60-70% visual emphasis) -->
                <div class="lg:col-span-7">
                    <x-news-card 
                        variant="hero"
                        :title="$leadStory->title"
                        :url="route('news.show', $leadStory->slug)"
                        :image="$leadStory->featured_image_url"
                        :category="$leadStory->category->name"
                        :categoryUrl="route('category.show', $leadStory->category->slug)"
                        :excerpt="$leadStory->excerpt ?? $leadStory->subtitle"
                        :author="$leadStory->author?->name ?? 'Redaksi'"
                        :authorAvatar="$leadStory->author?->avatar_url"
                        :date="$leadStory->published_at?->translatedFormat('d F Y - H:i') . ' WIB'"
                        :readingTime="$leadStory->reading_time"
                        :isBreaking="$leadStory->is_breaking"
                        :isSponsored="$leadStory->is_sponsored"
                    />
                </div>

                <!-- Supporting Major Stories (Cols 8-12, 30-40% visual emphasis) -->
                <div class="lg:col-span-5 flex flex-col justify-between space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-[#111111]">
                        <h2 class="font-headline font-bold text-sm uppercase tracking-wider text-[#111111]">
                            Sorotan Utama Hari Ini
                        </h2>
                        <span class="text-[11px] font-semibold text-[#E50914]">Update Terkini</span>
                    </div>

                    @foreach ($supportingStories as $story)
                        <x-news-card 
                            variant="horizontal"
                            :title="$story->title"
                            :url="route('news.show', $story->slug)"
                            :image="$story->featured_image_url"
                            :category="$story->category->name"
                            :categoryUrl="route('category.show', $story->category->slug)"
                            :author="$story->author?->name ?? 'Redaksi'"
                            :date="$story->published_at?->diffForHumans()"
                            :isBreaking="$story->is_breaking"
                            :isSponsored="$story->is_sponsored"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <!-- Graceful Empty State if database has no published articles yet -->
        <section class="tn-container py-12 text-center bg-[#F9FAFB] rounded-[8px] border border-[#E5E7EB]">
            <h1 class="font-headline font-black text-2xl text-[#111111] mb-2">Portal Berita TopNews</h1>
            <p class="text-sm text-[#6B7280] max-w-md mx-auto">
                Belum ada berita yang diterbitkan pada publikasi ini.
            </p>
        </section>
    @endif

    {{-- 2.1 Leaderboard Ad Placement --}}
    <div class="tn-container">
        <x-ad-slot key="home_leaderboard" />
    </div>

    <!-- 3. Latest News & Trending Grid -->
    @if ($latestNews->isNotEmpty())
        <section class="tn-container pt-4" aria-label="Berita Terbaru dan Terpopuler">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Latest News Feed (Cols 1-8) -->
                <div class="lg:col-span-8 space-y-6">
                    <x-section-heading title="Berita Terbaru" subtitle="Informasi aktual terverifikasi sepanjang hari" :link="route('latest')" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach ($latestNews as $item)
                            <x-news-card 
                                variant="standard"
                                :title="$item->title"
                                :url="route('news.show', $item->slug)"
                                :image="$item->featured_image_url"
                                :category="$item->category->name"
                                :categoryUrl="route('category.show', $item->category->slug)"
                                :excerpt="$item->excerpt ?? $item->subtitle"
                                :author="$item->author?->name ?? 'Redaksi'"
                                :date="$item->published_at?->diffForHumans()"
                                :readingTime="$item->reading_time"
                                :isSponsored="$item->is_sponsored"
                            />
                        @endforeach
                    </div>
                </div>

                <!-- Trending / Most Read Sidebar (Cols 9-12) -->
                <aside class="lg:col-span-4 space-y-6" aria-label="Sidebar Berita Terpopuler">
                    <!-- Trending Ranking Block -->
                    @if ($trendingNews->isNotEmpty())
                        <div class="bg-[#F7F7F7] p-5 rounded-[6px] border border-[#E8E8E8]">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E8E8E8]">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 bg-[#E50914] rounded-full"></span>
                                    <h2 class="font-headline font-bold text-sm uppercase tracking-wider text-[#111111]">
                                        Sedang Tren
                                    </h2>
                                </div>
                                <a href="{{ route('trending.index') }}" class="text-[11px] font-bold text-[#E50914] hover:underline">Lihat Semua</a>
                            </div>

                            <div class="space-y-4">
                                @foreach ($trendingNews as $idx => $trend)
                                    <div class="flex items-start gap-3.5 group">
                                        <span class="font-headline font-black text-2xl text-[#CCCCCC] group-hover:text-[#E50914] leading-none shrink-0 w-6 transition-colors select-none">
                                            0{{ $idx + 1 }}
                                        </span>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-xs md:text-sm text-[#111111] leading-snug group-hover:text-[#E50914] transition-colors font-headline">
                                                <a href="{{ route('news.show', $trend->slug) }}">
                                                    {{ $trend->title }}
                                                </a>
                                            </h3>
                                            <div class="flex items-center gap-2 mt-1.5 text-[10px] text-[#80868B]">
                                                <span class="font-semibold text-[#111111]">{{ $trend->category->name }}</span>
                                                <span>•</span>
                                                <span>{{ $trend->published_at?->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Most Read Period Block -->
                    @if ($popularNews->isNotEmpty())
                        <div class="bg-[#FFFFFF] p-5 rounded-[6px] border border-[#E8E8E8] shadow-subtle">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E8E8E8]">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 bg-[#111111] rounded-full"></span>
                                    <h2 class="font-headline font-bold text-sm uppercase tracking-wider text-[#111111]">
                                        Paling Banyak Dibaca
                                    </h2>
                                </div>
                                <a href="{{ route('popular.index') }}" class="text-[11px] font-bold text-[#E50914] hover:underline">Semua</a>
                            </div>

                            <div class="space-y-3.5">
                                @foreach ($popularNews as $popIdx => $pop)
                                    <div class="flex items-start gap-3 group border-b border-[#F3F4F6] pb-3 last:border-0 last:pb-0">
                                        <span class="font-headline font-bold text-base text-[#9CA3AF] shrink-0 w-4">
                                            {{ $popIdx + 1 }}.
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-xs text-[#111111] leading-snug group-hover:text-[#E50914] transition-colors font-headline line-clamp-2">
                                                <a href="{{ route('news.show', $pop->slug) }}">
                                                    {{ $pop->title }}
                                                </a>
                                            </h3>
                                            <span class="text-[10px] text-[#9CA3AF] block mt-1">
                                                {{ $pop->category->name }} • {{ number_format($pop->views_count) }} pembaca
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Ad Placement Space (Medium Rectangle 300x250) -->
                    <x-ad-slot key="sidebar" />
                </aside>
            </div>
        </section>
    @endif

    {{-- Inline Homepage Ad Placement --}}
    <div class="tn-container">
        <x-ad-slot key="home_inline_1" />
    </div>

    <!-- 4. Category Highlights Sections -->
    @if (!empty($categorySections))
        <div class="space-y-12 tn-container pt-4">
            @foreach ($categorySections as $section)
                <section aria-label="Rubrik {{ $section['category']->name }}" class="border-t border-[#E8E8E8] pt-8">
                    <x-section-heading 
                        :title="$section['category']->name" 
                        :subtitle="$section['category']->description" 
                        :link="route('category.show', $section['category']->slug)" 
                    />

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($section['articles'] as $catArticle)
                            <x-news-card 
                                variant="standard"
                                :title="$catArticle->title"
                                :url="route('news.show', $catArticle->slug)"
                                :image="$catArticle->featured_image_url"
                                :category="$section['category']->name"
                                :categoryUrl="route('category.show', $section['category']->slug)"
                                :excerpt="$catArticle->excerpt ?? $catArticle->subtitle"
                                :author="$catArticle->author?->name ?? 'Redaksi'"
                                :date="$catArticle->published_at?->diffForHumans()"
                                :readingTime="$catArticle->reading_time"
                                :isSponsored="$catArticle->is_sponsored"
                            />
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    <!-- 5. Editorial Opinion Column Section (Only if opinion articles exist) -->
    @if ($opinionColumns->isNotEmpty())
        <section class="bg-[#F7F7F7] py-10 border-y border-[#E8E8E8]" aria-label="Kolom Opini Redaksi">
            <div class="tn-container">
                <x-section-heading title="Opini & Kolom Redaksi" subtitle="Sudut pandang independen dari para pakar dan jurnalis senior" :link="route('opinion.index')" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($opinionColumns as $op)
                        <x-news-card 
                            variant="opinion"
                            :title="$op->title"
                            :url="route('news.show', $op->slug)"
                            :author="$op->author?->name ?? 'Kolumnis'"
                            :authorAvatar="$op->author?->avatar_url"
                            :date="$op->published_at?->translatedFormat('d F Y')"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- 6. Editorial Choice / Curated Section (Only if editor choice articles exist) -->
    @if ($editorsChoice->isNotEmpty())
        <section class="tn-container pt-4" aria-label="Pilihan Editor">
            <x-section-heading title="Pilihan Editor" subtitle="Laporan mendalam dan liputan khusus pilihan redaksi TopNews" :link="route('editors-choice.index')" />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($editorsChoice as $choice)
                    <x-news-card 
                        variant="standard"
                        :title="$choice->title"
                        :url="route('news.show', $choice->slug)"
                        :image="$choice->featured_image_url"
                        :category="$choice->category->name"
                        :categoryUrl="route('category.show', $choice->category->slug)"
                        :excerpt="$choice->excerpt ?? $choice->subtitle"
                        :author="$choice->author?->name ?? 'Redaksi'"
                        :date="$choice->published_at?->diffForHumans()"
                        :readingTime="$choice->reading_time"
                        :isSponsored="$choice->is_sponsored"
                    />
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
