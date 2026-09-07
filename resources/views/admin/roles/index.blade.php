@extends('layouts.admin')

@section('title', 'Matriks Role & Izin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#E8E8E8] pb-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Role & Matriks Izin Sistem
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Struktur Role-Based Access Control (RBAC) dan pemetaan hak akses resmi TopNews.
            </p>
        </div>
    </div>

    <!-- Roles Overview Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($roles as $role)
            <div class="bg-white p-5 rounded-[8px] border border-[#E8E8E8] shadow-subtle flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <x-badge :variant="$role->name === 'super_admin' ? 'primary' : 'dark'" size="xs">
                            {{ $role->label }}
                        </x-badge>
                        @if ($role->is_system)
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-[#80868B] bg-[#F2F2F2] px-1.5 py-0.5 rounded">
                                Role Sistem
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-[#5F6368] leading-relaxed line-clamp-2">
                        {{ $role->description ?? 'Tidak ada deskripsi khusus.' }}
                    </p>
                </div>

                <div class="pt-3 border-t border-[#F2F2F2] flex items-center justify-between text-xs">
                    <span class="text-[#80868B] font-medium">
                        {{ $role->users->count() }} pengguna
                    </span>

                    @if(Auth::user()->hasPermission('roles.manage'))
                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-[#E50914] hover:text-[#C8102E] font-bold transition-colors">
                            Atur Izin →
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Derived Dynamic Permission Matrix Table -->
    <div class="bg-white rounded-[8px] border border-[#E8E8E8] shadow-subtle overflow-hidden space-y-4 p-6">
        <div class="border-b border-[#F2F2F2] pb-4">
            <h2 class="font-headline font-black text-lg text-[#111111] tracking-tight">
                Matriks Hak Akses Nyata (Permission Matrix)
            </h2>
            <p class="text-xs text-[#5F6368] mt-1">
                Tabel di bawah ini ditarik langsung dari data basis data hak akses saat ini.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#F7F7F7] border-b border-[#E8E8E8] text-[#111111] font-headline font-bold">
                        <th class="py-3 px-4 w-1/3">Kategori & Nama Izin</th>
                        @foreach ($roles as $role)
                            <th class="py-3 px-2 text-center whitespace-nowrap">
                                {{ $role->label }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2F2F2]">
                    @foreach ($permissions as $group => $perms)
                        <tr class="bg-[#FBFBFB]">
                            <td colspan="{{ $roles->count() + 1 }}" class="py-2 px-4 font-black uppercase tracking-wider text-[10px] text-[#E50914] bg-[#FDE8E9]/40">
                                Grup: {{ strtoupper($group) }}
                            </td>
                        </tr>
                        @foreach ($perms as $perm)
                            <tr class="hover:bg-[#F9F9F9] transition-colors">
                                <td class="py-2.5 px-4">
                                    <span class="font-bold text-[#111111] block">{{ $perm->label }}</span>
                                    <span class="text-[10px] font-mono text-[#80868B]">{{ $perm->name }}</span>
                                </td>
                                @foreach ($roles as $role)
                                    <td class="py-2.5 px-2 text-center">
                                        @if ($role->name === 'super_admin' || $role->hasPermission($perm->name))
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#E6F4EA] text-[#137333] font-bold text-xs" title="Diizinkan">
                                                ✓
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#F2F2F2] text-[#CCCCCC] font-bold text-xs" title="Dilarang">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
