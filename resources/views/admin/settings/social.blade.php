@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">Tautan Jejaring Media Sosial</h2>
        <p class="text-xs text-gray-500 mt-0.5">Tautan profil resmi TopNews di berbagai platform media sosial yang muncul pada header dan footer.</p>
    </div>

    <form action="{{ route('admin.settings.social.update') }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div>
            <label for="instagram" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Instagram URL
            </label>
            <input 
                type="url" 
                id="instagram" 
                name="instagram" 
                value="{{ old('instagram', $values['instagram'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="https://instagram.com/topnews_id"
            >
            @error('instagram') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="x" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                X / Twitter URL
            </label>
            <input 
                type="url" 
                id="x" 
                name="x" 
                value="{{ old('x', $values['x'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="https://x.com/topnews_id"
            >
            @error('x') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="facebook" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Facebook Page URL
            </label>
            <input 
                type="url" 
                id="facebook" 
                name="facebook" 
                value="{{ old('facebook', $values['facebook'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="https://facebook.com/topnewsid"
            >
            @error('facebook') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="youtube" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                YouTube Channel URL
            </label>
            <input 
                type="url" 
                id="youtube" 
                name="youtube" 
                value="{{ old('youtube', $values['youtube'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="https://youtube.com/@topnews_id"
            >
            @error('youtube') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="tiktok" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                TikTok URL
            </label>
            <input 
                type="url" 
                id="tiktok" 
                name="tiktok" 
                value="{{ old('tiktok', $values['tiktok'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="https://tiktok.com/@topnews_id"
            >
            @error('tiktok') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="linkedin" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                LinkedIn Company URL
            </label>
            <input 
                type="url" 
                id="linkedin" 
                name="linkedin" 
                value="{{ old('linkedin', $values['linkedin'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="https://linkedin.com/company/topnews-id"
            >
            @error('linkedin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
