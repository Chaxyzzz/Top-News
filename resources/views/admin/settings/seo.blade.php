@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">SEO Default & Metadata Situs</h2>
        <p class="text-xs text-gray-500 mt-0.5">Pengaturan title tag dasar, meta description default, gambar preview media sosial (Open Graph), dan entitas organisasi.</p>
    </div>

    <form action="{{ route('admin.settings.seo.update') }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div>
            <label for="default_meta_title" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Default Meta Title <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="default_meta_title" 
                name="default_meta_title" 
                value="{{ old('default_meta_title', $values['default_meta_title'] ?? 'TopNews — Berita Terkini, Akurat & Terpercaya') }}" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('default_meta_title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="default_meta_description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Default Meta Description <span class="text-red-500">*</span>
            </label>
            <textarea 
                id="default_meta_description" 
                name="default_meta_description" 
                rows="3" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >{{ old('default_meta_description', $values['default_meta_description'] ?? '') }}</textarea>
            @error('default_meta_description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Default Social Share Image -->
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-3">
            <div>
                <label for="default_social_image_id" class="block text-xs font-bold uppercase tracking-wider text-gray-900 mb-0.5">
                    Default Gambar Pratinjau Sosial (Open Graph Media ID)
                </label>
                <p class="text-xs text-gray-500">Gambar yang muncul saat tautan beranda dibagikan ke WhatsApp, Twitter/X, atau Facebook (ukuran ideal: 1200x630px).</p>
            </div>

            @if($socialImage)
                <div class="flex items-center gap-4 p-3 bg-white border border-gray-200 rounded-md">
                    <img src="{{ $socialImage->url }}" alt="OG Preview" class="h-16 w-28 object-cover rounded">
                    <div class="text-xs text-gray-600">
                        <p class="font-bold text-gray-800">{{ $socialImage->original_filename }}</p>
                        <p class="text-[11px] text-gray-400">{{ $socialImage->width }}x{{ $socialImage->height }}px</p>
                    </div>
                </div>
            @endif

            <input 
                type="number" 
                id="default_social_image_id" 
                name="default_social_image_id" 
                value="{{ old('default_social_image_id', $values['default_social_image_id'] ?? '') }}" 
                placeholder="ID Media gambar pratinjau (opsional)" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('default_social_image_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="organization_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Nama Badan Hukum / Organisasi <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="organization_name" 
                name="organization_name" 
                value="{{ old('organization_name', $values['organization_name'] ?? 'PT TopNews Media Nusantara') }}" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('organization_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
