@extends('layouts.admin')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold font-headline text-gray-900">Manajemen Halaman Statis</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola halaman kelembagaan resmi (Tentang Kami, Pedoman, Kebijakan, Kontak) dan halaman kustom.</p>
    </div>
    @can('create', App\Models\Page::class)
        <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center gap-2 bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-xs px-4 py-2.5 rounded-md shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Halaman Baru
        </a>
    @endcan
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.pages.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Cari judul atau slug..." 
                class="text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914] w-64"
            >

            <select name="status" class="text-xs border-gray-300 rounded-md shadow-sm focus:border-[#E50914] focus:ring-[#E50914]">
                <option value="">Semua Status</option>
                @foreach(App\Enums\PageStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs px-3 py-2 rounded-md transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.pages.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Pages Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-3 font-bold">Judul Halaman</th>
                    <th scope="col" class="px-6 py-3 font-bold">Tipe & URL Slug</th>
                    <th scope="col" class="px-6 py-3 font-bold">Status</th>
                    <th scope="col" class="px-6 py-3 font-bold">Terakhir Diperbarui</th>
                    <th scope="col" class="px-6 py-3 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($pages as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $p->title }}</div>
                            @if($p->excerpt)
                                <div class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $p->excerpt }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($p->page_type)
                                <span class="inline-block px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold rounded">
                                    {{ $p->page_type->label() }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Halaman Kustom</span>
                            @endif
                            <div class="text-xs text-gray-500 font-mono mt-1">/{{ $p->slug }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $p->status->badgeClasses() }}">
                                {{ $p->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            <div>{{ $p->updated_at->isoFormat('D MMM Y, HH:mm') }}</div>
                            @if($p->updater)
                                <div class="text-[11px] text-gray-400">Oleh: {{ $p->updater->name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.pages.preview', $p) }}" target="_blank" class="text-xs font-semibold text-gray-600 hover:text-gray-900" title="Pratinjau Layout">
                                Pratinjau
                            </a>

                            @can('publish', $p)
                                <form action="{{ route('admin.pages.toggle-publish', $p) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold {{ $p->isPublished() ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                        {{ $p->isPublished() ? 'Jadikan Draf' : 'Terbitkan' }}
                                    </button>
                                </form>
                            @endcan

                            @can('update', $p)
                                <a href="{{ route('admin.pages.edit', $p) }}" class="text-xs font-semibold text-[#E50914] hover:text-[#B80710]">
                                    Edit
                                </a>
                            @endcan

                            @can('delete', $p)
                                <form action="{{ route('admin.pages.destroy', $p) }}" method="POST" class="inline-block" data-confirm-delete data-delete-title="Hapus Halaman Permanen?" data-delete-name="{{ $p->title }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                        Hapus Permanen
                                    </button>
                                </form>
                            @else
                                @if($p->isProtectedCorePage())
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider" title="Halaman sistem dilindungi dari penghapusan">
                                        Dilindungi
                                    </span>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada halaman statis yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($pages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
