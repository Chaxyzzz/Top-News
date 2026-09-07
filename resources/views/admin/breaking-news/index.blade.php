@extends('layouts.admin')

@section('title', 'Manajemen Breaking News')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Breaking News (Warta Kilat)
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Kelola informasi darurat dan warta kilat yang ditayangkan pada bilah atas portal berita TopNews.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.breaking-news.create') }}" class="px-3.5 py-2 bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs rounded-[4px] transition-colors shadow-xs">
                + Tambah Breaking News
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-xs flex items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs font-semibold">
            <a href="{{ route('admin.breaking-news.index') }}" class="px-3 py-1.5 rounded-[4px] {{ !request()->filled('status') ? 'bg-[#111111] text-white' : 'text-[#5F6368] hover:bg-[#F3F4F6]' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.breaking-news.index', ['status' => 'active']) }}" class="px-3 py-1.5 rounded-[4px] {{ request('status') === 'active' ? 'bg-emerald-600 text-white' : 'text-emerald-800 bg-emerald-50 hover:bg-emerald-100' }}">
                Aktif
            </a>
            <a href="{{ route('admin.breaking-news.index', ['status' => 'inactive']) }}" class="px-3 py-1.5 rounded-[4px] {{ request('status') === 'inactive' ? 'bg-neutral-800 text-white' : 'text-neutral-700 bg-neutral-100 hover:bg-neutral-200' }}">
                Nonaktif
            </a>
        </div>
    </div>

    <!-- Breaking News Table -->
    <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
        @if ($breakingNews->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F8F9FA] border-b border-[#E8E8E8] text-[#5F6368] font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 w-16 text-center">Prioritas</th>
                            <th class="py-3 px-4">Headline Warta</th>
                            <th class="py-3 px-4">Tautan Tujuan</th>
                            <th class="py-3 px-4">Masa Tayang (WIB)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E8E8]">
                        @foreach ($breakingNews as $item)
                            <tr class="hover:bg-[#FDFDFD] transition-colors {{ ! $item->is_active ? 'opacity-60 bg-neutral-50' : '' }}">
                                <td class="py-3 px-4 text-center font-headline font-black text-sm text-[#111111]">
                                    {{ $item->priority }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-sm text-[#111111] block">
                                        {{ $item->headline }}
                                    </span>
                                    <span class="text-[11px] text-[#80868B] mt-0.5 block">
                                        Oleh: {{ $item->author?->name ?? 'Redaksi' }} • Dibuat {{ $item->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($item->article)
                                        <a href="{{ route('news.show', $item->article->slug) }}" target="_blank" class="font-semibold text-blue-600 hover:underline block truncate max-w-xs">
                                            Artikel: {{ $item->article->title }}
                                        </a>
                                        @if(! $item->article->isPublished())
                                            <span class="text-[10px] text-amber-700 bg-amber-100 px-1 rounded font-bold">Artikel Belum Terbit</span>
                                        @endif
                                    @elseif ($item->external_url)
                                        <a href="{{ $item->external_url }}" target="_blank" rel="noopener noreferrer" class="text-neutral-600 hover:underline flex items-center gap-1 truncate max-w-xs">
                                            <span>🔗 Eksternal: {{ $item->external_url }}</span>
                                        </a>
                                    @else
                                        <span class="italic text-neutral-400">Hanya teks warta</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-[11px] text-neutral-600">
                                    @if ($item->starts_at || $item->ends_at)
                                        <div>Mulai: {{ $item->starts_at ? $item->starts_at->format('d/m/Y H:i') : 'Langsung' }}</div>
                                        <div>Berakhir: {{ $item->ends_at ? $item->ends_at->format('d/m/Y H:i') : 'Tanpa batas' }}</div>
                                        @if ($item->ends_at && $item->ends_at->isPast())
                                            <span class="text-rose-600 font-bold mt-0.5 block">Sudah Berakhir</span>
                                        @endif
                                    @else
                                        <span class="text-emerald-700 font-medium">Tayang Tetap</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $item->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-200 text-neutral-700' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.breaking-news.edit', $item) }}" class="px-2.5 py-1 bg-white border border-[#CCCCCC] hover:border-[#111111] text-[#111111] font-semibold text-[11px] rounded transition-colors">
                                            Ubah
                                        </a>
                                        <form action="{{ route('admin.breaking-news.toggle-active', $item) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-white border border-[#CCCCCC] text-[#5F6368] hover:text-[#111111] font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                                {{ $item->is_active ? 'Matikan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.breaking-news.destroy', $item) }}" method="POST" class="inline" data-confirm-delete data-delete-title="Hapus Breaking News Permanen?" data-delete-name="{{ $item->headline }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                                Hapus Permanen
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
                {{ $breakingNews->links() }}
            </div>
        @else
            <div class="p-12 text-center text-xs text-[#5F6368]">
                <p class="font-bold text-sm text-[#111111] mb-1">Belum ada warta kilat (Breaking News)</p>
                <p class="mb-4">Tambahkan informasi breaking news penting untuk disiarkan di bilah atas beranda.</p>
                <a href="{{ route('admin.breaking-news.create') }}" class="px-3.5 py-2 bg-[#E50914] text-white font-bold text-xs rounded-[4px]">
                    + Buat Breaking News
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
