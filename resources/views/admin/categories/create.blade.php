@extends('layouts.admin')

@section('title', 'Tambah Kategori Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <x-admin.page-header 
        title="Tambah Kategori / Rubrik Baru" 
        description="Membuat rubrik kanal berita baru untuk portal TopNews"
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Kategori Rubrik', 'url' => route('admin.categories.index')],
                ['label' => 'Tambah Kategori']
            ]" />
        </x-slot>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="p-4 rounded-[6px] bg-[#FDE8E9] border border-[#E50914] text-xs text-[#C8102E]">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-admin.card>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label for="name" class="block font-bold text-[#171717] mb-1">
                    Nama Kategori / Rubrik <span class="text-[#E50914]">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Finansial & Pasar" class="w-full text-xs border border-[#CCCCCC] rounded p-2.5 focus:outline-none focus:border-[#E50914]" />
            </div>

            <div>
                <label for="slug" class="block font-bold text-[#6B7280] mb-1">
                    Slug URL (Opsional, dibuat otomatis jika dikosongkan)
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="finansial-pasar" class="w-full text-xs font-mono border border-[#CCCCCC] rounded p-2 focus:outline-none focus:border-[#E50914]" />
            </div>

            <div>
                <label for="description" class="block font-semibold text-[#6B7280] mb-1">Deskripsi Rubrik</label>
                <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat mengenai fokus liputan kanal ini..." class="w-full text-xs border border-[#CCCCCC] rounded p-2 focus:outline-none focus:border-[#E50914]">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="accent_color" class="block font-semibold text-[#6B7280] mb-1">Warna Aksen Kategori</label>
                    <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', '#E50914') }}" class="h-9 w-full p-1 rounded border border-[#CCCCCC] cursor-pointer">
                </div>

                <div>
                    <label for="sort_order" class="block font-semibold text-[#6B7280] mb-1">Urutan Umum (Sort Order)</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full text-xs border border-[#CCCCCC] rounded p-2 focus:outline-none focus:border-[#E50914]">
                </div>
            </div>

            <!-- Discovery / Curation Placement Controls -->
            <div class="p-3 bg-[#F9FAFB] rounded-[6px] border border-[#E5E7EB] space-y-3">
                <span class="block font-bold text-[#111111] uppercase tracking-wider text-[10px]">Kurasi Tampilan & Navigasi Publik</span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-[#171717]">
                        <input type="checkbox" name="show_on_homepage" value="1" {{ old('show_on_homepage', true) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]">
                        <span>Tampilkan di Beranda (Homepage Highlight)</span>
                    </label>

                    <div>
                        <label for="homepage_order" class="block text-[11px] text-[#6B7280] mb-0.5">Urutan di Beranda</label>
                        <input type="number" name="homepage_order" id="homepage_order" value="{{ old('homepage_order', 0) }}" min="0" class="w-full text-xs border border-[#CCCCCC] rounded p-1.5 focus:outline-none focus:border-[#E50914]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center pt-2 border-t border-[#E5E7EB]">
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-[#171717]">
                        <input type="checkbox" name="show_in_navigation" value="1" {{ old('show_in_navigation', true) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]">
                        <span>Tampilkan di Menu Navigasi Header</span>
                    </label>

                    <div>
                        <label for="navigation_order" class="block text-[11px] text-[#6B7280] mb-0.5">Urutan di Header</label>
                        <input type="number" name="navigation_order" id="navigation_order" value="{{ old('navigation_order', 0) }}" min="0" class="w-full text-xs border border-[#CCCCCC] rounded p-1.5 focus:outline-none focus:border-[#E50914]">
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-[#171717]">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]">
                    <span>Status Kategori Aktif</span>
                </label>
            </div>

            <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-2">
                <x-button variant="outline" size="sm" :href="route('admin.categories.index')">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="sm">
                    Simpan Kategori
                </x-button>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection
