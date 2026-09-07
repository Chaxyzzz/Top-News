@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">Pengaturan Umum & Identitas Media</h2>
        <p class="text-xs text-gray-500 mt-0.5">Nama portal berita, tagline resmi, deskripsi, dan preferensi zona waktu.</p>
    </div>

    <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div>
            <label for="site_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Nama Portal Media <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="site_name" 
                name="site_name" 
                value="{{ old('site_name', $values['site_name'] ?? 'TopNews') }}" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('site_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="tagline" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Tagline / Slogan Redaksi <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="tagline" 
                name="tagline" 
                value="{{ old('tagline', $values['tagline'] ?? '') }}" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('tagline') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="site_description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Deskripsi Singkat Portal <span class="text-red-500">*</span>
            </label>
            <textarea 
                id="site_description" 
                name="site_description" 
                rows="3" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >{{ old('site_description', $values['site_description'] ?? '') }}</textarea>
            @error('site_description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="default_locale" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Bahasa Utama (Locale) <span class="text-red-500">*</span>
                </label>
                <select id="default_locale" name="default_locale" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                    <option value="id" {{ old('default_locale', $values['default_locale'] ?? 'id') === 'id' ? 'selected' : '' }}>Bahasa Indonesia (id)</option>
                    <option value="en" {{ old('default_locale', $values['default_locale'] ?? '') === 'en' ? 'selected' : '' }}>English (en)</option>
                </select>
                @error('default_locale') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="timezone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Zona Waktu Keredaksian <span class="text-red-500">*</span>
                </label>
                <select id="timezone" name="timezone" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                    <option value="Asia/Jakarta" {{ old('timezone', $values['timezone'] ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' }}>WIB (Asia/Jakarta)</option>
                    <option value="Asia/Makassar" {{ old('timezone', $values['timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' }}>WITA (Asia/Makassar)</option>
                    <option value="Asia/Jayapura" {{ old('timezone', $values['timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' }}>WIT (Asia/Jayapura)</option>
                    <option value="UTC" {{ old('timezone', $values['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                </select>
                @error('timezone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
