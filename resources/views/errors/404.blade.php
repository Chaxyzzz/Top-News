@extends('layouts.app')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
<div class="tn-container py-16 md:py-24 text-center">
    <div class="max-w-lg mx-auto space-y-6">
        <span class="font-headline font-black text-7xl md:text-9xl text-[#E8E8E8] tracking-tighter select-none block">
            404
        </span>

        <div class="space-y-2">
            <h1 class="font-headline font-black text-2xl md:text-3xl text-[#111111] tracking-tight">
                Halaman Tidak Ditemukan
            </h1>
            <p class="text-sm text-[#5F6368] leading-relaxed">
                Maaf, artikel atau halaman yang Anda tuju telah dipindahkan, dihapus, atau tautan yang Anda masukkan kurang tepat.
            </p>
        </div>

        <!-- Search option -->
        <form action="{{ route('search') }}" method="GET" class="flex gap-2 max-w-md mx-auto pt-2">
            <input 
                type="search" 
                name="q" 
                placeholder="Cari berita lain di TopNews..." 
                class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                required
            />
            <x-button type="submit" variant="primary" size="md">
                Cari
            </x-button>
        </form>

        <div class="pt-4">
            <x-button variant="outline" size="md" :href="route('home')">
                Kembali ke Beranda
            </x-button>
        </div>
    </div>
</div>
@endsection
