@extends('layouts.admin')

@section('title', 'Kelola Galeri Foto — ' . $gallery->title)

@section('content')
<div class="space-y-6 max-w-5xl">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-neutral-500">
        <a href="{{ route('admin.galleries.index') }}" class="hover:text-red-600 transition-colors">Galeri Foto</a>
        <span>/</span>
        <span class="text-neutral-900 font-semibold truncate">{{ $gallery->title }}</span>
    </div>

    <form action="{{ route('admin.galleries.update', $gallery) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Gallery Info Card --}}
        <div class="bg-white rounded-lg border border-neutral-200 shadow-xs p-6 space-y-4">
            <h2 class="text-base font-bold text-neutral-900 border-b border-neutral-100 pb-2">Informasi Galeri Foto</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">Judul Galeri <span class="text-red-600">*</span></label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $gallery->title) }}"
                        required
                        class="w-full text-sm rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Deskripsi / Pengantar</label>
                    <textarea
                        name="description"
                        rows="2"
                        class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                    >{{ old('description', $gallery->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Fotografer Staf</label>
                    <select name="photographer_id" class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 py-2">
                        <option value="">— Pilih Pewarta Foto Internal —</option>
                        @foreach($photographers as $user)
                            <option value="{{ $user->id }}" {{ old('photographer_id', $gallery->photographer_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Nama Fotografer Eksternal / Sumber</label>
                    <input
                        type="text"
                        name="photographer_name"
                        value="{{ old('photographer_name', $gallery->photographer_name) }}"
                        class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Status Galeri</label>
                    <select name="status" class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 py-2">
                        <option value="published" {{ old('status', $gallery->status) === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                        <option value="draft" {{ old('status', $gallery->status) === 'draft' ? 'selected' : '' }}>Draf</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Photos Sequence Card --}}
        <div class="bg-white rounded-lg border border-neutral-200 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-neutral-900">Rangkaian Foto dalam Galeri</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Atur urutan foto dan sesuaikan takarir (caption) untuk setiap gambar.</p>
                </div>
                <button type="button" onclick="openGalleryMediaPicker()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-neutral-900 hover:bg-neutral-800 text-white font-semibold text-xs rounded-sm shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Foto dari Pustaka</span>
                </button>
            </div>

            <div id="gallery-photos-container" class="space-y-4">
                @forelse($gallery->media as $idx => $photo)
                    <div class="gallery-photo-row flex flex-col md:flex-row gap-4 p-4 bg-neutral-50 rounded-lg border border-neutral-200 items-start">
                        <input type="hidden" name="media_ids[]" value="{{ $photo->id }}">

                        {{-- Thumbnail & Number --}}
                        <div class="w-32 shrink-0 space-y-1.5">
                            <div class="relative aspect-4/3 rounded-sm bg-neutral-900 overflow-hidden border border-neutral-300">
                                <img src="{{ $photo->thumbnail_url }}" alt="{{ $photo->original_filename }}" class="w-full h-full object-cover">
                                <div class="absolute top-1 left-1 px-1.5 py-0.5 bg-black/80 text-white text-[10px] font-bold rounded-xs">
                                    #{{ $idx + 1 }}
                                </div>
                            </div>
                            <button type="button" onclick="removeGalleryPhoto(this)" class="w-full text-center text-red-600 hover:text-red-800 text-[11px] font-semibold">
                                Hapus dari Galeri
                            </button>
                        </div>

                        {{-- Caption & Credit Overrides --}}
                        <div class="flex-1 space-y-2 w-full">
                            <div>
                                <label class="block text-[11px] font-semibold text-neutral-600 mb-0.5">Takarir Foto (Caption Override)</label>
                                <textarea
                                    name="captions[{{ $photo->id }}]"
                                    rows="2"
                                    placeholder="{{ $photo->caption ?: 'Tulis keterangan spesifik untuk foto ini...' }}"
                                    class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-1.5"
                                >{{ old('captions.'.$photo->id, $photo->pivot->caption_override) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-neutral-600 mb-0.5">Kredit Foto (Credit Override)</label>
                                <input
                                    type="text"
                                    name="credits[{{ $photo->id }}]"
                                    value="{{ old('credits.'.$photo->id, $photo->pivot->credit_override) }}"
                                    placeholder="{{ $photo->credit ?: 'Foto: Nama Fotografer' }}"
                                    class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-1.5"
                                >
                            </div>
                        </div>
                    </div>
                @empty
                    <div id="gallery-empty-state" class="text-center py-10 border-2 border-dashed border-neutral-200 rounded-lg">
                        <p class="text-xs text-neutral-500">Belum ada foto yang ditambahkan ke galeri ini.</p>
                        <button type="button" onclick="openGalleryMediaPicker()" class="mt-2 text-xs font-bold text-red-600 hover:underline">
                            + Tambah Foto Sekarang
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('admin.galleries.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold rounded-sm transition-colors">
                Kembali
            </a>
            <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-sm shadow-xs transition-colors">
                Simpan Perubahan Galeri
            </button>
        </div>
    </form>
</div>

@include('admin.media.partials.modal-picker')

@push('scripts')
<script>
    function openGalleryMediaPicker() {
        if (window.openMediaPicker) {
            window.openMediaPicker(function(selectedMedia) {
                addPhotoToGallery(selectedMedia);
            });
        }
    }

    function addPhotoToGallery(media) {
        const emptyState = document.getElementById('gallery-empty-state');
        if (emptyState) emptyState.remove();

        const container = document.getElementById('gallery-photos-container');
        const count = container.querySelectorAll('.gallery-photo-row').length + 1;

        const row = document.createElement('div');
        row.className = 'gallery-photo-row flex flex-col md:flex-row gap-4 p-4 bg-neutral-50 rounded-lg border border-neutral-200 items-start';
        row.innerHTML = `
            <input type="hidden" name="media_ids[]" value="${media.id}">
            <div class="w-32 shrink-0 space-y-1.5">
                <div class="relative aspect-4/3 rounded-sm bg-neutral-900 overflow-hidden border border-neutral-300">
                    <img src="${media.thumbnail_url || media.url}" alt="${media.filename}" class="w-full h-full object-cover">
                    <div class="absolute top-1 left-1 px-1.5 py-0.5 bg-black/80 text-white text-[10px] font-bold rounded-xs">
                        #${count}
                    </div>
                </div>
                <button type="button" onclick="removeGalleryPhoto(this)" class="w-full text-center text-red-600 hover:text-red-800 text-[11px] font-semibold">
                    Hapus dari Galeri
                </button>
            </div>
            <div class="flex-1 space-y-2 w-full">
                <div>
                    <label class="block text-[11px] font-semibold text-neutral-600 mb-0.5">Takarir Foto (Caption Override)</label>
                    <textarea
                        name="captions[${media.id}]"
                        rows="2"
                        placeholder="${media.caption || 'Tulis keterangan spesifik untuk foto ini...'}"
                        class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-1.5"
                    >${media.caption || ''}</textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-neutral-600 mb-0.5">Kredit Foto (Credit Override)</label>
                    <input
                        type="text"
                        name="credits[${media.id}]"
                        value="${media.credit || ''}"
                        placeholder="${media.credit || 'Foto: Nama Fotografer'}"
                        class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-1.5"
                    >
                </div>
            </div>
        `;
        container.appendChild(row);
    }

    function removeGalleryPhoto(btn) {
        btn.closest('.gallery-photo-row').remove();
    }
</script>
@endpush
@endsection
