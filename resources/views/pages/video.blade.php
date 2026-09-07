@extends('layouts.app')

@section('title', 'Video Berita Terkini — TopNews')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-6 font-sans">
        <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Beranda</a>
        <span class="text-neutral-300">/</span>
        <span class="text-neutral-900 font-semibold">Video</span>
    </nav>

    {{-- Section Header --}}
    <div class="border-b-2 border-red-600 pb-4 mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-red-600 text-xs font-bold uppercase tracking-wider mb-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                <span>Kanal Multimedia</span>
            </div>
            <h1 class="font-serif font-black text-3xl md:text-4xl text-neutral-900 tracking-tight">
                TopNews Video
            </h1>
            <p class="mt-2 text-sm text-neutral-600 max-w-2xl">
                Liputan video eksklusif, analisis mendalam, investigasi visual, dan wawancara terkini dari tim redaksi TopNews.
            </p>
        </div>
        <div class="text-xs text-neutral-500 font-medium">
            Total {{ $articles->total() }} video berita diterbitkan
        </div>
    </div>

    {{-- Video Grid --}}
    @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($articles as $article)
                <x-video-card :article="$article" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-neutral-50 rounded-lg border border-neutral-200">
            <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
            <h3 class="mt-4 text-base font-semibold text-neutral-900">Belum Ada Video Berita</h3>
            <p class="mt-1 text-sm text-neutral-500">Saat ini belum ada video berita yang dipublikasikan.</p>
            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-sm bg-neutral-900 text-white hover:bg-neutral-800 transition-colors">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
