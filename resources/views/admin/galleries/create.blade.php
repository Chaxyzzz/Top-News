@extends('layouts.admin')

@section('title', 'Buat Galeri Foto Baru')

@section('content')
<div class="space-y-6 max-w-4xl">
    {{-- Header --}}
    <div class="flex items-center gap-2 text-xs text-neutral-500">
        <a href="{{ route('admin.galleries.index') }}" class="hover:text-red-600 transition-colors">Galeri Foto</a>
        <span>/</span>
        <span class="text-neutral-900 font-semibold">Buat Baru</span>
    </div>

    <div class="bg-white rounded-lg border border-neutral-200 shadow-xs p-6">
        <h2 class="text-lg font-bold text-neutral-900 mb-4 border-b border-neutral-100 pb-3">Informasi Galeri Foto</h2>

        <form action="{{ route('admin.galleries.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">Judul Galeri <span class="text-red-600">*</span></label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="contoh: Sorotan Lensa: Suasana Kemeriahan Pesta Rakyat..."
                        class="w-full text-sm rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                    >
                    @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Deskripsi / Pengantar Galeri</label>
                    <textarea
                        name="description"
                        rows="3"
                        placeholder="Uraian singkat latar belakang liputan foto..."
                        class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1">Fotografer Staf</label>
                        <select name="photographer_id" class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 py-2">
                            <option value="">— Pilih Pewarta Foto Internal —</option>
                            @foreach($photographers as $user)
                                <option value="{{ $user->id }}" {{ old('photographer_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->username }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1">Atau Nama Fotografer Eksternal / Kontributor</label>
                        <input
                            type="text"
                            name="photographer_name"
                            value="{{ old('photographer_name') }}"
                            placeholder="contoh: Antara Foto / Redaksi"
                            class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 px-3 py-2"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-1">Status Publikasi</label>
                    <select name="status" class="w-full text-xs rounded-sm border-neutral-300 focus:border-red-600 focus:ring-red-600 py-2">
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publikasikan Segera</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-neutral-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.galleries.index') }}" class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold rounded-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-sm shadow-xs transition-colors">
                    Simpan & Lanjutkan Kelola Foto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
