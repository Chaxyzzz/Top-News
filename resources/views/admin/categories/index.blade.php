@extends('layouts.admin')

@section('title', 'Kategori & Rubrik Berita')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Rubrik & Kategori Berita" 
        description="Kelola hierarki rubrik pemberitaan, warna aksen identitas kategori, dan urutan navigasi publik."
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Kategori Rubrik']
            ]" />
        </x-slot>

        <x-slot name="actions">
            @can('create', App\Models\Category::class)
                <x-button variant="primary" size="sm" :href="route('admin.categories.create')">
                    + Tambah Kategori
                </x-button>
            @endcan
        </x-slot>
    </x-admin.page-header>

    <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-subtle overflow-hidden">
        @if ($categories->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#6B7280] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3 px-4">Nama Rubrik</th>
                            <th class="py-3 px-4">Slug URL</th>
                            <th class="py-3 px-4">Jumlah Artikel</th>
                            <th class="py-3 px-4">Urutan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F3F4F6]">
                        @foreach ($categories as $cat)
                            <tr class="hover:bg-[#F9FAFB] transition-colors">
                                <td class="py-3.5 px-4 font-bold text-[#171717]">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $cat->accent_color ?? '#E50914' }};"></span>
                                        <span>{{ $cat->name }}</span>
                                    </div>
                                    @if ($cat->description)
                                        <span class="text-[11px] text-[#6B7280] font-normal block pl-5">{{ $cat->description }}</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 font-mono text-[#6B7280]">
                                    /category/{{ $cat->slug }}
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="font-bold px-2 py-0.5 bg-[#F3F4F6] rounded text-[#171717]">
                                        {{ $cat->articles_count }} artikel
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 font-mono text-[#6B7280]">
                                    {{ $cat->sort_order }}
                                </td>

                                <td class="py-3.5 px-4">
                                    @if ($cat->is_active)
                                        <x-admin.badge variant="success" size="xs">Aktif</x-admin.badge>
                                    @else
                                        <x-admin.badge variant="subtle" size="xs">Nonaktif</x-admin.badge>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @can('update', $cat)
                                            <a href="{{ route('admin.categories.edit', $cat) }}" class="p-1.5 text-[#4B5563] hover:text-[#E50914] hover:bg-[#FFF1F2] rounded" title="Sunting Kategori">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('delete', $cat)
                                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline" data-confirm-delete data-delete-title="Hapus Kategori Permanen?" data-delete-name="{{ $cat->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-[#6B7280] hover:text-[#C8102E] hover:bg-[#FDE8E9] rounded cursor-pointer" title="Hapus Kategori Permanen">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[#E5E7EB]">
                {{ $categories->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="Tidak Ada Kategori" 
                description="Buat kategori pertama Anda untuk mengelompokkan naskah berita."
            />
        @endif
    </div>
</div>
@endsection
