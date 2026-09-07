@extends('layouts.admin')

@section('title', 'Detail Pengguna — ' . $user->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#E8E8E8] pb-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Detail Profil Pengguna
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Informasi rinci akun, identitas unik UUID, riwayat sesi login, dan catatan audit keamanan.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @can('update', $user)
                <x-button variant="primary" size="sm" :href="route('admin.users.edit', $user)">
                    Edit Pengguna
                </x-button>
            @endcan
            <x-button variant="outline" size="sm" :href="route('admin.users.index')">
                ← Kembali ke Daftar
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- User Info Card (Cols 1-5) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-[8px] border border-[#E8E8E8] shadow-subtle space-y-6">
            <div class="flex items-center gap-4 border-b border-[#F2F2F2] pb-6">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border border-[#E8E8E8]">
                @else
                    <div class="w-16 h-16 rounded-full bg-[#111111] text-white flex items-center justify-center font-bold text-xl">
                        {{ $user->initials }}
                    </div>
                @endif
                <div>
                    <h2 class="font-headline font-black text-xl text-[#111111]">{{ $user->name }}</h2>
                    <span class="text-xs text-[#80868B]">@ {{ $user->username }}</span>
                    <div class="flex items-center gap-2 mt-2">
                        @if ($user->primary_role)
                            <x-badge :variant="$user->isSuperAdmin() ? 'primary' : 'dark'" size="xs">
                                {{ $user->primary_role->label }}
                            </x-badge>
                        @endif
                        <x-badge :variant="$user->status->badgeVariant()" size="xs">
                            {{ $user->status->label() }}
                        </x-badge>
                    </div>
                </div>
            </div>

            <!-- Details List -->
            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1.5 border-b border-[#F2F2F2]">
                    <span class="text-[#80868B] font-medium">UUID:</span>
                    <span class="font-mono text-[#111111] select-all">{{ $user->uuid }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-[#F2F2F2]">
                    <span class="text-[#80868B] font-medium">Email:</span>
                    <span class="font-mono text-[#111111]">{{ $user->email }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-[#F2F2F2]">
                    <span class="text-[#80868B] font-medium">Telepon:</span>
                    <span class="text-[#111111]">{{ $user->phone ?? '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-[#F2F2F2]">
                    <span class="text-[#80868B] font-medium">Dibuat Oleh:</span>
                    <span class="text-[#111111]">{{ $user->creator?->name ?? 'Sistem / Mandiri' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-[#F2F2F2]">
                    <span class="text-[#80868B] font-medium">Tanggal Terdaftar:</span>
                    <span class="text-[#111111]">{{ $user->created_at->format('d F Y, H:i') }} WIB</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-[#F2F2F2]">
                    <span class="text-[#80868B] font-medium">Login Terakhir:</span>
                    <span class="text-[#111111]">{{ $user->last_login_at ? $user->last_login_at->format('d F Y, H:i') : 'Belum pernah' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-[#F2F2F2]">
                    <span class="text-[#80868B] font-medium">IP Terakhir:</span>
                    <span class="font-mono text-[#111111]">{{ $user->last_login_ip ?? '-' }}</span>
                </div>

                <div class="py-1.5">
                    <span class="text-[#80868B] font-medium block mb-1">User Agent Terakhir:</span>
                    <span class="font-mono text-[11px] text-[#5F6368] break-all block bg-[#F7F7F7] p-2 rounded border border-[#E8E8E8]">
                        {{ $user->last_login_user_agent ?? 'Tidak ada data' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- User Audit Logs (Cols 6-12) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-[8px] border border-[#E8E8E8] shadow-subtle space-y-4">
            <h2 class="font-headline font-bold text-sm uppercase tracking-wider text-[#111111] pb-2 border-b border-[#F2F2F2]">
                Riwayat Aktivitas & Audit Keamanan Pengguna
            </h2>

            @if ($auditLogs->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#E8E8E8] text-[#80868B] uppercase tracking-wider font-bold">
                                <th class="py-2.5">Waktu</th>
                                <th class="py-2.5">Aktivitas</th>
                                <th class="py-2.5">Deskripsi</th>
                                <th class="py-2.5">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F2F2F2]">
                            @foreach ($auditLogs as $log)
                                <tr>
                                    <td class="py-2.5 text-[#5F6368] whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-[#F2F2F2] text-[#111111]">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-[#5F6368]">{{ $log->description ?? '-' }}</td>
                                    <td class="py-2.5 text-[#80868B] font-mono text-[11px]">{{ $log->ip_address ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-[#80868B] py-8 text-center">Belum ada riwayat aktivitas keamanan untuk pengguna ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection
