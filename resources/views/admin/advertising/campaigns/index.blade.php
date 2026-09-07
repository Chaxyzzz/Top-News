@extends('layouts.admin')

@section('title', 'Daftar Kampanye Iklan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Kampanye Iklan Komersial
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Kelola paket promosi mitra pengiklan, durasi tayang, dan alokasi materi sponsor.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.advertising.campaigns.create') }}" class="px-3.5 py-2 bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs rounded-[4px] transition-colors shadow-xs">
                + Buat Kampanye Baru
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-[#E8E8E8] pb-2 text-xs font-bold">
        <a href="{{ route('admin.advertising.overview') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Ringkasan
        </a>
        <a href="{{ route('admin.advertising.campaigns.index') }}" class="px-3 py-1.5 bg-[#111111] text-white rounded-[4px]">
            Kampanye
        </a>
        <a href="{{ route('admin.advertising.ads.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Materi Iklan
        </a>
        <a href="{{ route('admin.advertising.slots.index') }}" class="px-3 py-1.5 text-[#5F6368] hover:bg-[#F3F4F6] rounded-[4px]">
            Slot Penempatan
        </a>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
        @if ($campaigns->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F8F9FA] border-b border-[#E8E8E8] text-[#5F6368] font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Nama Kampanye</th>
                            <th class="py-3 px-4">Pengiklan / Brand</th>
                            <th class="py-3 px-4">Periode Tayang</th>
                            <th class="py-3 px-4 text-center">Jumlah Iklan</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E8E8]">
                        @foreach ($campaigns as $camp)
                            <tr class="hover:bg-[#FDFDFD] transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-sm text-[#111111] block">{{ $camp->name }}</span>
                                    @if ($camp->contact_name)
                                        <span class="text-[11px] text-[#80868B]">Kontak: {{ $camp->contact_name }} ({{ $camp->contact_email }})</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-semibold text-[#111111]">
                                    {{ $camp->advertiser }}
                                </td>
                                <td class="py-3 px-4 text-[11px] text-neutral-600">
                                    <div>Mulai: {{ $camp->starts_at ? $camp->starts_at->format('d/m/Y') : 'Langsung' }}</div>
                                    <div>Selesai: {{ $camp->ends_at ? $camp->ends_at->format('d/m/Y') : 'Tanpa batas' }}</div>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-[#111111]">
                                    {{ $camp->advertisements_count }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $camp->status->badgeClass() }}">
                                        {{ $camp->getEffectiveStatusLabel() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.advertising.campaigns.edit', $camp) }}" class="px-2.5 py-1 bg-white border border-[#CCCCCC] hover:border-[#111111] text-[#111111] font-semibold text-[11px] rounded transition-colors">
                                            Ubah
                                        </a>
                                        <form action="{{ route('admin.advertising.campaigns.toggle-status', $camp) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-white border border-[#CCCCCC] text-[#5F6368] hover:text-[#111111] font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                                {{ $camp->status === \App\Enums\AdCampaignStatus::Active ? 'Jeda' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.advertising.campaigns.destroy', $camp) }}" method="POST" class="inline" onsubmit="return confirm('Arsipkan kampanye ini?')">
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
                {{ $campaigns->links() }}
            </div>
        @else
            <div class="p-12 text-center text-xs text-[#5F6368]">
                <p class="font-bold text-sm text-[#111111] mb-1">Belum ada kampanye iklan</p>
                <p class="mb-4">Buat kampanye pertama untuk menampung materi promosi mitra pengiklan.</p>
                <a href="{{ route('admin.advertising.campaigns.create') }}" class="px-3.5 py-2 bg-[#E50914] text-white font-bold text-xs rounded-[4px]">
                    + Buat Kampanye Baru
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
