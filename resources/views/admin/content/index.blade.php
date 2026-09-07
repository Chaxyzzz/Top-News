@extends('layouts.admin')

@section('title', 'Kelola Konten & Taksonomi')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Struktur Konten & Taksonomi" 
        description="Pengorganisasian rubrik, kategori berita, topik hangat, tagar, dan halaman statis TopNews."
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Kelola Konten']
            ]" />
        </x-slot>
    </x-admin.page-header>

    <div class="p-4 bg-[#FFF1F2] border border-[#FECDD3] rounded-[6px] text-xs text-[#C8102E] flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 bg-[#E50914] text-white font-bold rounded text-[10px] uppercase">Phase 03 Foundation</span>
            <span class="font-medium">Modul manajemen Kategori, Tagar, dan Layout CMS Beranda akan diaktifkan secara menyeluruh pada <strong>Phase 04</strong> dan <strong>Phase 09</strong>.</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-admin.card title="Kategori & Rubrik" subtitle="Pengelompokan berita utama">
            <p class="text-xs text-[#6B7280] leading-relaxed mb-4">
                Rubrik terdaftar saat ini: Nasional, Politik, Ekonomi & Bisnis, Teknologi, Olahraga, Hiburan, Gaya Hidup, dan Internasional.
            </p>
            <span class="text-[11px] font-bold text-[#E50914]">Tersedia di Phase 04</span>
        </x-admin.card>

        <x-admin.card title="Tagar & Topik Hangat" subtitle="Kata kunci pencarian dan tren">
            <p class="text-xs text-[#6B7280] leading-relaxed mb-4">
                Pengelolaan tag berita untuk meningkatkan penelusuran, indexing SEO, dan integrasi topik terhangat.
            </p>
            <span class="text-[11px] font-bold text-[#E50914]">Tersedia di Phase 04</span>
        </x-admin.card>

        <x-admin.card title="Halaman Statis" subtitle="Informasi legal & korporat">
            <p class="text-xs text-[#6B7280] leading-relaxed mb-4">
                Pengelolaan halaman Tentang Kami, Pedoman Media Siber, Struktur Redaksi, dan Hubungi Kami.
            </p>
            <span class="text-[11px] font-bold text-[#E50914]">Tersedia di Phase 10</span>
        </x-admin.card>
    </div>
</div>
@endsection
