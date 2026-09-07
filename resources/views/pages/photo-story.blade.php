@extends('layouts.app')

@section('title', 'Foto Cerita & Galeri Jurnalistik — TopNews')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-6 font-sans">
        <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Beranda</a>
        <span class="text-neutral-300">/</span>
        <span class="text-neutral-900 font-semibold">Foto Cerita</span>
    </nav>

    {{-- Section Header --}}
    <div class="border-b-2 border-neutral-900 pb-4 mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-neutral-900 text-xs font-bold uppercase tracking-wider mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
                <span>Jurnalisme Visual</span>
            </div>
            <h1 class="font-serif font-black text-3xl md:text-4xl text-neutral-900 tracking-tight">
                Foto Cerita &amp; Galeri
            </h1>
            <p class="mt-2 text-sm text-neutral-600 max-w-2xl">
                Rangkaian jurnalisme foto kurasi redaksi yang menangkap momen bersejarah, kehidupan sosial, human interest, dan peristiwa penting.
            </p>
        </div>
        <div class="text-xs text-neutral-500 font-medium">
            Total {{ $articles->total() }} foto cerita diterbitkan
        </div>
    </div>

    {{-- Photo Stories Grid --}}
    @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($articles as $article)
                <x-photo-story-card :article="$article" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-neutral-50 rounded-lg border border-neutral-200">
            <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
            <h3 class="mt-4 text-base font-semibold text-neutral-900">Belum Ada Foto Cerita</h3>
            <p class="mt-1 text-sm text-neutral-500">Saat ini belum ada galeri foto cerita yang dipublikasikan.</p>
            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-sm bg-neutral-900 text-white hover:bg-neutral-800 transition-colors">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
