@extends('admin.settings.layout')

@section('settings-content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">Konfigurasi Buletin Newsletter</h2>
        <p class="text-xs text-gray-500 mt-0.5">Kelola mekanisme pendaftaran buletin, double opt-in verifikasi email, nama pengirim, dan teks ajakan berlangganan (CTA).</p>
    </div>

    <form action="{{ route('admin.settings.newsletter.update') }}" method="POST" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input 
                    type="checkbox" 
                    name="enabled" 
                    value="1" 
                    {{ old('enabled', $values['enabled'] ?? true) ? 'checked' : '' }}
                    class="rounded text-[#E50914] focus:ring-[#E50914] w-4 h-4"
                >
                <div>
                    <span class="text-sm font-bold text-gray-900">Aktifkan Formulir Pendaftaran Newsletter</span>
                    <p class="text-xs text-gray-500 mt-0.5">Jika dinonaktifkan, blok formulir newsletter pada artikel dan footer tidak akan ditampilkan ke pembaca.</p>
                </div>
            </label>

            <label class="flex items-center gap-3 cursor-pointer border-t border-gray-200 pt-3">
                <input 
                    type="checkbox" 
                    name="double_opt_in" 
                    value="1" 
                    {{ old('double_opt_in', $values['double_opt_in'] ?? true) ? 'checked' : '' }}
                    class="rounded text-[#E50914] focus:ring-[#E50914] w-4 h-4"
                >
                <div>
                    <span class="text-sm font-bold text-gray-900">Gunakan Verifikasi Double Opt-In (Direkomendasikan)</span>
                    <p class="text-xs text-gray-500 mt-0.5">Pendaftar baru berstatus Menunggu Verifikasi hingga mengklik tautan konfirmasi di kotak masuk email mereka.</p>
                </div>
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="default_sender_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Nama Pengirim Email <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="default_sender_name" 
                    name="default_sender_name" 
                    value="{{ old('default_sender_name', $values['default_sender_name'] ?? 'Redaksi TopNews') }}" 
                    required 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                >
                @error('default_sender_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="default_reply_to" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                    Alamat Email Reply-To <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="default_reply_to" 
                    name="default_reply_to" 
                    value="{{ old('default_reply_to', $values['default_reply_to'] ?? 'newsletter@topnews.id') }}" 
                    required 
                    class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
                >
                @error('default_reply_to') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="cta_title" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Judul Ajakan Berlangganan (CTA Title) <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="cta_title" 
                name="cta_title" 
                value="{{ old('cta_title', $values['cta_title'] ?? 'Berita Penting, Langsung ke Inbox Anda.') }}" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >
            @error('cta_title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="cta_description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                Deskripsi Ajakan Berlangganan (CTA Description) <span class="text-red-500">*</span>
            </label>
            <textarea 
                id="cta_description" 
                name="cta_description" 
                rows="3" 
                required 
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]"
            >{{ old('cta_description', $values['cta_description'] ?? '') }}</textarea>
            @error('cta_description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
            <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-6 py-2.5 rounded-md shadow-sm transition-colors">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.newsletter.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 ml-2">
                Lihat Daftar Pelanggan &rarr;
            </a>
        </div>
    </form>
</div>
@endsection
