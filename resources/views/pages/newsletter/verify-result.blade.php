@extends('layouts.app')

@section('title', 'Verifikasi Buletin — TopNews')

@section('content')
<div class="tn-container py-16">
    <div class="max-w-md mx-auto bg-white border border-[#E8E8E8] rounded-[8px] p-8 text-center shadow-subtle space-y-5">
        @if($result['status'] === 'verified')
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="font-headline font-black text-2xl text-[#111111]">Verifikasi Berhasil!</h1>
            <p class="text-sm text-[#5F6368] leading-relaxed">
                {{ $result['message'] }}
            </p>
        @else
            <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h1 class="font-headline font-black text-2xl text-[#111111]">Tautan Tidak Valid</h1>
            <p class="text-sm text-[#5F6368] leading-relaxed">
                {{ $result['message'] }}
            </p>
        @endif

        <div class="pt-4">
            <a href="{{ route('home') }}" class="inline-block bg-[#111111] hover:bg-[#222222] text-white font-bold text-xs px-6 py-2.5 rounded-[4px] transition-colors">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
