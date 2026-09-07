@extends('layouts.app')

@section('title', 'Akun Ditangguhkan')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#F7F7F7]">
    <div class="max-w-md w-full bg-white p-8 rounded-[8px] border border-[#E8E8E8] shadow-subtle text-center space-y-6">
        <div class="w-16 h-16 bg-[#FDE8E9] text-[#E50914] rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <div class="space-y-2">
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Akun Anda Ditangguhkan
            </h1>
            <p class="text-sm text-[#5F6368] leading-relaxed">
                Akses akun Anda saat ini sedang dinonaktifkan oleh administrator sistem TopNews. Apabila Anda memerlukan bantuan atau ingin mengajukan peninjauan, silakan hubungi tim manajemen redaksi.
            </p>
        </div>

        <div class="pt-4 border-t border-[#E8E8E8] flex flex-col sm:flex-row gap-2 justify-center">
            <x-button variant="primary" size="md" :href="route('contact')">
                Hubungi Administrator
            </x-button>
            <x-button variant="outline" size="md" :href="route('home')">
                Kembali ke Beranda
            </x-button>
        </div>
    </div>
</div>
@endsection
