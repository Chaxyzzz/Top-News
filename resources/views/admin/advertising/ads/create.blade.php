@extends('layouts.admin')

@section('title', 'Buat Materi Iklan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.advertising.ads.index') }}" class="text-xs font-semibold text-[#5F6368] hover:text-[#111111] flex items-center gap-1 mb-1">
                ← Kembali ke Daftar Iklan
            </a>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Buat Materi Iklan Baru
            </h1>
        </div>
    </div>

    <form action="{{ route('admin.advertising.ads.store') }}" method="POST" class="bg-white p-6 rounded-[6px] border border-[#E8E8E8] shadow-xs space-y-5">
        @csrf

        {{-- Campaign & Slot Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="campaign_id" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Kampanye Iklan <span class="text-red-600">*</span>
                </label>
                <select name="campaign_id" id="campaign_id" class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none" required>
                    <option value="">-- Pilih Kampanye --</option>
                    @foreach ($campaigns as $camp)
                        <option value="{{ $camp->id }}" {{ old('campaign_id') == $camp->id ? 'selected' : '' }}>
                            {{ $camp->name }} ({{ $camp->advertiser }})
                        </option>
                    @endforeach
                </select>
                @error('campaign_id') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="ad_slot_id" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Slot Penempatan <span class="text-red-600">*</span>
                </label>
                <select name="ad_slot_id" id="ad_slot_id" class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none" required>
                    <option value="">-- Pilih Slot Penempatan --</option>
                    @foreach ($slots as $sl)
                        <option value="{{ $sl->id }}" {{ old('ad_slot_id') == $sl->id ? 'selected' : '' }}>
                            {{ $sl->name }} ({{ $sl->width }}x{{ $sl->height }} px)
                        </option>
                    @endforeach
                </select>
                @error('ad_slot_id') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Ad Name & Destination URL --}}
        <div>
            <label for="name" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Nama / Label Internal Materi Iklan <span class="text-red-600">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name') }}" 
                placeholder="Contoh: Banner Promo Kredit Rumah Bunga 2.5%"
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                required
            />
            @error('name') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="destination_url" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                URL Tujuan Klik (Destination URL) <span class="text-red-600">*</span>
            </label>
            <input 
                type="url" 
                name="destination_url" 
                id="destination_url" 
                value="{{ old('destination_url') }}" 
                placeholder="https://sponsor.example.com/promo-khusus"
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                required
            />
            <p class="text-[11px] text-[#80868B] mt-0.5">Semua tautan otomatis diberi atribut rel="sponsored noopener noreferrer" dan dilacak secara aman.</p>
            @error('destination_url') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Media Selection --}}
        <div class="pt-2 border-t border-[#E8E8E8]">
            <label for="media_id" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Pilih Gambar Banner (Dari Media Library)
            </label>
            <select name="media_id" id="media_id" class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none">
                <option value="">-- Tanpa Banner Gambar (Iklan Teks) --</option>
                @foreach ($mediaItems as $med)
                    <option value="{{ $med->id }}" {{ old('media_id') == $med->id ? 'selected' : '' }}>
                        {{ $med->title ?: $med->file_name }} ({{ $med->width }}x{{ $med->height }} px)
                    </option>
                @endforeach
            </select>
            @error('media_id') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Text Ads Fields (Headline, Body, Alt Text) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="headline" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Headline Promosi (Opsional)
                </label>
                <input 
                    type="text" 
                    name="headline" 
                    id="headline" 
                    value="{{ old('headline') }}" 
                    placeholder="Contoh: Dapatkan Promo Spesial Akhir Tahun"
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
            </div>

            <div>
                <label for="alt_text" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Alt Text Gambar (Aksesibilitas)
                </label>
                <input 
                    type="text" 
                    name="alt_text" 
                    id="alt_text" 
                    value="{{ old('alt_text') }}" 
                    placeholder="Deskripsi gambar untuk pembaca tuna netra"
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
            </div>
        </div>

        <div>
            <label for="body" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Teks Narasi / Deskripsi Singkat (Opsional)
            </label>
            <textarea 
                name="body" 
                id="body" 
                rows="2" 
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                placeholder="Penjelasan ringkas promo sponsor..."
            >{{ old('body') }}</textarea>
        </div>

        {{-- Schedule & Priority --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-[#E8E8E8]">
            <div>
                <label for="starts_at" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Mulai Tayang
                </label>
                <input 
                    type="datetime-local" 
                    name="starts_at" 
                    id="starts_at" 
                    value="{{ old('starts_at') }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
            </div>

            <div>
                <label for="ends_at" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Berakhir Tayang
                </label>
                <input 
                    type="datetime-local" 
                    name="ends_at" 
                    id="ends_at" 
                    value="{{ old('ends_at') }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
            </div>

            <div>
                <label for="priority" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Bobot Prioritas (0-100)
                </label>
                <input 
                    type="number" 
                    name="priority" 
                    id="priority" 
                    value="{{ old('priority', 0) }}" 
                    min="0" 
                    max="100" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none font-bold"
                />
            </div>
        </div>

        {{-- Active Checkbox --}}
        <div class="pt-2">
            <label class="flex items-center gap-2 text-xs font-bold text-[#111111] cursor-pointer">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    value="1" 
                    {{ old('is_active', true) ? 'checked' : '' }} 
                    class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-0"
                />
                <span>Aktifkan langsung materi iklan ini</span>
            </label>
        </div>

        <div class="pt-4 border-t border-[#E8E8E8] flex items-center justify-end gap-3">
            <a href="{{ route('admin.advertising.ads.index') }}" class="px-4 py-2 bg-white border border-[#CCCCCC] text-xs font-semibold text-[#5F6368] hover:text-[#111111] rounded-[4px]">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold rounded-[4px] transition-colors cursor-pointer">
                Simpan Materi Iklan
            </button>
        </div>
    </form>
</div>
@endsection
