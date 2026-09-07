@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">Pengaturan Teks Footer</h2>
        <p class="text-xs text-gray-500 mt-0.5">Teks pengantar tentang media dan pernyataan hak cipta pada bagian bawah seluruh halaman publik.</p>
    </div>

    <form action="{{ route('admin.settings.footer.update') }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div>
            <label for="about_text" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Deskripsi Singkat Redaksi (Footer About) <span class="text-red-500">*</span>
            </label>
            <textarea 
                id="about_text" 
                name="about_text" 
                rows="4" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >{{ old('about_text', $values['about_text'] ?? '') }}</textarea>
            @error('about_text') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="copyright_text" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Pernyataan Hak Cipta & Kepatuhan Pers
            </label>
            <input 
                type="text" 
                id="copyright_text" 
                name="copyright_text" 
                value="{{ old('copyright_text', $values['copyright_text'] ?? '') }}" 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                placeholder="Hak cipta dilindungi undang-undang. Redaksi mematuhi Kode Etik Jurnalistik Dewan Pers."
            >
            @error('copyright_text') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="developer_label" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Label Pengembang Situs (Footer)
                </label>
                <input 
                    type="text" 
                    id="developer_label" 
                    name="developer_label" 
                    value="{{ old('developer_label', $values['developer_label'] ?? 'Website developed by') }}" 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                    placeholder="Website developed by"
                >
                @error('developer_label') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="developer_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Nama Pembuat / Pengembang Website
                </label>
                <input 
                    type="text" 
                    id="developer_name" 
                    name="developer_name" 
                    value="{{ old('developer_name', $values['developer_name'] ?? 'Zakky Mubaraq') }}" 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                    placeholder="Zakky Mubaraq"
                >
                @error('developer_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
