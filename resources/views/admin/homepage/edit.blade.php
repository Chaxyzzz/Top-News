@extends('layouts.admin')

@section('title', 'Ubah Bagian: ' . $section->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.homepage.index') }}" class="text-xs font-semibold text-[#5F6368] hover:text-[#111111] flex items-center gap-1 mb-1">
                ← Kembali ke Manajemen Beranda
            </a>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Pengaturan Bagian: {{ $section->title }}
            </h1>
        </div>
    </div>

    <form action="{{ route('admin.homepage.update', $section) }}" method="POST" class="bg-white p-6 rounded-[6px] border border-[#E8E8E8] shadow-xs space-y-5">
        @csrf
        @method('PUT')

        {{-- Section Title --}}
        <div>
            <label for="title" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Judul Tampilan Bagian <span class="text-red-600">*</span>
            </label>
            <input 
                type="text" 
                name="title" 
                id="title" 
                value="{{ old('title', $section->title) }}" 
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                required
            />
            @error('title') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Subtitle --}}
        <div>
            <label for="subtitle" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Sub-judul / Deskripsi Singkat
            </label>
            <input 
                type="text" 
                name="subtitle" 
                id="subtitle" 
                value="{{ old('subtitle', $section->subtitle) }}" 
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
            />
            @error('subtitle') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Source Type --}}
        <div>
            <label for="source_type" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Sumber Berita <span class="text-red-600">*</span>
            </label>
            <select 
                name="source_type" 
                id="source_type" 
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                onchange="toggleCategorySelect(this.value)"
            >
                @foreach (\App\Enums\HomepageSourceType::cases() as $source)
                    <option value="{{ $source->value }}" {{ old('source_type', $section->source_type->value) === $source->value ? 'selected' : '' }}>
                        {{ $source->label() }}
                    </option>
                @endforeach
            </select>
            @error('source_type') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Category Selection (conditionally shown if category source) --}}
        <div id="category_wrapper" class="{{ old('source_type', $section->source_type->value) === 'category' ? '' : 'hidden' }}">
            <label for="category_id" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Rubrik / Kategori Terpilih <span class="text-red-600">*</span>
            </label>
            <select 
                name="category_id" 
                id="category_id" 
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
            >
                <option value="">-- Pilih Rubrik Berita --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $section->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} ({{ $cat->slug }})
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Layout Variant --}}
        <div>
            <label for="layout_variant" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                Bentuk Tata Letak (Layout Variant) <span class="text-red-600">*</span>
            </label>
            <select 
                name="layout_variant" 
                id="layout_variant" 
                class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
            >
                @foreach ($section->section_type->allowedLayouts() as $layout)
                    <option value="{{ $layout->value }}" {{ old('layout_variant', $section->layout_variant->value) === $layout->value ? 'selected' : '' }}>
                        {{ $layout->label() }}
                    </option>
                @endforeach
            </select>
            @error('layout_variant') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Item Limit & Order Grid --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="item_limit" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Batas Jumlah Berita (1 - 12) <span class="text-red-600">*</span>
                </label>
                <input 
                    type="number" 
                    name="item_limit" 
                    id="item_limit" 
                    value="{{ old('item_limit', $section->item_limit) }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                    min="1" 
                    max="12" 
                    required
                />
                @error('item_limit') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="sort_order" class="block text-xs font-bold text-[#111111] uppercase tracking-wider mb-1">
                    Urutan Posisi
                </label>
                <input 
                    type="number" 
                    name="sort_order" 
                    id="sort_order" 
                    value="{{ old('sort_order', $section->sort_order) }}" 
                    class="w-full text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                    min="0"
                />
                @error('sort_order') <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Active Checkbox --}}
        <div class="pt-2">
            <label class="flex items-center gap-2 text-xs font-bold text-[#111111] cursor-pointer">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    value="1" 
                    {{ old('is_active', $section->is_active) ? 'checked' : '' }} 
                    class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-0"
                />
                <span>Aktifkan bagian ini di halaman beranda</span>
            </label>
            <p class="text-[11px] text-[#80868B] ml-5 mt-0.5">Jika dimatikan, bagian ini sama sekali tidak akan dirender di publik.</p>
        </div>

        <div class="pt-4 border-t border-[#E8E8E8] flex items-center justify-end gap-3">
            <a href="{{ route('admin.homepage.index') }}" class="px-4 py-2 bg-white border border-[#CCCCCC] text-xs font-semibold text-[#5F6368] hover:text-[#111111] rounded-[4px]">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold rounded-[4px] transition-colors cursor-pointer">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    function toggleCategorySelect(val) {
        const wrap = document.getElementById('category_wrapper');
        if (val === 'category') {
            wrap.classList.remove('hidden');
        } else {
            wrap.classList.add('hidden');
        }
    }
</script>
@endsection
