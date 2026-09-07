@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">Identitas Visual & Media Branding</h2>
        <p class="text-xs text-gray-500 mt-0.5">Atur logo situs, logo footer, favicon, dan nama brand. Apabila logo dikosongkan, sistem secara otomatis merender wordmark tipografi standar TopNews.</p>
    </div>

    <form action="{{ route('admin.settings.branding.update') }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div>
            <label for="brand_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Nama Brand / Identitas <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="brand_name" 
                name="brand_name" 
                value="{{ old('brand_name', $values['brand_name'] ?? 'TopNews') }}" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('brand_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Main Header Logo -->
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-3">
            <div>
                <label for="logo_media_id" class="block text-xs font-bold uppercase tracking-wider text-gray-900 mb-0.5">
                    Logo Utama Header (Media ID)
                </label>
                <p class="text-xs text-gray-500">ID Media gambar dari Media Library untuk logo utama pada header situs.</p>
            </div>

            @if($logoMedia)
                <div class="flex items-center gap-4 p-3 bg-white border border-gray-200 rounded-md">
                    <img src="{{ $logoMedia->url }}" alt="Logo Preview" class="h-10 w-auto object-contain">
                    <div class="text-xs text-gray-600">
                        <p class="font-bold text-gray-800">{{ $logoMedia->original_filename }}</p>
                        <p class="text-[11px] text-gray-400">Dimensi: {{ $logoMedia->width }}x{{ $logoMedia->height }}px</p>
                    </div>
                </div>
            @endif

            <input 
                type="number" 
                id="logo_media_id" 
                name="logo_media_id" 
                value="{{ old('logo_media_id', $values['logo_media_id'] ?? '') }}" 
                placeholder="Contoh: 12 (atau kosongkan untuk menggunakan wordmark)" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('logo_media_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Favicon Media -->
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-3">
            <div>
                <label for="favicon_media_id" class="block text-xs font-bold uppercase tracking-wider text-gray-900 mb-0.5">
                    Favicon Tab Browser (Media ID)
                </label>
                <p class="text-xs text-gray-500">ID Media ikon favicon berformat PNG, ICO, atau SVG.</p>
            </div>

            @if($faviconMedia)
                <div class="flex items-center gap-4 p-3 bg-white border border-gray-200 rounded-md">
                    <img src="{{ $faviconMedia->url }}" alt="Favicon Preview" class="h-8 w-8 object-contain">
                    <div class="text-xs text-gray-600">
                        <p class="font-bold text-gray-800">{{ $faviconMedia->original_filename }}</p>
                    </div>
                </div>
            @endif

            <input 
                type="number" 
                id="favicon_media_id" 
                name="favicon_media_id" 
                value="{{ old('favicon_media_id', $values['favicon_media_id'] ?? '') }}" 
                placeholder="ID Media favicon (kosongkan untuk default favicon.svg)" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('favicon_media_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Footer Logo Media -->
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-3">
            <div>
                <label for="footer_logo_media_id" class="block text-xs font-bold uppercase tracking-wider text-gray-900 mb-0.5">
                    Logo Footer Khusus Latar Gelap (Media ID)
                </label>
                <p class="text-xs text-gray-500">Opsional: Logo versi putih/terang untuk area footer berlatar hitam.</p>
            </div>

            @if($footerLogoMedia)
                <div class="flex items-center gap-4 p-3 bg-gray-900 border border-gray-800 rounded-md">
                    <img src="{{ $footerLogoMedia->url }}" alt="Footer Logo Preview" class="h-10 w-auto object-contain">
                    <div class="text-xs text-gray-300">
                        <p class="font-bold">{{ $footerLogoMedia->original_filename }}</p>
                    </div>
                </div>
            @endif

            <input 
                type="number" 
                id="footer_logo_media_id" 
                name="footer_logo_media_id" 
                value="{{ old('footer_logo_media_id', $values['footer_logo_media_id'] ?? '') }}" 
                placeholder="ID Media logo footer (opsional)" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('footer_logo_media_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
