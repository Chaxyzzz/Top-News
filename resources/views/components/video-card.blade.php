@props(['article'])

<article {{ $attributes->merge(['class' => 'group flex flex-col bg-white rounded-lg border border-neutral-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow']) }}>
    <a href="{{ route('news.show', $article->slug) }}" class="relative aspect-video bg-neutral-900 overflow-hidden block">
        @if($article->featured_image_url)
            <img
                src="{{ $article->featured_image_url }}"
                alt="{{ $article->featured_image_alt ?: $article->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center bg-neutral-900 text-neutral-600">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
            </div>
        @endif

        {{-- Play Icon Overlay --}}
        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors flex items-center justify-center">
            <div class="w-12 h-12 rounded-full bg-red-600/90 text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </div>
        </div>

        {{-- Duration Badge --}}
        @if($article->video && $article->video->formatted_duration)
            <div class="absolute bottom-2 right-2 px-2 py-0.5 rounded-sm bg-black/80 text-white text-xs font-mono font-medium tracking-tight">
                {{ $article->video->formatted_duration }}
            </div>
        @endif

        {{-- Video Badge --}}
        <div class="absolute top-2 left-2 px-2 py-0.5 rounded-sm bg-red-600 text-white text-[11px] font-bold uppercase tracking-wider">
            VIDEO
        </div>
    </a>

    <div class="p-4 flex-1 flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2 mb-2 text-xs">
                @if($article->category)
                    <a href="{{ route('category.show', $article->category->slug) }}" class="font-bold text-red-700 hover:underline">
                        {{ $article->category->name }}
                    </a>
                    <span class="text-neutral-300">•</span>
                @endif
                <time datetime="{{ $article->published_at?->toIso8601String() }}" class="text-neutral-500">
                    {{ $article->published_at?->diffForHumans() }}
                </time>
            </div>

            <h3 class="font-serif font-bold text-base text-neutral-900 leading-snug group-hover:text-red-700 transition-colors line-clamp-2">
                <a href="{{ route('news.show', $article->slug) }}">
                    {{ $article->title }}
                </a>
            </h3>

            @if($article->subtitle || $article->excerpt)
                <p class="mt-2 text-xs text-neutral-600 line-clamp-2 leading-relaxed">
                    {{ $article->subtitle ?: $article->excerpt }}
                </p>
            @endif
        </div>
    </div>
</article>
