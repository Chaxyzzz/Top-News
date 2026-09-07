@props(['article'])

@php
    $photoCount = $article->photoStory?->gallery?->media?->count() ?? 0;
@endphp

<article {{ $attributes->merge(['class' => 'group flex flex-col bg-white rounded-lg border border-neutral-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow']) }}>
    <a href="{{ route('news.show', $article->slug) }}" class="relative aspect-4/3 bg-neutral-900 overflow-hidden block">
        @if($article->featured_image_url)
            <img
                src="{{ $article->featured_image_url }}"
                alt="{{ $article->featured_image_alt ?: $article->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center bg-neutral-900 text-neutral-600">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
            </div>
        @endif

        {{-- Photo Count Pill --}}
        <div class="absolute bottom-2 right-2 px-2.5 py-1 rounded-sm bg-black/80 backdrop-blur-xs text-white text-xs font-semibold flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
            <span>{{ $photoCount > 0 ? $photoCount . ' Foto' : 'Galeri Foto' }}</span>
        </div>

        {{-- Photo Story Badge --}}
        <div class="absolute top-2 left-2 px-2 py-0.5 rounded-sm bg-neutral-900 text-white text-[11px] font-bold uppercase tracking-wider">
            FOTO CERITA
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

        @if($article->photoStory?->gallery?->photographer_display)
            <div class="mt-3 pt-3 border-t border-neutral-100 flex items-center justify-between text-[11px] text-neutral-500">
                <span>Foto: <strong class="text-neutral-700">{{ $article->photoStory->gallery->photographer_display }}</strong></span>
            </div>
        @endif
    </div>
</article>
