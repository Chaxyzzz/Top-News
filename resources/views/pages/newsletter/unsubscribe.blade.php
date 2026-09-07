@extends('layouts.app')

@section('title', 'Berhenti Berlangganan Buletin — TopNews')

@section('content')
<div class="tn-container py-16">
    <div class="max-w-md mx-auto bg-white border border-[#E8E8E8] rounded-[8px] p-8 text-center shadow-subtle space-y-6">
        <div class="w-14 h-14 bg-neutral-100 text-[#5F6368] rounded-full flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>

        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] mb-2">Berhenti Berlangganan?</h1>
            <p class="text-sm text-[#5F6368] leading-relaxed">
                Apakah Anda yakin ingin berhenti menerima buletin harian TopNews untuk alamat:
            </p>
            <p class="text-sm font-bold text-[#111111] mt-1 bg-[#F8F9FA] py-2 px-3 rounded border border-[#E8E8E8] inline-block">
                {{ $subscriber->email }}
            </p>
        </div>

        <form action="{{ route('newsletter.unsubscribe', $subscriber->uuid) }}" method="POST" class="space-y-3">
            @csrf
            <button type="submit" class="w-full bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs py-3 px-6 rounded-[4px] transition-colors">
                Ya, Berhenti Berlangganan
            </button>
            <a href="{{ route('home') }}" class="block text-xs text-[#5F6368] hover:text-[#111111] font-semibold py-2">
                Batalkan & Tetap Berlangganan
            </a>
        </form>
    </div>
</div>
@endsection
