@extends('layouts.admin')

@section('title', 'Manajemen Navigasi Menu')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Navigasi & Menu Portal
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Kelola menu navigasi bilah atas (Header) dan navigasi kaki (Footer) tanpa perlu mengubah kode template.
            </p>
        </div>
    </div>

    <!-- Menu Selector Tabs -->
    <div class="flex items-center gap-2 border-b border-[#E8E8E8] pb-2 text-xs font-bold">
        @foreach ($menus as $menu)
            <a 
                href="{{ route('admin.navigation.index', ['menu' => $menu->key]) }}" 
                class="px-4 py-2 rounded-[4px] transition-colors {{ $activeMenu && $activeMenu->id === $menu->id ? 'bg-[#111111] text-white' : 'text-[#5F6368] hover:bg-[#F3F4F6]' }}"
            >
                {{ $menu->name }}
            </a>
        @endforeach
    </div>

    @if ($activeMenu)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Navigation Items List & Reorder (Left 7 cols) --}}
            <div class="lg:col-span-7 bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
                <form action="{{ route('admin.navigation.reorder', $activeMenu) }}" method="POST">
                    @csrf
                    <div class="p-4 border-b border-[#E8E8E8] bg-[#FAFAFA] flex items-center justify-between">
                        <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                            Item Navigasi: {{ $activeMenu->name }} ({{ $items->count() }})
                        </span>
                        <button type="submit" class="px-3 py-1.5 bg-[#111111] hover:bg-black text-white text-xs font-bold rounded-[4px] transition-colors cursor-pointer">
                            Simpan Urutan
                        </button>
                    </div>

                    <div class="divide-y divide-[#E8E8E8]">
                        @forelse ($items as $item)
                            <div class="p-3.5 hover:bg-[#FDFDFD] flex items-center justify-between gap-3 text-xs transition-colors {{ ! $item->is_active ? 'opacity-50' : '' }}">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <input 
                                        type="number" 
                                        name="order[{{ $item->id }}]" 
                                        value="{{ $item->sort_order }}" 
                                        class="w-12 text-center bg-[#F9FAFB] border border-[#CCCCCC] rounded py-1 text-xs font-bold focus:border-[#E50914] focus:outline-none"
                                        min="0"
                                    />
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-sm text-[#111111]">{{ $item->label }}</span>
                                            <span class="px-1.5 py-0.5 bg-neutral-100 text-neutral-700 text-[10px] rounded font-semibold">
                                                {{ $item->link_type->label() }}
                                            </span>
                                            @if ($item->open_new_tab)
                                                <span class="text-[10px] text-blue-600 font-bold">Tab Baru</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-[#80868B] truncate mt-0.5">
                                            Tujuan: {{ $item->getResolvedUrl() }}
                                        </div>

                                        {{-- Sub-items (Child Menu Items) --}}
                                        @if ($item->children->isNotEmpty())
                                            <div class="mt-2 pl-4 border-l-2 border-neutral-200 space-y-1.5">
                                                @foreach ($item->children as $child)
                                                    <div class="flex items-center justify-between text-[11px] text-[#5F6368]">
                                                        <span>↳ {{ $child->label }} ({{ $child->getResolvedUrl() }})</span>
                                                        <form action="{{ route('admin.navigation.items.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub-menu ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 hover:underline cursor-pointer">Hapus</button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <form action="{{ route('admin.navigation.items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus item menu ini beserta sub-menunya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 bg-white border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-[#80868B]">
                                Belum ada item navigasi pada menu ini.
                            </div>
                        @endforelse
                    </div>
                </form>
            </div>

            {{-- Add New Menu Item Form (Right 5 cols) --}}
            <div class="lg:col-span-5 bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs p-5 space-y-4">
                <h2 class="font-headline font-bold text-base text-[#111111] pb-2 border-b border-[#E8E8E8]">
                    + Tambah Item Navigasi
                </h2>

                <form action="{{ route('admin.navigation.items.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="menu_id" value="{{ $activeMenu->id }}">

                    <div>
                        <label for="label" class="block font-bold text-[#111111] uppercase tracking-wider mb-1">
                            Label Menu <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="label" 
                            id="label" 
                            placeholder="Contoh: Nasional, Bisnis, Opini" 
                            class="w-full bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                            required
                        />
                    </div>

                    <div>
                        <label for="parent_id" class="block font-bold text-[#111111] uppercase tracking-wider mb-1">
                            Menu Induk (Opsional / Sub-menu)
                        </label>
                        <select name="parent_id" id="parent_id" class="w-full bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none">
                            <option value="">-- Menu Utama (Tingkat 1) --</option>
                            @foreach ($items as $pItem)
                                <option value="{{ $pItem->id }}">Sub dari: {{ $pItem->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="link_type" class="block font-bold text-[#111111] uppercase tracking-wider mb-1">
                            Tipe Tautan <span class="text-red-600">*</span>
                        </label>
                        <select name="link_type" id="link_type" class="w-full bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none" onchange="handleLinkTypeChange(this.value)">
                            @foreach (\App\Enums\MenuLinkType::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category Selector --}}
                    <div id="wrapper_category">
                        <label for="category_id" class="block font-bold text-[#111111] uppercase tracking-wider mb-1">
                            Pilih Rubrik Kategori <span class="text-red-600">*</span>
                        </label>
                        <select name="category_id" id="category_id" class="w-full bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none">
                            <option value="">-- Pilih Rubrik --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Named Route Selector --}}
                    <div id="wrapper_route" class="hidden">
                        <label for="route_name" class="block font-bold text-[#111111] uppercase tracking-wider mb-1">
                            Pilih Halaman Sistem (Named Route) <span class="text-red-600">*</span>
                        </label>
                        <select name="route_name" id="route_name" class="w-full bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none">
                            <option value="">-- Pilih Halaman --</option>
                            @foreach ($allowedRoutes as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- External URL Input --}}
                    <div id="wrapper_url" class="hidden">
                        <label for="url" class="block font-bold text-[#111111] uppercase tracking-wider mb-1">
                            Alamat URL Eksternal <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="url" 
                            name="url" 
                            id="url" 
                            placeholder="https://..." 
                            class="w-full bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:border-[#E50914] focus:outline-none"
                        />
                    </div>

                    <div class="space-y-2 pt-2">
                        <label class="flex items-center gap-2 font-semibold text-[#111111] cursor-pointer">
                            <input type="checkbox" name="open_new_tab" value="1" class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-0">
                            <span>Buka di tab baru (target="_blank")</span>
                        </label>
                        <label class="flex items-center gap-2 font-semibold text-[#111111] cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-0">
                            <span>Aktifkan langsung item navigasi ini</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-2 bg-[#E50914] hover:bg-[#B80710] text-white font-bold rounded-[4px] transition-colors cursor-pointer">
                        + Tambahkan ke Navigasi
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    function handleLinkTypeChange(type) {
        document.getElementById('wrapper_category').classList.toggle('hidden', type !== 'category');
        document.getElementById('wrapper_route').classList.toggle('hidden', type !== 'route');
        document.getElementById('wrapper_url').classList.toggle('hidden', type !== 'url');
    }
</script>
@endsection
