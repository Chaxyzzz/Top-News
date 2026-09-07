@extends('layouts.app')

@section('title', '500 — Terjadi Kesalahan Server')

@section('content')
<div class="tn-container py-16 md:py-24 text-center">
    <div class="max-w-lg mx-auto space-y-6">
        <span class="font-headline font-black text-7xl md:text-9xl text-[#E8E8E8] tracking-tighter select-none block">
            500
        </span>

        <div class="space-y-2">
            <h1 class="font-headline font-black text-2xl md:text-3xl text-[#111111] tracking-tight">
                Terjadi Kendala Sistem
            </h1>
            <p class="text-sm text-[#5F6368] leading-relaxed">
                Mohon maaf, server kami sedang mengalami kendala teknis sementara. Tim pengembang TopNews sedang menangani masalah ini.
            </p>
        </div>

        <div class="pt-4">
            <x-button variant="primary" size="md" :href="route('home')">
                Kembali ke Beranda
            </x-button>
        </div>
    </div>
</div>
@endsection
