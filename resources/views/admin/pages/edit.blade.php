@extends('layouts.admin')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold font-headline text-gray-900">Edit Halaman: {{ $page->title }}</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui naskah, status publikasi, dan pengaturan SEO halaman.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.pages.preview', $page) }}" target="_blank" class="text-xs font-bold text-gray-600 hover:text-gray-900 border border-gray-300 bg-white px-3 py-2 rounded-md shadow-sm">
            Pratinjau
        </a>
        <a href="{{ route('admin.pages.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900">
            &larr; Kembali ke Daftar
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="space-y-6 max-w-3xl">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Judul Halaman <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                value="{{ old('title', $page->title) }}" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    URL Slug <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="slug" 
                    name="slug" 
                    value="{{ old('slug', $page->slug) }}" 
                    required 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                >
                @error('slug') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="page_type" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Tipe Halaman Sistem
                </label>
                <select id="page_type" name="page_type" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                    <option value="">Halaman Kustom Mandiri</option>
                    @foreach($pageTypes as $type)
                        <option value="{{ $type->value }}" {{ old('page_type', $page->page_type?->value) === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                @error('page_type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="excerpt" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Ringkasan / Kutipan Singkat (Excerpt)
            </label>
            <textarea 
                id="excerpt" 
                name="excerpt" 
                rows="2" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >{{ old('excerpt', $page->excerpt) }}</textarea>
            @error('excerpt') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="content" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Konten Halaman (HTML / Teks Kaya) <span class="text-red-500">*</span>
            </label>
            <textarea 
                id="content" 
                name="content" 
                rows="16" 
                required 
                class="w-full font-mono text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >{{ old('content', $page->content) }}</textarea>
            <p class="text-[11px] text-gray-400 mt-1">Konten disanitasi secara otomatis untuk mencegah script berbahaya.</p>
            @error('content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Status Publikasi <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                    @foreach(App\Enums\PageStatus::cases() as $st)
                        <option value="{{ $st->value }}" {{ old('status', $page->status->value) === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
                @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center pt-5">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="show_in_search" 
                        value="1" 
                        {{ old('show_in_search', $page->show_in_search) ? 'checked' : '' }}
                        class="rounded text-[#E50914] focus:ring-[#E50914] w-4 h-4"
                    >
                    <span class="text-xs font-bold text-gray-700">Izinkan Muncul dalam Hasil Pencarian</span>
                </label>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-5 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-800">Metadata SEO Khusus (Opsional)</h3>

            <div>
                <label for="seo_title" class="block text-xs font-bold text-gray-600 mb-1">SEO Title Tag</label>
                <input 
                    type="text" 
                    id="seo_title" 
                    name="seo_title" 
                    value="{{ old('seo_title', $page->seo_title) }}" 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                >
                @error('seo_title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="seo_description" class="block text-xs font-bold text-gray-600 mb-1">SEO Meta Description</label>
                <textarea 
                    id="seo_description" 
                    name="seo_description" 
                    rows="2" 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                >{{ old('seo_description', $page->seo_description) }}</textarea>
                @error('seo_description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.pages.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 ml-2">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
