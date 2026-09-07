@extends('layouts.admin')

@section('title', 'Ubah Breaking News')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.breaking-news.index') }}" class="text-xs font-semibold text-[#5F6368] hover:text-[#111111] flex items-center gap-1 mb-1">
                ← Kembali ke Daftar Breaking News
            </a>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Ubah Breaking News
            </h1>
        </div>
    </div>

    <form action="{{ route('admin.breaking-news.update', $breaking) }}" method="POST" class="bg-white p-6 rounded-[6px] border border-[#E8E8E8] shadow-xs space-y-5">
        @csrf
        @method('PUT')

        {{-- Headline --}}
        <div>
            <label for="headline" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Headline / Judul Warta Kilat <span class="text-red-600">*</span>
            </label>
            <input 
                type="text" 
                name="headline" 
                id="headline" 
                value="{{ old('headline', $breaking->headline) }}" 
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none font-bold"
                required
            />
            @error('headline') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Destination: Article Selector or External URL --}}
        <div class="space-y-4 pt-2 border-t border-[#E8E8E8]">
            <p class="text-xs font-bold text-[#111111] uppercase tracking-wider">Tautan Tujuan</p>
            
            <div>
                <label for="article_id" class="block text-xs font-bold text-[#5F6368] mb-1">
                    Tautkan ke Artikel Terbit TopNews
                </label>
                <select name="article_id" id="article_id" class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none">
                    <option value="">-- Tidak ditautkan ke artikel internal --</option>
                    @foreach ($recentArticles as $art)
                        <option value="{{ $art->id }}" {{ old('article_id', $breaking->article_id) == $art->id ? 'selected' : '' }}>
                            {{ $art->title }} ({{ $art->category?->name ?? 'News' }} - {{ $art->published_at?->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
                @error('article_id') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="external_url" class="block text-xs font-bold text-[#5F6368] mb-1">
                    Atau Tautan Eksternal Khusus (Harus http:// atau https://)
                </label>
                <input 
                    type="url" 
                    name="external_url" 
                    id="external_url" 
                    value="{{ old('external_url', $breaking->external_url) }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
                @error('external_url') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Schedule Window & Priority --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-[#E8E8E8]">
            <div>
                <label for="starts_at" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Mulai Tayang (Opsional)
                </label>
                <input 
                    type="datetime-local" 
                    name="starts_at" 
                    id="starts_at" 
                    value="{{ old('starts_at', $breaking->starts_at?->format('Y-m-d\TH:i')) }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
                @error('starts_at') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="ends_at" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Berakhir Tayang (Opsional)
                </label>
                <input 
                    type="datetime-local" 
                    name="ends_at" 
                    id="ends_at" 
                    value="{{ old('ends_at', $breaking->ends_at?->format('Y-m-d\TH:i')) }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                />
                @error('ends_at') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="priority" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Bobot Prioritas (0-100)
                </label>
                <input 
                    type="number" 
                    name="priority" 
                    id="priority" 
                    value="{{ old('priority', $breaking->priority) }}" 
                    min="0" 
                    max="100" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none font-bold"
                />
                @error('priority') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Active Checkbox --}}
        <div class="pt-2">
            <label class="flex items-center gap-2 text-xs font-bold text-[#111111] cursor-pointer">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    value="1" 
                    {{ old('is_active', $breaking->is_active) ? 'checked' : '' }} 
                    class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-0"
                />
                <span>Aktifkan breaking news ini di beranda</span>
            </label>
        </div>

        <div class="pt-4 border-t border-[#E8E8E8] flex items-center justify-end gap-3">
            <a href="{{ route('admin.breaking-news.index') }}" class="px-4 py-2 bg-white border border-[#CCCCCC] text-xs font-semibold text-[#5F6368] hover:text-[#111111] rounded-[4px]">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold rounded-[4px] transition-colors cursor-pointer">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
