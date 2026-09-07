@extends('layouts.app')

@section('title', ($article->seo_title ?: $article->title) . ' — TopNews')

@push('styles')
@if (!$article->robots_index)
    <meta name="robots" content="noindex, nofollow">
@endif
@if ($article->seo_description)
    <meta name="description" content="{{ $article->seo_description }}">
@elseif ($article->excerpt)
    <meta name="description" content="{{ $article->excerpt }}">
@else
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 160) }}">
@endif
<link rel="canonical" href="{{ $article->canonical_url ?: route('news.show', $article->slug) }}" />

<!-- Open Graph / Social Sharing Metadata -->
<meta property="og:title" content="{{ $article->title }}" />
<meta property="og:description" content="{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 160) }}" />
<meta property="og:type" content="article" />
<meta property="og:url" content="{{ route('news.show', $article->slug) }}" />
@if ($article->featured_image_url)
    <meta property="og:image" content="{{ $article->featured_image_url }}" />
@endif
@endpush

@section('content')
<!-- Reading Progress Indicator Bar -->
<x-reading-progress />

<div class="tn-container py-6 lg:py-10">
    <!-- Print Only Header -->
    <div class="print-only-header hidden">
        <div class="flex items-center justify-between">
            <span class="font-headline font-black text-2xl tracking-tighter text-black">
                TOP<span class="text-[#E50914]">NEWS</span>
            </span>
            <span class="text-xs text-[#555555]">
                {{ $article->published_at?->translatedFormat('d F Y, H:i') ?? $article->created_at->translatedFormat('d F Y') }} WIB
            </span>
        </div>
        <p class="text-[10px] text-[#777777] mt-1">{{ route('news.show', $article->slug) }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 relative">
        <!-- Sticky Share Rail (Desktop Only, Left Column) -->
        <div class="hidden xl:block xl:col-span-1">
            <x-article-share :title="$article->title" :url="route('news.show', $article->slug)" variant="sticky" />
        </div>

        <!-- Main Article Reading Column -->
        <article class="col-span-1 lg:col-span-8 xl:col-span-7 space-y-6">
            <!-- Breadcrumbs Navigation -->
            <div class="no-print">
                <x-breadcrumb :items="[
                    ['label' => 'Beranda', 'url' => route('home')],
                    ['label' => $article->category->name ?? 'Berita', 'url' => isset($article->category) ? route('category.show', $article->category->slug) : route('latest')],
                    ['label' => \Illuminate\Support\Str::limit($article->title, 45)]
                ]" />
            </div>

            <!-- Article Header -->
            <header class="space-y-4 border-b border-[#E5E7EB] pb-6">
                <!-- Context Badges -->
                <div class="flex items-center gap-2 flex-wrap">
                    @if ($article->category)
                        <x-category-label :name="$article->category->name" :slug="$article->category->slug" :color="$article->category->accent_color ?? '#E50914'" />
                    @endif

                    @if ($article->content_type && $article->content_type->value !== 'standard')
                        <span class="px-2 py-0.5 bg-[#111111] text-white text-[10px] font-bold uppercase tracking-wider rounded-[2px]">
                            {{ $article->content_type->label() }}
                        </span>
                    @endif
                    
                    @if ($article->is_breaking)
                        <span class="px-2 py-0.5 bg-[#E50914] text-white text-[10px] font-black uppercase tracking-wider rounded-[2px]">
                            BREAKING NEWS
                        </span>
                    @endif

                    @if ($article->is_sponsored)
                        <span class="px-2 py-0.5 bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] text-[10px] font-black uppercase tracking-wider rounded-[2px]" title="Artikel Bersponsor / Advertorial">
                            ADVERTORIAL / SPONSORED
                            @if($article->sponsor_name)
                                • {{ $article->sponsor_name }}
                            @endif
                        </span>
                    @endif

                    <span class="text-xs text-[#6B7280]">
                        • {{ $article->reading_time ?? 3 }} menit baca
                    </span>
                </div>

                <!-- Primary Headline (H1) -->
                <h1 class="font-headline font-black text-3xl sm:text-4xl lg:text-[2.65rem] text-[#111111] leading-[1.18] tracking-tight">
                    {{ $article->title }}
                </h1>

                <!-- Subtitle Deck -->
                @if ($article->subtitle)
                    <p class="text-lg sm:text-xl text-[#4B5563] font-medium leading-snug">
                        {{ $article->subtitle }}
                    </p>
                @endif

                <!-- Author & Published Metadata -->
                <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-[#F3F4F6]">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('author.show', $article->author->username ?? 'redaksi') }}" class="shrink-0 group">
                            @if ($article->author?->avatar_url)
                                <img src="{{ $article->author->avatar_url }}" alt="{{ $article->author->name }}" class="w-11 h-11 rounded-full object-cover border border-[#E5E7EB] group-hover:opacity-90 transition-opacity">
                            @else
                                <div class="w-11 h-11 rounded-full bg-[#171717] text-white flex items-center justify-center font-bold text-xs group-hover:bg-[#E50914] transition-colors">
                                    {{ $article->author?->initials ?? 'TN' }}
                                </div>
                            @endif
                        </a>
                        <div>
                            <a href="{{ route('author.show', $article->author->username ?? 'redaksi') }}" class="font-headline font-bold text-sm text-[#111111] hover:text-[#E50914] transition-colors block">
                                {{ $article->author?->name ?? 'Redaksi TopNews' }}
                            </a>
                            <span class="text-xs text-[#6B7280] block">
                                Jurnalis TopNews
                            </span>
                        </div>
                    </div>

                    <div class="text-xs text-[#6B7280] sm:text-right space-y-0.5">
                        <time datetime="{{ $article->published_at?->toIso8601String() }}" class="block font-medium text-[#111111]">
                            {{ $article->published_at?->translatedFormat('l, d F Y - H:i') ?? $article->created_at->translatedFormat('d F Y') }} WIB
                        </time>
                        
                        @if ($article->updated_at && $article->published_at && $article->updated_at->diffInMinutes($article->published_at) > 15)
                            <span class="text-[11px] text-[#6B7280] block">
                                Diperbarui: {{ $article->updated_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        @endif

                        @if ($article->editor)
                            <span class="text-[11px] text-[#9CA3AF] block">
                                Editor: {{ $article->editor->name }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Top Social Share Bar & Bookmark -->
                <div class="pt-2 no-print flex items-center justify-between flex-wrap gap-2">
                    <x-article-share :title="$article->title" :url="route('news.show', $article->slug)" variant="inline" />

                    {{-- Bookmark Action --}}
                    <form action="{{ route('news.bookmark', $article) }}" method="POST" class="inline">
                        @csrf
                        @php $isBookmarked = $article->isBookmarkedBy(auth()->user()); @endphp
                        <button
                            type="submit"
                            aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] border text-xs font-semibold transition-colors cursor-pointer {{ $isBookmarked ? 'bg-red-50 border-red-300 text-red-600' : 'bg-[#F3F4F6] border-transparent text-[#374151] hover:border-neutral-300 hover:text-neutral-900' }}"
                            title="{{ $isBookmarked ? 'Hapus dari daftar simpanan' : 'Simpan artikel untuk dibaca nanti' }}"
                        >
                            <svg class="w-3.5 h-3.5 {{ $isBookmarked ? 'fill-red-600 text-red-600' : 'fill-none text-[#6B7280]' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span>{{ $isBookmarked ? 'Tersimpan' : 'Simpan' }}</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Media Section (Video / Photo Story / Standard Featured Image) -->
            @if ($article->content_type === \App\Enums\ArticleType::Video && $article->video)
                {{-- Video Player Layout --}}
                <div class="space-y-2">
                    <div class="relative aspect-16/9 bg-neutral-900 rounded-lg overflow-hidden shadow-md">
                        <iframe
                            src="{{ $article->video->embed_url }}"
                            title="{{ $article->title }}"
                            class="absolute inset-0 w-full h-full border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    </div>
                    @if ($article->featured_image_caption)
                        <p class="text-xs text-neutral-500 italic text-center">
                            {{ $article->featured_image_caption }}
                        </p>
                    @endif
                </div>
            @elseif ($article->featured_image_url && $article->content_type !== \App\Enums\ArticleType::PhotoStory)
                {{-- Standard Featured Image --}}
                <figure class="space-y-2">
                    <div class="aspect-16/9 bg-[#F3F4F6] rounded-[6px] overflow-hidden">
                        <img src="{{ $article->featuredMedia?->large_url ?: $article->featured_image_url }}" 
                             @if($article->featuredMedia)
                                 srcset="{{ $article->featuredMedia->small_url }} 640w, {{ $article->featuredMedia->medium_url }} 1024w, {{ $article->featuredMedia->large_url }} 1600w"
                                 sizes="(max-width: 768px) 100vw, 800px"
                             @endif
                             alt="{{ $article->featured_image_alt ?? $article->title }}" 
                             class="w-full h-full object-cover">
                    </div>
                    @if ($article->featured_image_caption || $article->featuredMedia?->credit)
                        <figcaption class="text-xs text-[#6B7280] italic px-1 text-center flex flex-col sm:flex-row items-center justify-center gap-1">
                            @if ($article->featured_image_caption)
                                <span>{{ $article->featured_image_caption }}</span>
                            @endif
                            @if ($article->featuredMedia?->credit)
                                <span class="text-neutral-400">({{ $article->featuredMedia->credit }})</span>
                            @endif
                        </figcaption>
                    @endif
                </figure>
            @endif

            <!-- Top Article Advertisement -->
            <x-ad-slot key="article_top" />

            <!-- Excerpt Callout -->
            @if ($article->excerpt)
                <div class="p-4 sm:p-5 bg-[#F8F9FA] border-l-4 border-[#E50914] rounded-r text-base sm:text-lg text-[#111111] font-semibold leading-relaxed">
                    {{ $article->excerpt }}
                </div>
            @endif

            @if ($article->is_sponsored)
                <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-[6px] text-xs text-amber-900 leading-relaxed">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <span>PEMBERITAHUAN KONTEN BERSAMPOR / ADVERTORIAL</span>
                    </div>
                    <p>
                        Artikel ini merupakan naskah kemitraan komersial yang disajikan secara transparan. 
                        @if ($article->sponsor_name)
                            Disponsori oleh: 
                            @if ($article->sponsor_url)
                                <a href="{{ $article->sponsor_url }}" target="_blank" rel="sponsored noopener noreferrer" class="font-bold underline text-amber-950 hover:text-black">
                                    {{ $article->sponsor_name }}
                                </a>.
                            @else
                                <strong class="font-bold text-amber-950">{{ $article->sponsor_name }}</strong>.
                            @endif
                        @endif
                        Redaksi TopNews menjaga independensi jurnalisme dan menegakkan pemisahan tegas antara karya editorial dan publikasi promosi.
                    </p>
                </div>
            @endif

            <!-- Sanitized Main Article Content -->
            <div id="article-content-body" class="article-content max-w-none pt-2">
                {!! $sanitizedContent !!}
            </div>

            <!-- Bottom Article Advertisement -->
            <x-ad-slot key="article_bottom" />

            <!-- Sequential Photo Story Gallery (Vertical Storytelling Mode) -->
            @if ($article->content_type === \App\Enums\ArticleType::PhotoStory && $article->photoStory?->gallery)
                <div class="mt-8 space-y-12 border-t-2 border-neutral-900 pt-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-bold text-neutral-900 uppercase tracking-wider">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
                            <span>Galeri Foto Cerita ({{ $article->photoStory->gallery->media->count() }} Foto)</span>
                        </div>
                        @if ($article->photoStory->gallery->photographer_display)
                            <div class="text-xs text-neutral-500">
                                Fotografer: <strong class="text-neutral-900">{{ $article->photoStory->gallery->photographer_display }}</strong>
                            </div>
                        @endif
                    </div>

                    @foreach ($article->photoStory->gallery->media as $idx => $photo)
                        <figure class="space-y-3 bg-neutral-50 rounded-lg p-4 border border-neutral-200">
                            <div class="relative overflow-hidden rounded-md bg-neutral-900">
                                <img
                                    src="{{ $photo->large_url }}"
                                    alt="{{ $photo->pivot->caption_override ?: ($photo->alt_text ?: 'Foto ' . ($idx + 1)) }}"
                                    class="w-full h-auto object-cover max-h-[700px] mx-auto"
                                    loading="lazy"
                                >
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-sm bg-black/80 backdrop-blur-xs text-white text-xs font-bold font-mono">
                                    {{ $idx + 1 }} / {{ $article->photoStory->gallery->media->count() }}
                                </div>
                            </div>
                            <figcaption class="space-y-1">
                                <p class="text-sm text-neutral-800 leading-relaxed font-sans">
                                    {{ $photo->pivot->caption_override ?: ($photo->caption ?: 'Dokumentasi visual TopNews.') }}
                                </p>
                                <div class="text-xs text-neutral-500 flex items-center justify-between">
                                    <span>Foto: <strong>{{ $photo->pivot->credit_override ?: ($photo->credit ?: ($article->photoStory->gallery->photographer_display ?? 'TopNews')) }}</strong></span>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            @endif

            <!-- Editorial Correction / Update Note Box -->
            @if (!empty($article->correction_note))
                <div class="p-4 bg-[#FEF2F2] border border-[#FCA5A5] rounded-[6px] space-y-1">
                    <div class="flex items-center gap-2 text-[#991B1B] text-xs font-bold uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Catatan Koreksi / Pembaruan Redaksi</span>
                    </div>
                    <p class="text-xs text-[#7F1D1D] leading-relaxed">
                        {{ $article->correction_note }}
                    </p>
                </div>
            @endif

            <!-- Article Footer: Tags, Source, Share, Author Box -->
            <footer class="pt-8 border-t border-[#E5E7EB] space-y-6">
                <!-- Source Attribution -->
                @if ($article->source_name)
                    <div class="p-3 bg-[#F9FAFB] rounded border border-[#E5E7EB] text-xs text-[#6B7280]">
                        Sumber Informasi: <strong class="text-[#111111]">{{ $article->source_name }}</strong>
                        @if ($article->source_url)
                            — <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" class="text-[#E50914] hover:underline font-medium">Buka Tautan Sumber</a>
                        @endif
                    </div>
                @endif

                <!-- Tags List -->
                @if ($article->tags->isNotEmpty())
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#6B7280]">Tag Terkait:</span>
                        @foreach ($article->tags as $t)
                            <a href="{{ route('tag.show', $t->slug) }}" class="px-3 py-1 bg-[#F3F4F6] hover:bg-[#FFF1F2] hover:text-[#E50914] text-xs font-semibold text-[#4B5563] rounded-full transition-colors">
                                #{{ $t->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Bottom Social Share & Bookmark -->
                <div class="p-4 bg-[#F9FAFB] rounded-[8px] border border-[#E5E7EB] no-print flex items-center justify-between flex-wrap gap-3">
                    <x-article-share :title="$article->title" :url="route('news.show', $article->slug)" variant="inline" />

                    {{-- Bottom Bookmark Action --}}
                    <form action="{{ route('news.bookmark', $article) }}" method="POST" class="inline">
                        @csrf
                        @php $isBookmarked = $article->isBookmarkedBy(auth()->user()); @endphp
                        <button
                            type="submit"
                            aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-[4px] border text-xs font-semibold transition-colors cursor-pointer {{ $isBookmarked ? 'bg-red-50 border-red-300 text-red-600' : 'bg-white border-[#D1D5DB] text-[#374151] hover:border-neutral-400' }}"
                            title="{{ $isBookmarked ? 'Hapus dari daftar simpanan' : 'Simpan artikel ini' }}"
                        >
                            <svg class="w-4 h-4 {{ $isBookmarked ? 'fill-red-600 text-red-600' : 'fill-none text-[#6B7280]' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                            <span>{{ $isBookmarked ? 'Tersimpan di Akun' : 'Simpan Artikel' }}</span>
                        </button>
                    </form>
                </div>

                <!-- Author Profile Box -->
                <x-author-box :author="$article->author" />

                <!-- Article Reaction Bar (Phase 08) -->
                <x-reaction-bar :article="$article" />

                <!-- Moderated Comments Section (Phase 08) -->
                <x-comments-section :article="$article" />

                <!-- Previous / Next Story Navigation -->
                @if ($previousArticle || $nextArticle)
                    <nav class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-[#E5E7EB] no-print" aria-label="Navigasi Artikel">
                        <!-- Previous Story -->
                        @if ($previousArticle)
                            <a href="{{ route('news.show', $previousArticle->slug) }}" class="p-4 rounded-[6px] border border-[#E5E7EB] hover:border-[#E50914] bg-white hover:bg-[#FFFBFB] transition-all group flex flex-col justify-between">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-[#6B7280] group-hover:text-[#E50914] flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Artikel Sebelumnya
                                </span>
                                <h4 class="font-headline font-bold text-sm text-[#111111] group-hover:text-[#E50914] transition-colors mt-2 line-clamp-2 leading-snug">
                                    {{ $previousArticle->title }}
                                </h4>
                            </a>
                        @else
                            <div></div>
                        @endif

                        <!-- Next Story -->
                        @if ($nextArticle)
                            <a href="{{ route('news.show', $nextArticle->slug) }}" class="p-4 rounded-[6px] border border-[#E5E7EB] hover:border-[#E50914] bg-white hover:bg-[#FFFBFB] transition-all group flex flex-col justify-between text-right sm:text-right">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-[#6B7280] group-hover:text-[#E50914] flex items-center justify-end gap-1">
                                    Artikel Selanjutnya
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                                <h4 class="font-headline font-bold text-sm text-[#111111] group-hover:text-[#E50914] transition-colors mt-2 line-clamp-2 leading-snug">
                                    {{ $nextArticle->title }}
                                </h4>
                            </a>
                        @endif
                    </nav>
                @endif
            </footer>
        </article>

        <!-- Right Sidebar: Trending & Quick Discovery (Cols 9-12 / xl:4) -->
        <aside class="col-span-1 lg:col-span-4 xl:col-span-4 space-y-8 sidebar-wrapper no-print">
            <!-- Trending Sidebar Box -->
            @if (!empty($trending) && count($trending) > 0)
                <div class="bg-white p-5 rounded-[8px] border border-[#E5E7EB] shadow-subtle space-y-4">
                    <x-section-heading title="Sedang Tren" size="sm" />
                    <div class="space-y-3">
                        @foreach ($trending as $idx => $tr)
                            <div class="flex items-start gap-3 border-b border-[#F3F4F6] pb-3 last:border-0 last:pb-0">
                                <span class="font-headline font-black text-2xl text-[#E50914]/80 shrink-0 w-6 leading-none pt-0.5">
                                    0{{ $idx + 1 }}
                                </span>
                                <div>
                                    <a href="{{ route('news.show', $tr->slug ?? $tr['slug']) }}" class="font-headline font-bold text-xs text-[#111111] hover:text-[#E50914] transition-colors block leading-snug line-clamp-2">
                                        {{ $tr->title ?? $tr['title'] }}
                                    </a>
                                    <span class="text-[10px] text-[#6B7280] block mt-1">
                                        {{ $tr->category->name ?? ($tr['category'] ?? 'Trending') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Sidebar Ad Slot -->
            <x-ad-slot key="sidebar" />

            <!-- Editorial Newsletter Component (Phase 10) -->
            <x-newsletter-cta variant="box" source="article" />
        </aside>
    </div>

    <!-- Discovery Sections Below Article (Related & Recommendations) -->
    <div class="mt-16 space-y-12 border-t border-[#E5E7EB] pt-10 article-discovery-sections no-print">
        <!-- 1. Contextual Related Articles -->
        @if ($related->isNotEmpty())
            <section class="space-y-6" aria-labelledby="related-news-heading">
                <div class="flex items-center justify-between">
                    <h2 id="related-news-heading" class="font-headline font-black text-xl text-[#111111] uppercase tracking-tight flex items-center gap-2">
                        <span class="w-1 h-5 bg-[#E50914] rounded-full inline-block"></span>
                        Berita Terkait
                    </h2>
                    @if ($article->category)
                        <a href="{{ route('category.show', $article->category->slug) }}" class="text-xs font-bold text-[#E50914] hover:underline">
                            Lihat Kanal {{ $article->category->name }} &rarr;
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($related as $relArticle)
                        <x-news-card :article="$relArticle" layout="vertical" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 2. Curated Editorial Recommendations -->
        @if ($recommended->isNotEmpty())
            <section class="space-y-6" aria-labelledby="recommended-news-heading">
                <div class="flex items-center justify-between">
                    <h2 id="recommended-news-heading" class="font-headline font-black text-xl text-[#111111] uppercase tracking-tight flex items-center gap-2">
                        <span class="w-1 h-5 bg-[#111111] rounded-full inline-block"></span>
                        Pilihan Bacaan Lainnya
                    </h2>
                    <a href="{{ route('latest') }}" class="text-xs font-bold text-[#111111] hover:text-[#E50914] hover:underline">
                        Lihat Berita Terbaru &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($recommended as $recArticle)
                        <x-news-card :article="$recArticle" layout="vertical" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection
