@extends('layouts.admin')

@section('title', 'Buat Kampanye Iklan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.advertising.campaigns.index') }}" class="text-xs font-semibold text-[#5F6368] hover:text-[#111111] flex items-center gap-1 mb-1">
                ← Kembali ke Daftar Kampanye
            </a>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Buat Kampanye Iklan Baru
            </h1>
        </div>
    </div>

    <form action="{{ route('admin.advertising.campaigns.store') }}" method="POST" class="bg-white p-6 rounded-[6px] border border-[#E8E8E8] shadow-xs space-y-5">
        @csrf

        {{-- Campaign Name --}}
        <div>
            <label for="name" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Nama Kampanye <span class="text-red-600">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name') }}" 
                placeholder="Contoh: Kampanye Ramadhan 2026 - Brand Bank ABC"
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                required
            />
            @error('name') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Advertiser Name --}}
        <div>
            <label for="advertiser" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Nama Pengiklan / Mitra Bisnis <span class="text-red-600">*</span>
            </label>
            <input 
                type="text" 
                name="advertiser" 
                id="advertiser" 
                value="{{ old('advertiser') }}" 
                placeholder="Contoh: PT Bank ABC Indonesia Tbk"
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                required
            />
            @error('advertiser') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Status --}}
        <div>
            <label for="status" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Status Awal Kampanye <span class="text-red-600">*</span>
            </label>
            <select name="status" id="status" class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none">
                @foreach ($statuses as $st)
                    <option value="{{ $st->value }}" {{ old('status', 'active') === $st->value ? 'selected' : '' }}>
                        {{ $st->label() }}
                    </option>
                @endforeach
            </select>
            @error('status') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Dates --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="starts_at" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Tanggal Mulai Tayang
                </label>
                <input 
                    type="datetime-local" 
                    name="starts_at" 
                    id="starts_at" 
                    value="{{ old('starts_at') }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
                @error('starts_at') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="ends_at" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Tanggal Berakhir Tayang
                </label>
                <input 
                    type="datetime-local" 
                    name="ends_at" 
                    id="ends_at" 
                    value="{{ old('ends_at') }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
                @error('ends_at') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Contact Info (Optional) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-[#E8E8E8]">
            <div>
                <label for="contact_name" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Nama Kontak PIC Pengiklan
                </label>
                <input 
                    type="text" 
                    name="contact_name" 
                    id="contact_name" 
                    value="{{ old('contact_name') }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
            </div>
            <div>
                <label for="contact_email" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Email Kontak PIC
                </label>
                <input 
                    type="email" 
                    name="contact_email" 
                    id="contact_email" 
                    value="{{ old('contact_email') }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
            </div>
        </div>

        <div class="pt-4 border-t border-[#E8E8E8] flex items-center justify-end gap-3">
            <a href="{{ route('admin.advertising.campaigns.index') }}" class="px-4 py-2 bg-white border border-[#CCCCCC] text-xs font-semibold text-[#5F6368] hover:text-[#111111] rounded-[4px]">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold rounded-[4px] transition-colors cursor-pointer">
                Simpan Kampanye
            </button>
        </div>
    </form>
</div>
@endsection
