@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">Pengaturan Kontak & Alamat Kantor</h2>
        <p class="text-xs text-gray-500 mt-0.5">Informasi kontak publik yang ditampilkan pada halaman Kontak, footer, dan komunikasi pembaca.</p>
    </div>

    <form action="{{ route('admin.settings.contact.update') }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="public_email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Email Redaksi Publik <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="public_email" 
                    name="public_email" 
                    value="{{ old('public_email', $values['public_email'] ?? 'redaksi@topnews.id') }}" 
                    required 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                >
                @error('public_email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="public_phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Nomor Telepon Kantor
                </label>
                <input 
                    type="text" 
                    id="public_phone" 
                    name="public_phone" 
                    value="{{ old('public_phone', $values['public_phone'] ?? '') }}" 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                    placeholder="+62 21 555 0199"
                >
                @error('public_phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="whatsapp" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Nomor WhatsApp Hotline Redaksi
            </label>
            <input 
                type="text" 
                id="whatsapp" 
                name="whatsapp" 
                value="{{ old('whatsapp', $values['whatsapp'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="Contoh: 081234567890"
            >
            @error('whatsapp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="office_address" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Alamat Kantor Redaksi <span class="text-red-500">*</span>
            </label>
            <textarea 
                id="office_address" 
                name="office_address" 
                rows="3" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >{{ old('office_address', $values['office_address'] ?? '') }}</textarea>
            @error('office_address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="business_hours" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Jam Operasional Kantor
            </label>
            <input 
                type="text" 
                id="business_hours" 
                name="business_hours" 
                value="{{ old('business_hours', $values['business_hours'] ?? 'Senin - Jumat: 08:00 - 18:00 WIB') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('business_hours') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="google_maps_url" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Tautan Google Maps Lokasi
            </label>
            <input 
                type="url" 
                id="google_maps_url" 
                name="google_maps_url" 
                value="{{ old('google_maps_url', $values['google_maps_url'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="https://maps.google.com/..."
            >
            @error('google_maps_url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
