@extends('layouts.admin')

@section('title', 'Slot Penempatan Iklan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Slot Penempatan Iklan (Ad Slots)
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Lokasi penayangan banner promosi yang terdaftar dan aman di portal TopNews.
            </p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-[#E8E8E8] pb-2 text-xs font-bold">
        <a href="{{ route('admin.advertising.overview') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Ringkasan
        </a>
        <a href="{{ route('admin.advertising.campaigns.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Kampanye
        </a>
        <a href="{{ route('admin.advertising.ads.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Materi Iklan
        </a>
        <a href="{{ route('admin.advertising.slots.index') }}" class="px-3 py-1.5 bg-[#111111] text-white rounded-[4px]">
            Slot Penempatan
        </a>
    </div>

    <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F8F9FA] border-b border-[#E8E8E8] text-[#5F6368] font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4">Nama Slot & Key</th>
                        <th class="py-3 px-4">Penempatan (Placement)</th>
                        <th class="py-3 px-4">Cakupan Perangkat</th>
                        <th class="py-3 px-4">Dimensi Standar</th>
                        <th class="py-3 px-4 text-center">Jumlah Iklan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8E8E8]">
                    @foreach ($slots as $slot)
                        <tr class="hover:bg-[#FDFDFD] transition-colors {{ ! $slot->is_active ? 'opacity-60 bg-neutral-50' : '' }}">
                            <td class="py-3 px-4">
                                <span class="font-bold text-sm text-[#111111] block">{{ $slot->name }}</span>
                                <code class="text-[11px] bg-neutral-100 px-1 py-0.5 rounded text-neutral-600 font-mono">
                                    {{ $slot->key }}
                                </code>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 bg-neutral-100 text-neutral-800 font-semibold rounded text-[11px] uppercase">
                                    {{ $slot->placement }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-neutral-600">
                                {{ $slot->device_scope->label() }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-[#111111]">
                                {{ $slot->width }} x {{ $slot->height }} px
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-[#111111]">
                                {{ $slot->advertisements_count }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $slot->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-200 text-neutral-700' }}">
                                    {{ $slot->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <form action="{{ route('admin.advertising.slots.toggle-active', $slot) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-white border border-[#CCCCCC] text-[#5F6368] hover:text-[#111111] font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                        {{ $slot->is_active ? 'Matikan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
