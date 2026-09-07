@extends('layouts.admin')

@section('title', 'Daftar Materi Iklan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Materi Iklan & Banner Promosi
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Kelola materi kreatif banner dan teks promosi sponsor yang disiarkan di slot iklan TopNews.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.advertising.ads.create') }}" class="px-3.5 py-2 bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs rounded-[4px] transition-colors shadow-xs">
                + Buat Materi Iklan
            </a>
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
        <a href="{{ route('admin.advertising.ads.index') }}" class="px-3 py-1.5 bg-[#111111] text-white rounded-[4px]">
            Materi Iklan
        </a>
        <a href="{{ route('admin.advertising.slots.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Slot Penempatan
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-xs">
        <form action="{{ route('admin.advertising.ads.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 text-xs">
            <select name="slot_id" class="bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-1.5 focus:border-[#E50914] focus:outline-none">
                <option value="">Semua Slot Penempatan</option>
                @foreach ($slots as $sl)
                    <option value="{{ $sl->id }}" {{ request('slot_id') == $sl->id ? 'selected' : '' }}>
                        {{ $sl->name }} ({{ $sl->key }})
                    </option>
                @endforeach
            </select>

            <select name="campaign_id" class="bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-1.5 focus:border-[#E50914] focus:outline-none">
                <option value="">Semua Kampanye</option>
                @foreach ($campaigns as $cp)
                    <option value="{{ $cp->id }}" {{ request('campaign_id') == $cp->id ? 'selected' : '' }}>
                        {{ $cp->name }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-1.5 focus:border-[#E50914] focus:outline-none">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="px-4 py-1.5 bg-[#111111] hover:bg-black text-white font-bold rounded-[4px] transition-colors cursor-pointer">
                Filter
            </button>
            @if (request()->hasAny(['slot_id', 'campaign_id', 'status']))
                <a href="{{ route('admin.advertising.ads.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:text-[#111111] self-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Ads Table -->
    <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
        @if ($ads->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F8F9FA] border-b border-[#E8E8E8] text-[#5F6368] font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Materi Iklan</th>
                            <th class="py-3 px-4">Slot Penempatan</th>
                            <th class="py-3 px-4">Kampanye</th>
                            <th class="py-3 px-4 text-center">Prioritas</th>
                            <th class="py-3 px-4 text-center">Impresi</th>
                            <th class="py-3 px-4 text-center">Klik</th>
                            <th class="py-3 px-4 text-center">CTR</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E8E8]">
                        @foreach ($ads as $ad)
                            <tr class="hover:bg-[#FDFDFD] transition-colors {{ ! $ad->is_active ? 'opacity-60 bg-neutral-50' : '' }}">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        @if ($ad->media)
                                            <img src="{{ $ad->media->getUrl() }}" alt="{{ $ad->name }}" class="w-12 h-10 object-cover rounded border border-[#E8E8E8] shrink-0" />
                                        @else
                                            <div class="w-12 h-10 bg-neutral-100 border border-[#E8E8E8] rounded flex items-center justify-center text-[10px] text-neutral-400 font-bold shrink-0">
                                                TEXT
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <span class="font-bold text-sm text-[#111111] block truncate max-w-xs">{{ $ad->name }}</span>
                                            <a href="{{ $ad->destination_url }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-blue-600 hover:underline block truncate max-w-xs">
                                                Tujuan: {{ $ad->destination_url }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-semibold text-[#111111]">
                                    {{ $ad->slot?->name }}
                                </td>
                                <td class="py-3 px-4 text-neutral-600">
                                    {{ $ad->campaign?->name }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-[#111111]">
                                    {{ $ad->priority }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-blue-600">
                                    {{ number_format($ad->impressions_count) }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-purple-600">
                                    {{ number_format($ad->clicks_count) }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-emerald-700">
                                    {{ $ad->ctr }}%
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $ad->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-200 text-neutral-700' }}">
                                        {{ $ad->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.advertising.ads.edit', $ad) }}" class="px-2.5 py-1 bg-white border border-[#CCCCCC] hover:border-[#111111] text-[#111111] font-semibold text-[11px] rounded transition-colors">
                                            Ubah
                                        </a>
                                        <form action="{{ route('admin.advertising.ads.toggle-active', $ad) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-white border border-[#CCCCCC] text-[#5F6368] hover:text-[#111111] font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                                {{ $ad->is_active ? 'Matikan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.advertising.ads.destroy', $ad) }}" method="POST" class="inline" onsubmit="return confirm('Arsipkan materi iklan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                                Arsip
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[#E8E8E8]">
                {{ $ads->links() }}
            </div>
        @else
            <div class="p-12 text-center text-xs text-[#5F6368]">
                <p class="font-bold text-sm text-[#111111] mb-1">Belum ada materi iklan</p>
                <p class="mb-4">Buat materi iklan pertama dan pilih slot penempatan serta banner gambar.</p>
                <a href="{{ route('admin.advertising.ads.create') }}" class="px-3.5 py-2 bg-[#E50914] text-white font-bold text-xs rounded-[4px]">
                    + Buat Materi Iklan
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
