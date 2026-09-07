@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">Standar Pengaturan Redaksi</h2>
        <p class="text-xs text-gray-500 mt-0.5">Konfigurasi perilaku default artikel, kecepatan baca rata-rata, dan paginasi konten.</p>
    </div>

    <form action="{{ route('admin.settings.editorial.update') }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <label class="flex items-center gap-3 cursor-pointer">
                <input 
                    type="checkbox" 
                    name="default_allow_comments" 
                    value="1" 
                    {{ old('default_allow_comments', $values['default_allow_comments'] ?? true) ? 'checked' : '' }}
                    class="rounded text-[#E50914] focus:ring-[#E50914] w-4 h-4"
                >
                <div>
                    <span class="text-sm font-bold text-gray-900">Izinkan Komentar Pembaca Secara Default</span>
                    <p class="text-xs text-gray-500 mt-0.5">Saat draf artikel baru dibuat, kolom komentar pembaca akan langsung aktif secara otomatis.</p>
                </div>
            </label>
            @error('default_allow_comments') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="default_reading_words_per_minute" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Estimasi Kecepatan Baca (Kata/Menit) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="default_reading_words_per_minute" 
                    name="default_reading_words_per_minute" 
                    value="{{ old('default_reading_words_per_minute', $values['default_reading_words_per_minute'] ?? 200) }}" 
                    required 
                    min="100" 
                    max="500" 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                >
                <p class="text-[11px] text-gray-400 mt-1">Standar umum pembaca Indonesia: 180–220 kata per menit.</p>
                @error('default_reading_words_per_minute') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="default_articles_per_page" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Artikel Per Halaman Kategori/Tag <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="default_articles_per_page" 
                    name="default_articles_per_page" 
                    value="{{ old('default_articles_per_page', $values['default_articles_per_page'] ?? 12) }}" 
                    required 
                    min="6" 
                    max="50" 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                >
                <p class="text-[11px] text-gray-400 mt-1">Jumlah item artikel sebelum navigasi halaman (pagination).</p>
                @error('default_articles_per_page') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
