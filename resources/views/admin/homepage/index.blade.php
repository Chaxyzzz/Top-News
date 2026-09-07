@extends('layouts.admin')

@section('title', 'Manajemen Bagian Homepage')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Homepage & Tata Letak Redaksi
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Atur urutan bagian, sumber data, dan kurasi berita utama yang tampil di beranda publik TopNews.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.homepage.preview') }}" target="_blank" class="px-3.5 py-2 bg-white border border-[#CCCCCC] hover:border-[#111111] text-[#111111] font-bold text-xs rounded-[4px] transition-colors flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4 text-[#5F6368]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>Pratinjau Beranda</span>
            </a>
        </div>
    </div>

    <!-- Alert / Explanation -->
    <div class="bg-blue-50 border border-blue-200 rounded-[6px] p-4 text-xs text-blue-900 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="space-y-1">
            <p class="font-bold">Standar Tata Letak Terkendali TopNews</p>
            <p class="text-blue-800 leading-relaxed">
                Komposisi beranda menggunakan template editorial berstandar tinggi untuk menjaga kecepatan akses, keseragaman tipografi, dan aksesibilitas. Bagian dengan sumber <strong>Kurasi Manual</strong> memungkinkan redaktur memilih naskah terbit secara spesifik. Bagian yang tidak memiliki berita publik aktif akan disembunyikan secara otomatis.
            </p>
        </div>
    </div>

    <!-- Sections Table with Numeric Reorder Form -->
    <form action="{{ route('admin.homepage.reorder') }}" method="POST">
        @csrf
        <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
            <div class="p-4 border-b border-[#E8E8E8] flex items-center justify-between bg-[#FAFAFA]">
                <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                    Daftar Bagian Beranda ({{ $sections->count() }})
                </span>
                <button type="submit" class="px-3 py-1.5 bg-[#111111] hover:bg-black text-white text-xs font-bold rounded-[4px] transition-colors cursor-pointer">
                    Simpan Urutan
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F8F9FA] border-b border-[#E8E8E8] text-[#5F6368] font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 w-20 text-center">Urutan</th>
                            <th class="py-3 px-4">Nama Bagian</th>
                            <th class="py-3 px-4">Tipe Bagian</th>
                            <th class="py-3 px-4">Sumber Data</th>
                            <th class="py-3 px-4">Bentuk Tata Letak</th>
                            <th class="py-3 px-4 text-center">Batas</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8E8E8]">
                        @foreach ($sections as $section)
                            <tr class="hover:bg-[#FDFDFD] transition-colors {{ ! $section->is_active ? 'opacity-60 bg-neutral-50' : '' }}">
                                <td class="py-3 px-4 text-center">
                                    <input 
                                        type="number" 
                                        name="order[{{ $section->id }}]" 
                                        value="{{ $section->sort_order }}" 
                                        class="w-14 text-center bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] py-1 text-xs font-bold focus:border-[#E50914] focus:outline-none"
                                        min="0"
                                    />
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-[#111111] text-sm">
                                        {{ $section->title }}
                                    </div>
                                    @if ($section->subtitle)
                                        <div class="text-[11px] text-[#80868B] truncate max-w-xs mt-0.5">
                                            {{ $section->subtitle }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 bg-neutral-100 text-neutral-800 font-semibold rounded text-[11px]">
                                        {{ $section->section_type->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div>
                                        <span class="font-semibold text-[#111111]">
                                            {{ $section->source_type->label() }}
                                        </span>
                                        @if ($section->source_type === \App\Enums\HomepageSourceType::Category && $section->category)
                                            <span class="block text-[10px] text-blue-600 font-bold mt-0.5">
                                                Rubrik: {{ $section->category->name }}
                                            </span>
                                        @elseif ($section->source_type === \App\Enums\HomepageSourceType::Manual)
                                            <span class="block text-[10px] text-emerald-700 font-bold mt-0.5">
                                                {{ $section->curated_articles_count }} artikel dikurasi
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-neutral-600">
                                    {{ $section->layout_variant->label() }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-[#111111]">
                                    {{ $section->item_limit }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $section->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-200 text-neutral-700' }}">
                                        {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if ($section->source_type === \App\Enums\HomepageSourceType::Manual || $section->section_type === \App\Enums\HomepageSectionType::Hero)
                                            <a href="{{ route('admin.homepage.curate', $section) }}" class="px-2 py-1 bg-white border border-[#CCCCCC] hover:border-emerald-600 hover:text-emerald-700 text-[#111111] font-semibold text-[11px] rounded transition-colors" title="Pilih naskah untuk bagian ini">
                                                Kurasi Berita
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.homepage.edit', $section) }}" class="px-2 py-1 bg-white border border-[#CCCCCC] hover:border-[#111111] text-[#111111] font-semibold text-[11px] rounded transition-colors">
                                            Ubah
                                        </a>

                                        <form action="{{ route('admin.homepage.toggle-active', $section) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-white border border-[#CCCCCC] text-[#5F6368] hover:text-[#111111] font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                                {{ $section->is_active ? 'Matikan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        @if (! $section->section_type->isProtected())
                                            <form action="{{ route('admin.homepage.destroy', $section) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bagian ini dari tata letak beranda?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 bg-white border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@endsection
