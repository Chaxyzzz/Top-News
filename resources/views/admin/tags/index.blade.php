@extends('layouts.admin')

@section('title', 'Tagar Berita (Tags)')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Tagar & Topik Berita" 
        description="Kelola kata kunci tagar (tags) untuk pengelompokan topik hangat, indeks penelusuran, dan keterkaitan artikel."
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Tagar Berita']
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Create Tag Form (Cols 1-4) -->
        <div class="lg:col-span-4">
            <x-admin.card title="Tambah Tagar Baru">
                <form action="{{ route('admin.tags.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label for="name" class="block font-bold text-[#171717] mb-1">
                            Nama Tagar <span class="text-[#E50914]">*</span>
                        </label>
                        <input type="text" name="name" id="name" required placeholder="Contoh: Kecerdasan Buatan" class="w-full text-xs border border-[#CCCCCC] rounded p-2 focus:outline-none focus:border-[#E50914]" />
                    </div>

                    <div>
                        <label for="slug" class="block font-semibold text-[#6B7280] mb-1">
                            Slug (Opsional)
                        </label>
                        <input type="text" name="slug" id="slug" placeholder="kecerdasan-buatan" class="w-full text-xs font-mono border border-[#CCCCCC] rounded p-2 focus:outline-none focus:border-[#E50914]" />
                    </div>

                    <x-button type="submit" variant="primary" size="sm" class="w-full">
                        + Tambah Tagar
                    </x-button>
                </form>
            </x-admin.card>
        </div>

        <!-- Tags List (Cols 5-12) -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-subtle overflow-hidden">
                <div class="p-4 border-b border-[#F3F4F6]">
                    <form action="{{ route('admin.tags.index') }}" method="GET" class="flex gap-2">
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama tagar..." class="w-full text-xs border border-[#CCCCCC] rounded px-3 py-1.5 focus:outline-none focus:border-[#E50914]" />
                        <x-button type="submit" variant="secondary" size="sm">Cari</x-button>
                        @if (request('search'))
                            <a href="{{ route('admin.tags.index') }}" class="px-2.5 py-1.5 text-xs text-[#6B7280] hover:text-[#171717] bg-[#F3F4F6] rounded flex items-center justify-center">Reset</a>
                        @endif
                    </form>
                </div>

                @if ($tags->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#6B7280] uppercase tracking-wider font-bold">
                                <tr>
                                    <th class="py-3 px-4">Nama Tagar</th>
                                    <th class="py-3 px-4">Slug URL</th>
                                    <th class="py-3 px-4">Penggunaan Artikel</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F3F4F6]">
                                @foreach ($tags as $tag)
                                    <tr class="hover:bg-[#F9FAFB] transition-colors">
                                        <td class="py-3 px-4 font-bold text-[#171717]">
                                            #{{ $tag->name }}
                                        </td>
                                        <td class="py-3 px-4 font-mono text-[#6B7280]">
                                            /tag/{{ $tag->slug }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-bold px-2 py-0.5 bg-[#F3F4F6] rounded text-[#171717]">
                                                {{ $tag->articles_count }} artikel
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right whitespace-nowrap">
                                             @can('delete', $tag)
                                                 <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline" data-confirm-delete data-delete-title="Hapus Tagar Permanen?" data-delete-name="#{{ $tag->name }}">
                                                     @csrf
                                                     @method('DELETE')
                                                     <button type="submit" class="p-1.5 text-[#6B7280] hover:text-[#C8102E] hover:bg-[#FDE8E9] rounded cursor-pointer" title="Hapus Tagar Permanen">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                         </svg>
                                                     </button>
                                                 </form>
                                             @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-[#E5E7EB]">
                        {{ $tags->links() }}
                    </div>
                @else
                    <x-admin.empty-state 
                        title="Tidak Ada Tagar" 
                        description="Tambahkan tagar pertama melalui formulir di samping."
                    />
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
