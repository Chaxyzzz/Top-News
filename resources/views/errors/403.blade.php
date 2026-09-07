@extends('layouts.app')

@section('title', '403 — Akses Tidak Diizinkan')

@section('content')
<div class="tn-container py-16 md:py-24 text-center">
    <div class="max-w-md mx-auto space-y-6">
        <span class="font-headline font-black text-7xl md:text-9xl text-[#E8E8E8] tracking-tighter select-none block">
            403
        </span>

        <div class="space-y-2">
            <h1 class="font-headline font-black text-2xl md:text-3xl text-[#111111] tracking-tight">
                Akses Ditolak
            </h1>
            <p class="text-sm text-[#5F6368] leading-relaxed">
                Maaf, akun Anda tidak memiliki hak akses yang diizinkan untuk membuka halaman ini. Hubungi administrator redaksi apabila Anda merasa ini adalah sebuah kekeliruan.
            </p>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row gap-2 justify-center">
            @auth
                <x-button variant="primary" size="md" :href="route('admin.dashboard')">
                    Ke Dashboard
                </x-button>
            @else
                <x-button variant="primary" size="md" :href="route('login')">
                    Masuk ke Akun
                </x-button>
            @endauth
            <x-button variant="outline" size="md" :href="route('home')">
                Kembali ke Beranda
            </x-button>
        </div>
    </div>
</div>
@endsection
