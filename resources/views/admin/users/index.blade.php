@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Staf')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#E8E8E8] pb-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Manajemen Pengguna & Staf Redaksi
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Kelola akun tim redaksi, reporter, editor, dan hak akses sistem TopNews.
            </p>
        </div>

        @can('create', App\Models\User::class)
            <x-button variant="primary" size="sm" :href="route('admin.users.create')">
                + Tambah Pengguna Baru
            </x-button>
        @endcan
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-subtle">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-5">
                <input 
                    type="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari nama, username, atau email..." 
                    class="w-full text-xs border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:outline-none focus:border-[#E50914]"
                />
            </div>

            <div class="sm:col-span-3">
                <select name="role_id" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:outline-none focus:border-[#E50914] bg-white">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="status" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:outline-none focus:border-[#E50914] bg-white">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <x-button type="submit" variant="secondary" size="sm" class="w-full">
                    Filter
                </x-button>
                @if (request()->hasAny(['search', 'role_id', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="px-2.5 py-1.5 text-xs text-[#5F6368] hover:text-[#111111] bg-[#F2F2F2] rounded flex items-center justify-center" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-[8px] border border-[#E8E8E8] shadow-subtle overflow-hidden">
        @if ($users->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F7F7F7] border-b border-[#E8E8E8] text-[#80868B] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3 px-4">Nama & Username</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Login Terakhir</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F2F2F2]">
                        @foreach ($users as $user)
                            <tr class="hover:bg-[#F9F9F9] transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        @if ($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-[#E8E8E8]">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-[#111111] text-white flex items-center justify-center font-bold text-xs">
                                                {{ $user->initials }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.users.show', $user) }}" class="font-bold text-sm text-[#111111] hover:text-[#E50914] transition-colors">
                                                {{ $user->name }}
                                            </a>
                                            <span class="block text-[11px] text-[#80868B]">@ {{ $user->username }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-[#5F6368] font-mono">
                                    {{ $user->email }}
                                </td>

                                <td class="py-3 px-4">
                                    @if ($user->primary_role)
                                        <x-badge :variant="$user->isSuperAdmin() ? 'primary' : 'dark'" size="xs">
                                            {{ $user->primary_role->label }}
                                        </x-badge>
                                    @else
                                        <span class="text-[#80868B] italic">-</span>
                                    @endif
                                </td>

                                <td class="py-3 px-4">
                                    <x-badge :variant="$user->status->badgeVariant()" size="xs">
                                        {{ $user->status->label() }}
                                    </x-badge>
                                </td>

                                <td class="py-3 px-4 text-[#5F6368] whitespace-nowrap">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah' }}
                                </td>

                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.users.show', $user) }}" class="p-1.5 text-[#5F6368] hover:text-[#111111] hover:bg-[#F2F2F2] rounded" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @can('update', $user)
                                            <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 text-[#5F6368] hover:text-[#111111] hover:bg-[#F2F2F2] rounded" title="Edit Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('suspend', $user)
                                            @if ($user->isActive())
                                                <form action="{{ route('admin.users.suspend', $user) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menangguhkan akun pengguna ini?')">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 text-[#E50914] hover:bg-[#FDE8E9] rounded cursor-pointer" title="Tangguhkan Akun">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline" onsubmit="return confirm('Aktifkan kembali akun pengguna ini?')">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 text-[#137333] hover:bg-[#E6F4EA] rounded cursor-pointer" title="Aktifkan Akun">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        @can('delete', $user)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" data-confirm-delete data-delete-title="Hapus Pengguna Permanen?" data-delete-name="{{ $user->name }} ({{ $user->email }})">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-[#80868B] hover:text-[#D93025] hover:bg-[#FDE8E9] rounded cursor-pointer" title="Hapus Akun Permanen">
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

            <!-- Pagination Bar -->
            <div class="p-4 border-t border-[#E8E8E8]">
                {{ $users->links() }}
            </div>
        @else
            <!-- Empty state -->
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-[#A0A0A0] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="font-bold text-base text-[#111111] mb-1">Tidak Ada Data Pengguna</h3>
                <p class="text-xs text-[#5F6368] mb-4">Tidak ada data akun yang cocok dengan filter atau kata kunci pencarian.</p>
                <x-button variant="outline" size="sm" :href="route('admin.users.index')">
                    Reset Filter
                </x-button>
            </div>
        @endif
    </div>
</div>
@endsection
