@extends('layouts.admin')

@section('title', 'Dasbor Manajemen Periklanan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Sistem Manajemen Periklanan (Ads CMS)
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Kelola slot, kampanye sponsor komersial, dan pantau rasio klik tayang (CTR) dengan pemisahan editorial yang ketat.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.advertising.campaigns.create') }}" class="px-3 py-1.5 bg-white border border-[#CCCCCC] hover:border-[#111111] text-[#111111] font-bold text-xs rounded-[4px] transition-colors">
                + Kampanye Baru
            </a>
            <a href="{{ route('admin.advertising.ads.create') }}" class="px-3 py-1.5 bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs rounded-[4px] transition-colors">
                + Materi Iklan Baru
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-xs">
            <span class="text-[11px] font-bold text-[#80868B] uppercase tracking-wider">Kampanye Aktif</span>
            <div class="text-2xl font-headline font-black text-[#111111] mt-1">
                {{ $stats['active_campaigns'] }} <span class="text-xs font-normal text-[#80868B]">/ {{ $stats['total_campaigns'] }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-xs">
            <span class="text-[11px] font-bold text-[#80868B] uppercase tracking-wider">Iklan Berjalan</span>
            <div class="text-2xl font-headline font-black text-emerald-600 mt-1">
                {{ $stats['active_ads'] }} <span class="text-xs font-normal text-[#80868B]">/ {{ $stats['total_ads'] }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-xs">
            <span class="text-[11px] font-bold text-[#80868B] uppercase tracking-wider">Total Impresi</span>
            <div class="text-2xl font-headline font-black text-blue-600 mt-1">
                {{ number_format($stats['total_impressions']) }}
            </div>
        </div>

        <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-xs">
            <span class="text-[11px] font-bold text-[#80868B] uppercase tracking-wider">Total Klik</span>
            <div class="text-2xl font-headline font-black text-purple-600 mt-1">
                {{ number_format($stats['total_clicks']) }}
            </div>
        </div>

        <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-xs">
            <span class="text-[11px] font-bold text-[#80868B] uppercase tracking-wider">Rata-Rata CTR</span>
            <div class="text-2xl font-headline font-black text-[#E50914] mt-1">
                {{ $stats['average_ctr'] }}%
            </div>
        </div>
    </div>

    <!-- Main Navigation Sub-Bar -->
    <div class="flex items-center gap-2 border-b border-[#E8E8E8] pb-2 text-xs font-bold">
        <a href="{{ route('admin.advertising.overview') }}" class="px-3 py-1.5 bg-[#111111] text-white rounded-[4px]">
            Ringkasan
        </a>
        <a href="{{ route('admin.advertising.campaigns.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Kampanye ({{ $stats['total_campaigns'] }})
        </a>
        <a href="{{ route('admin.advertising.ads.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Materi Iklan ({{ $stats['total_ads'] }})
        </a>
        <a href="{{ route('admin.advertising.slots.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Slot Penempatan ({{ $stats['total_slots'] }})
        </a>
    </div>

    <!-- Content Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Recent Ads (Left 7 Cols) --}}
        <div class="lg:col-span-7 bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
            <div class="p-4 border-b border-[#E8E8E8] bg-[#FAFAFA] flex items-center justify-between">
                <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">Materi Iklan Terbaru</span>
                <a href="{{ route('admin.advertising.ads.index') }}" class="text-xs font-bold text-[#E50914] hover:underline">Lihat Semua →</a>
            </div>

            <div class="divide-y divide-[#E8E8E8]">
                @forelse ($recentAds as $ad)
                    <div class="p-3.5 hover:bg-[#FDFDFD] flex items-center justify-between gap-3 text-xs transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            @if ($ad->media)
                                <img src="{{ $ad->media->getUrl() }}" alt="{{ $ad->name }}" class="w-12 h-10 object-cover rounded border border-[#E8E8E8] shrink-0" />
                            @else
                                <div class="w-12 h-10 bg-neutral-100 border border-[#E8E8E8] rounded flex items-center justify-center text-[10px] text-neutral-400 font-bold shrink-0">
                                    TEXT
                                </div>
                            @endif
                            <div class="min-w-0">
                                <span class="font-bold text-[#111111] block line-clamp-1">{{ $ad->name }}</span>
                                <div class="text-[11px] text-[#80868B] mt-0.5 flex items-center gap-2">
                                    <span>Slot: {{ $ad->slot?->name }}</span>
                                    <span>•</span>
                                    <span>Kampanye: {{ $ad->campaign?->name }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="font-headline font-black text-xs text-[#111111] block">
                                {{ number_format($ad->clicks_count) }} Klik / {{ number_format($ad->impressions_count) }} Imp
                            </span>
                            <span class="text-[10px] text-emerald-700 font-bold">
                                CTR: {{ $ad->ctr }}%
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-[#80868B]">
                        Belum ada materi iklan yang dibuat.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Slots & Recent Campaigns (Right 5 Cols) --}}
        <div class="lg:col-span-5 space-y-6">
            {{-- Slot Status --}}
            <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
                <div class="p-4 border-b border-[#E8E8E8] bg-[#FAFAFA] flex items-center justify-between">
                    <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">Status Slot Iklan</span>
                    <a href="{{ route('admin.advertising.slots.index') }}" class="text-xs font-bold text-[#E50914] hover:underline">Kelola Slot →</a>
                </div>
                <div class="divide-y divide-[#E8E8E8] text-xs">
                    @foreach ($slots as $slot)
                        <div class="p-3 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-[#111111] block">{{ $slot->name }}</span>
                                <span class="text-[10px] text-[#80868B]">{{ $slot->width }}x{{ $slot->height }} px • {{ $slot->placement }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $slot->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-200 text-neutral-700' }}">
                                    {{ $slot->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
