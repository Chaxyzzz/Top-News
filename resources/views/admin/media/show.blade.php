@extends('layouts.admin')

@section('title', 'Detail Aset Media — ' . $media->original_filename)

@section('content')
<div class="space-y-6 max-w-5xl">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-neutral-500">
        <a href="{{ route('admin.media.index') }}" class="hover:text-red-600 transition-colors">Pustaka Media</a>
        <span>/</span>
        <span class="text-neutral-900 font-semibold truncate">{{ $media->original_filename }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Preview Column --}}
        <div class="lg:col-span-7 space-y-4">
            @if($media->width && $media->height)
                <div class="p-3 rounded-lg border flex items-center justify-between text-xs font-semibold {{ ($media->width >= 1280 && $media->height >= 720) ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900' }}">
                    <div class="flex items-center gap-2">
                        @if($media->width >= 1280 && $media->height >= 720)
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                            <span>Kualitas Gambar HD ({{ $media->width }} × {{ $media->height }} px)</span>
                        @else
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                            <span>Resolusi Sumber Rendah ({{ $media->width }} × {{ $media->height }} px) — Disarankan minimal 1280 × 720 px untuk ketajaman HD optimal</span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-neutral-900 rounded-lg p-4 flex items-center justify-center min-h-[350px] overflow-hidden border border-neutral-800">
                <img
                    src="{{ $media->large_url }}"
                    alt="{{ $media->alt_text ?: $media->original_filename }}"
                    class="max-h-[500px] w-auto max-w-full object-contain rounded-sm"
                >
            </div>

            {{-- Available Variants --}}
            @if(!empty($media->variants))
                <div class="bg-white p-4 rounded-lg border border-neutral-200 shadow-xs">
                    <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider mb-2">Varian Resolusi Otomatis (WebP)</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                        @foreach($media->variants as $variantName => $path)
                            <a href="{{ Storage::disk($media->disk)->url($path) }}" target="_blank" class="p-2 bg-neutral-50 hover:bg-red-50 hover:border-red-300 border border-neutral-200 rounded-sm block transition-colors">
                                <div class="font-bold text-neutral-900 capitalize">{{ $variantName }}</div>
                                <div class="text-[10px] text-neutral-500 truncate mt-0.5">{{ basename($path) }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Usage Information --}}
            <div class="bg-white p-4 rounded-lg border border-neutral-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider">Informasi Penggunaan</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $media->isUsed() ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-100 text-neutral-600' }}">
                        {{ $media->usage_count }} Penggunaan
                    </span>
                </div>

                @if($media->articles->isNotEmpty())
                    <div>
                        <div class="text-xs font-semibold text-neutral-700 mb-1">Artikel Terkait (Gambar Utama):</div>
                        <ul class="divide-y divide-neutral-100 text-xs">
                            @foreach($media->articles as $art)
                                <li class="py-1.5 flex items-center justify-between">
                                    <a href="{{ route('admin.articles.show', $art) }}" class="text-neutral-900 hover:text-red-600 font-medium truncate max-w-sm">
                                        {{ $art->title }}
                                    </a>
                                    <span class="text-[10px] text-neutral-400 capitalize">{{ $art->status->value }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($media->galleries->isNotEmpty())
                    <div>
                        <div class="text-xs font-semibold text-neutral-700 mb-1">Galeri Terkait:</div>
                        <ul class="divide-y divide-neutral-100 text-xs">
                            @foreach($media->galleries as $gal)
                                <li class="py-1.5 flex items-center justify-between">
                                    <a href="{{ route('admin.galleries.edit', $gal) }}" class="text-neutral-900 hover:text-red-600 font-medium truncate max-w-sm">
                                        {{ $gal->title }}
                                    </a>
                                    <span class="text-[10px] text-neutral-400 capitalize">{{ $gal->status }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!$media->isUsed())
                    <p class="text-xs text-neutral-500 italic">Media ini belum digunakan dalam artikel atau galeri manapun.</p>
                @endif
            </div>
        </div>

        {{-- Metadata & Actions Column --}}
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white p-5 rounded-lg border border-neutral-200 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-neutral-900 border-b border-neutral-100 pb-2">Metadata Aset Media</h3>

                <form action="{{ route('admin.media.update', $media) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1">Teks Alternatif (Alt Text)</label>
                        <input
                            type="text"
                            name="alt_text"
                            value="{{ old('alt_text', $media->alt_text) }}"
                            placeholder="Deskripsi visual untuk pembaca layar..."
                            class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1">Takarir Standar (Caption)</label>
                        <textarea
                            name="caption"
                            rows="3"
                            placeholder="Keterangan peristiwa foto..."
                            class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                        >{{ old('caption', $media->caption) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1">Kredit Fotografer / Sumber</label>
                        <input
                            type="text"
                            name="credit"
                            value="{{ old('credit', $media->credit) }}"
                            placeholder="contoh: Foto: Nama Fotografer / TopNews"
                            class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                        >
                    </div>

                    <button type="submit" class="w-full bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-semibold py-2 px-4 rounded-sm shadow-xs transition-colors">
                        Simpan Perubahan Metadata
                    </button>
                </form>
            </div>

            {{-- Technical Specs --}}
            <div class="bg-white p-5 rounded-lg border border-neutral-200 shadow-xs space-y-3">
                <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider border-b border-neutral-100 pb-2">Spesifikasi Berkas</h3>
                <dl class="grid grid-cols-2 gap-x-2 gap-y-2 text-xs">
                    <dt class="text-neutral-500">Nama Asli:</dt>
                    <dd class="font-medium text-neutral-900 truncate" title="{{ $media->original_filename }}">{{ $media->original_filename }}</dd>

                    <dt class="text-neutral-500">Dimensi:</dt>
                    <dd class="font-medium text-neutral-900">{{ $media->dimensions ?: '—' }}</dd>

                    <dt class="text-neutral-500">Ukuran Berkas:</dt>
                    <dd class="font-medium text-neutral-900">{{ $media->human_size }}</dd>

                    <dt class="text-neutral-500">Tipe MIME:</dt>
                    <dd class="font-medium text-neutral-900 font-mono text-[11px]">{{ $media->mime_type }}</dd>

                    <dt class="text-neutral-500">Pengunggah:</dt>
                    <dd class="font-medium text-neutral-900">{{ $media->uploader?->name ?? 'Sistem' }}</dd>

                    <dt class="text-neutral-500">Tanggal Unggah:</dt>
                    <dd class="font-medium text-neutral-900">{{ $media->created_at->format('d M Y, H:i') }} WIB</dd>
                </dl>
            </div>

            {{-- Delete Action --}}
            @can('delete', $media)
                <div class="bg-red-50 border border-red-200 p-4 rounded-lg space-y-2">
                    <h4 class="text-xs font-bold text-red-900 uppercase tracking-wider">Hapus Media</h4>
                    @if($media->isUsed())
                        <p class="text-xs text-red-700 leading-relaxed">
                            Media ini tidak dapat dihapus karena sedang digunakan dalam {{ $media->usage_count }} artikel/galeri. Lepaskan tautan penggunaan terlebih dahulu.
                        </p>
                    @else
                        <p class="text-xs text-red-700 leading-relaxed">
                            Aset media ini belum digunakan dan dapat dihapus permanen dari penyimpanan server.
                        </p>
                        <form action="{{ route('admin.media.destroy', $media) }}" method="POST" data-confirm-delete data-delete-title="Hapus Media Permanen?" data-delete-name="{{ $media->original_filename }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-2 px-3 rounded-sm shadow-xs transition-colors cursor-pointer">
                                Hapus Media Permanen
                            </button>
                        </form>
                    @endif
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection
