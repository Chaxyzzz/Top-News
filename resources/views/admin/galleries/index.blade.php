@extends('layouts.admin')

@section('title', 'Galeri Foto — Photo Galleries')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Galeri Foto</h1>
            <p class="text-sm text-neutral-500 mt-1">Kelola rangkaian galeri foto jurnalistik dan liputan foto cerita.</p>
        </div>
        @can('create', App\Models\Gallery::class)
            <div>
                <a href="{{ route('admin.galleries.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-sm shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Galeri Baru</span>
                </a>
            </div>
        @endcan
    </div>

    {{-- Filter / Search --}}
    <div class="bg-white p-4 rounded-lg border border-neutral-200 shadow-xs">
        <form method="GET" action="{{ route('admin.galleries.index') }}" class="flex items-center gap-3">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari galeri berdasarkan judul, fotografer, deskripsi..."
                class="flex-1 text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
            >
            <button type="submit" class="bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-semibold py-2 px-4 rounded-sm transition-colors">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.galleries.index') }}" class="bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold py-2 px-3 rounded-sm transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Galleries Table --}}
    @if($galleries->count() > 0)
        <div class="bg-white rounded-lg border border-neutral-200 shadow-xs overflow-hidden">
            <table class="min-w-full divide-y divide-neutral-200 text-xs">
                <thead class="bg-neutral-50 font-bold text-neutral-700 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3 text-left">Sampul</th>
                        <th class="px-4 py-3 text-left">Judul Galeri</th>
                        <th class="px-4 py-3 text-center">Jumlah Foto</th>
                        <th class="px-4 py-3 text-left">Fotografer</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($galleries as $gallery)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-4 py-3 w-16">
                                <div class="w-14 h-10 rounded-sm bg-neutral-900 overflow-hidden border border-neutral-200">
                                    @if($gallery->cover_image_url)
                                        <img src="{{ $gallery->cover_image_url }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-neutral-500 text-[10px]">Foto</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.galleries.edit', $gallery) }}" class="font-bold text-neutral-900 hover:text-red-600 block text-sm">
                                    {{ $gallery->title }}
                                </a>
                                <span class="text-neutral-400 text-[11px]">Dibuat: {{ $gallery->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-neutral-700">
                                {{ $gallery->media_count }} Foto
                            </td>
                            <td class="px-4 py-3 text-neutral-600">
                                {{ $gallery->photographer_display }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $gallery->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-100 text-neutral-600' }}">
                                    {{ $gallery->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.galleries.edit', $gallery) }}" class="text-red-600 hover:text-red-800 font-semibold">
                                    Kelola Foto
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $galleries->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-lg border border-neutral-200 shadow-xs">
            <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
            <h3 class="mt-3 text-sm font-semibold text-neutral-900">Belum Ada Galeri Foto</h3>
            <p class="mt-1 text-xs text-neutral-500">Buat galeri foto untuk dihubungkan dengan artikel Foto Cerita atau dokumentasi visual.</p>
            @can('create', App\Models\Gallery::class)
                <div class="mt-4">
                    <a href="{{ route('admin.galleries.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-sm transition-colors">
                        Buat Galeri Pertama
                    </a>
                </div>
            @endcan
        </div>
    @endif
</div>
@endsection
