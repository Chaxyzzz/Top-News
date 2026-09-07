@props([
    'article' => null,
    'variant' => 'standard', // 'hero', 'standard', 'horizontal', 'compact', 'opinion', 'featured'
    'title' => null,
    'url' => null,
    'image' => null,
    'category' => null,
    'categoryUrl' => null,
    'excerpt' => null,
    'author' => null,
    'authorAvatar' => null,
    'date' => null,
    'readingTime' => null,
    'isBreaking' => false,
    'isSponsored' => false,
    'imageAspect' => 'aspect-16/9',
    'layout' => null,
])

@php
    if ($article) {
        $title = $title ?? $article->title;
        $url = $url ?? route('news.show', $article->slug);
        $image = $image ?? ($article->featuredMedia?->url ?? $article->featured_image);
        $category = $category ?? $article->category?->name;
        $categoryUrl = $categoryUrl ?? ($article->category ? route('category.show', $article->category->slug) : '#');
        $excerpt = $excerpt ?? $article->excerpt;
        $author = $author ?? $article->author?->name;
        $authorAvatar = $authorAvatar ?? ($article->author?->avatar_url ?? null);
        $date = $date ?? $article->published_at?->diffForHumans();
        $readingTime = $readingTime ?? ($article->reading_time ? $article->reading_time . ' mnt baca' : null);
        $isBreaking = $isBreaking || (bool) $article->is_breaking;
        $isSponsored = $isSponsored || (bool) $article->is_sponsored;
    }
    $title = $title ?? '';
    $url = $url ?? '#';
    $categoryUrl = $categoryUrl ?? '#';
@endphp

@if ($variant === 'hero')
    <article class="group relative flex flex-col bg-white overflow-hidden rounded-[6px] border border-[#E8E8E8] hover:border-[#CCCCCC] transition-all duration-200">
        @if ($image)
            <a href="{{ $url }}" class="tn-card-img-wrapper block aspect-16/9 w-full bg-[#171717] overflow-hidden" aria-label="{{ $title }}">
                <img src="{{ $image }}" alt="{{ $title }}" loading="eager" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @if ($isBreaking)
                    <div class="absolute top-3 left-3">
                        <x-badge variant="breaking">BREAKING NEWS</x-badge>
                    </div>
                @elseif ($category)
                    <div class="absolute top-3 left-3">
                        <x-badge variant="dark">{{ $category }}</x-badge>
                    </div>
                @endif
            </a>
        @endif

        <div class="flex flex-col flex-1 p-5 md:p-6">
            @if (!$image && $category)
                <div class="mb-2">
                    <x-category-label :category="$category" :url="$categoryUrl" />
                </div>
            @endif

            <h1 class="font-black text-2xl sm:text-3xl lg:text-4xl text-[#111111] leading-tight tracking-tight mb-3 font-headline group-hover:text-[#E50914] transition-colors">
                <a href="{{ $url }}">{{ $title }}</a>
            </h1>

            @if ($excerpt)
                <p class="text-sm md:text-base text-[#5F6368] line-clamp-3 mb-4 leading-relaxed font-sans">
                    {{ $excerpt }}
                </p>
            @endif

            <div class="mt-auto pt-4 border-t border-[#F2F2F2]">
                <x-news-meta :author="$author" :authorAvatar="$authorAvatar" :date="$date" :readingTime="$readingTime" size="sm" />
            </div>
        </div>
    </article>

@elseif ($variant === 'horizontal')
    <article class="group flex gap-4 items-start py-3.5 border-b border-[#E8E8E8] last:border-b-0">
        @if ($image)
            <a href="{{ $url }}" class="tn-card-img-wrapper block w-28 h-20 sm:w-36 sm:h-24 shrink-0 rounded-[4px] overflow-hidden bg-[#F3F4F6]" aria-label="{{ $title }}">
                <img src="{{ $image }}" alt="{{ $title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            </a>
        @endif

        <div class="flex flex-col flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                @if ($isBreaking)
                    <x-badge variant="breaking" size="xs">BREAKING</x-badge>
                @elseif ($category)
                    <x-category-label :category="$category" :url="$categoryUrl" />
                @endif
                @if ($isSponsored)
                    <x-badge variant="sponsored" size="xs">Sponsored</x-badge>
                @endif
            </div>

            <h3 class="font-bold text-sm sm:text-base text-[#111111] leading-snug line-clamp-2 group-hover:text-[#E50914] transition-colors font-headline">
                <a href="{{ $url }}">{{ $title }}</a>
            </h3>

            <div class="mt-2">
                <x-news-meta :author="$author" :date="$date" size="xs" />
            </div>
        </div>
    </article>

