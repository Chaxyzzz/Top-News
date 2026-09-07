@extends('layouts.app')

@section('title', '[PRATINJAU] ' . $article->title . ' — TopNews')

@push('styles')
<meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
<!-- Preview Sticky Top Banner -->
<div class="bg-[#171717] text-white px-4 py-2.5 text-xs sticky top-16 z-30 shadow-md flex items-center justify-between">
    <div class="tn-container flex items-center justify-between w-full">
        <div class="flex items-center gap-2 font-bold">
            <span class="px-2 py-0.5 bg-[#E50914] text-white rounded text-[10px] uppercase tracking-wider">
                PRATINJAU REDAKSI
            </span>
            <span>Status Saat Ini: <strong>{{ $article->status->label() }}</strong> (Naskah ini belum terbit untuk publik)</span>
        </div>

        @auth
            <a href="{{ route('admin.articles.edit', $article) }}" class="underline hover:text-[#E50914] text-xs font-semibold">
                ← Kembali ke Editor CMS
            </a>
        @endauth
    </div>
</div>

<div class="tn-container py-6 lg:py-10">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Article Header -->
        <header class="space-y-4 border-b border-[#E5E7EB] pb-6">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2 py-0.5 rounded text-xs font-bold" style="color: {{ $article->category->accent_color ?? '#E50914' }}; background-color: {{ $article->category->accent_color ?? '#E50914' }}15;">
                    {{ $article->category->name }}
                </span>
                
                @if ($article->is_breaking)
                    <span class="px-2 py-0.5 bg-[#E50914] text-white text-[10px] font-black uppercase tracking-wider rounded-[2px]">
                        BREAKING NEWS
                    </span>
                @endif

                <span class="text-xs text-[#6B7280]">
                    • {{ $article->reading_time }} menit baca
                </span>
            </div>

            <h1 class="font-headline font-black text-3xl sm:text-4xl lg:text-5xl text-[#111111] leading-[1.15] tracking-tight">
                {{ $article->title }}
            </h1>

            @if ($article->subtitle)
                <p class="text-lg sm:text-xl text-[#4B5563] font-medium leading-snug">
                    {{ $article->subtitle }}
                </p>
            @endif

            <div class="pt-4 flex items-center justify-between gap-4 border-t border-[#F3F4F6] text-xs text-[#6B7280]">
                <div>
                    Penulis: <strong class="text-[#111111]">{{ $article->author?->name ?? '-' }}</strong>
                </div>
                <div>
                    Dibuat: {{ $article->created_at->format('d/m/Y H:i') }} WIB
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        @if ($article->featured_image_url)
            <figure class="space-y-2">
                <div class="aspect-16/9 bg-[#F3F4F6] rounded-[6px] overflow-hidden">
                    <img src="{{ $article->featured_image_url }}" alt="{{ $article->featured_image_alt ?? $article->title }}" class="w-full h-full object-cover">
                </div>
                @if ($article->featured_image_caption)
                    <figcaption class="text-xs text-[#6B7280] italic px-1">
                        {{ $article->featured_image_caption }}
                    </figcaption>
                @endif
            </figure>
        @endif

        <!-- Excerpt -->
        @if ($article->excerpt)
            <div class="p-4 sm:p-5 bg-[#F7F7F8] border-l-4 border-[#E50914] rounded-r text-sm sm:text-base text-[#111111] font-semibold leading-relaxed">
                {{ $article->excerpt }}
            </div>
        @endif

        <!-- Sanitized Content -->
        <div class="prose max-w-none text-base text-[#111111] leading-relaxed space-y-4 pt-2 font-sans">
            {!! $sanitizedContent !!}
        </div>
    </div>
</div>
@endsection
