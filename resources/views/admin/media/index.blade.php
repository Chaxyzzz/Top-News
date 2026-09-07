@extends('layouts.admin')

@section('title', 'Pustaka Media — Media Library')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Pustaka Media</h1>
            <p class="text-sm text-neutral-500 mt-1">Kelola foto berita, visual editorial, dan aset grafis liputan redaksi.</p>
        </div>
        @can('create', App\Models\Media::class)
            <div>
                <button type="button" onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-sm shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Unggah Media</span>
                </button>
            </div>
        @endcan
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white p-4 rounded-lg border border-neutral-200 shadow-xs">
        <form method="GET" action="{{ route('admin.media.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari berdasarkan nama file, teks alternatif, takarir (caption), atau kredit..."
                    class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                >
            </div>
            <div class="sm:col-span-3">
                <select name="type" class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 py-2">
                    <option value="">Semua Jenis Media</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Gambar / Foto</option>
                    <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
                    <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Dokumen</option>
                </select>
            </div>
            <div class="sm:col-span-3 flex items-center gap-2">
                <button type="submit" class="flex-1 bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-semibold py-2 px-3 rounded-sm transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'type', 'uploader_id']))
                    <a href="{{ route('admin.media.index') }}" class="bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold py-2 px-3 rounded-sm transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Media Grid --}}
    @if($media->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($media as $item)
                <div class="group bg-white rounded-lg border border-neutral-200 overflow-hidden shadow-xs hover:border-red-500 hover:shadow-md transition-all flex flex-col justify-between">
                    <a href="{{ route('admin.media.show', $item) }}" class="block relative aspect-square bg-neutral-100 overflow-hidden">
                        <img
                            src="{{ $item->thumbnail_url }}"
                            alt="{{ $item->alt_text ?: $item->original_filename }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                            loading="lazy"
                        >
                        {{-- Dimensions / Size Badge --}}
                        <div class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded-xs bg-black/70 text-white text-[10px] font-mono">
                            {{ $item->human_size }}
                        </div>
                        @if($item->articles_count > 0 || $item->galleries_count > 0)
                            <div class="absolute top-1 left-1 px-1.5 py-0.5 rounded-xs bg-emerald-600 text-white text-[9px] font-bold uppercase tracking-wider">
                                Digunakan
                            </div>
                        @endif
                    </a>

                    <div class="p-2.5">
                        <a href="{{ route('admin.media.show', $item) }}" class="text-xs font-semibold text-neutral-900 hover:text-red-600 transition-colors line-clamp-1 block" title="{{ $item->original_filename }}">
                            {{ $item->original_filename }}
                        </a>
                        <div class="mt-1 flex items-center justify-between text-[11px] text-neutral-400">
                            <span>{{ $item->dimensions ?: strtoupper($item->extension) }}</span>
                            <span>{{ $item->created_at->format('d/m/y') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $media->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-lg border border-neutral-200 shadow-xs">
            <svg class="mx-auto h-12 w-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
            <h3 class="mt-3 text-sm font-semibold text-neutral-900">Belum Ada Aset Media</h3>
            <p class="mt-1 text-xs text-neutral-500">Mulai unggah foto liputan jurnalistik atau aset berita untuk digunakan redaksi.</p>
            @can('create', App\Models\Media::class)
                <div class="mt-4">
                    <button type="button" onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-sm transition-colors">
                        Unggah Media Pertama
                    </button>
                </div>
            @endcan
        </div>
    @endif
</div>

{{-- Upload Modal --}}
<div id="upload-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-neutral-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 space-y-4 border border-neutral-200">
        <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
            <h3 class="text-base font-bold text-neutral-900">Unggah Aset Media Baru</h3>
            <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="text-neutral-400 hover:text-neutral-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-2">Pilih Berkas Foto / Gambar</label>
                <input
                    type="file"
                    name="files[]"
                    multiple
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    required
                    class="block w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-neutral-100 file:text-neutral-700 hover:file:bg-neutral-200 cursor-pointer border border-neutral-300 rounded-sm p-1"
                >
                <p class="mt-1 text-[11px] text-neutral-500">Mendukung format JPG, PNG, WEBP, GIF. Maksimal 10 MB per berkas. Sistem akan mengoptimalkan resolusi dan varian secara otomatis.</p>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Teks Alternatif (Alt Text)</label>
                    <input type="text" name="alt_text" placeholder="Deskripsikan objek visual foto..." class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Takarir Standar (Caption)</label>
                    <textarea name="caption" rows="2" placeholder="Keterangan konteks peristiwa dalam foto..." class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Kredit Fotografer / Sumber</label>
                    <input type="text" name="credit" placeholder="contoh: Foto: Nama Fotografer / TopNews" class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
                <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold rounded-sm transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-sm shadow-xs transition-colors">
                    Unggah Berkas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