@elseif ($variant === 'compact')
    <article class="group py-2.5 border-b border-[#F2F2F2] last:border-b-0">
        @if ($category)
            <div class="mb-1">
                <x-category-label :category="$category" :url="$categoryUrl" />
            </div>
        @endif
        <h3 class="font-bold text-sm text-[#111111] leading-snug line-clamp-2 group-hover:text-[#E50914] transition-colors font-headline">
            <a href="{{ $url }}">{{ $title }}</a>
        </h3>
        @if ($date)
            <div class="mt-1.5">
                <x-news-meta :date="$date" size="xs" />
            </div>
        @endif
    </article>

@elseif ($variant === 'opinion')
    <article class="group p-5 bg-[#F7F7F7] rounded-[6px] border border-[#E8E8E8] hover:border-[#111111] transition-colors flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-3 mb-3">
                @if ($authorAvatar)
                    <img src="{{ $authorAvatar }}" alt="{{ $author }}" class="w-10 h-10 rounded-full object-cover border border-[#E8E8E8]">
                @else
                    <div class="w-10 h-10 rounded-full bg-[#111111] text-white flex items-center justify-center font-bold text-sm">
                        {{ substr($author ?? 'O', 0, 1) }}
                    </div>
                @endif
                <div>
                    <h4 class="text-xs font-bold text-[#111111] uppercase tracking-wide">{{ $author }}</h4>
                    <p class="text-[11px] text-[#5F6368]">Kolumnis Opini</p>
                </div>
            </div>

            <h3 class="font-extrabold text-base text-[#111111] leading-snug line-clamp-3 group-hover:text-[#E50914] transition-colors font-headline">
                <a href="{{ $url }}">"{{ $title }}"</a>
            </h3>
        </div>

        @if ($date)
            <div class="mt-4 pt-3 border-t border-[#E8E8E8]">
                <x-news-meta :date="$date" size="xs" />
            </div>
        @endif
    </article>

@else
    {{-- Standard Grid Card --}}
    <article class="group flex flex-col bg-white rounded-[6px] overflow-hidden border border-[#E8E8E8] hover:border-[#CCCCCC] hover:shadow-subtle transition-all duration-200">
        @if ($image)
            <a href="{{ $url }}" class="tn-card-img-wrapper block {{ $imageAspect }} w-full bg-[#F3F4F6] overflow-hidden" aria-label="{{ $title }}">
                <img src="{{ $image }}" alt="{{ $title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                @if ($category)
                    <div class="absolute top-2.5 left-2.5">
                        <x-badge variant="dark" size="xs">{{ $category }}</x-badge>
                    </div>
                @endif
            </a>
        @endif

        <div class="flex flex-col flex-1 p-4">
            @if (!$image && $category)
                <div class="mb-1.5">
                    <x-category-label :category="$category" :url="$categoryUrl" />
                </div>
            @endif

            <h3 class="font-bold text-base text-[#111111] leading-snug line-clamp-2 mb-2 font-headline group-hover:text-[#E50914] transition-colors">
                <a href="{{ $url }}">{{ $title }}</a>
            </h3>

            @if ($excerpt)
                <p class="text-xs text-[#5F6368] line-clamp-2 mb-3 leading-relaxed">
                    {{ $excerpt }}
                </p>
            @endif

            <div class="mt-auto pt-3 border-t border-[#F2F2F2]">
                <x-news-meta :author="$author" :date="$date" :readingTime="$readingTime" size="xs" />
            </div>
        </div>
    </article>
@endif
