@extends('layouts.admin')

@section('title', 'Catatan Audit & Keamanan')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Catatan Audit & Keamanan Sistem" 
        description="Riwayat lengkap log aktivitas operasional staf, perubahan status pengguna, mutasi hak akses, dan sesi autentikasi."
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Catatan Audit Log']
            ]" />
        </x-slot>
    </x-admin.page-header>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-[6px] border border-[#E5E7EB] shadow-subtle">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6">
                <input 
                    type="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari deskripsi, aksi, atau nama pelaksana..." 
                    class="w-full text-xs border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                />
            </div>

            <div class="sm:col-span-4">
                <select name="action" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:outline-none focus:border-[#E50914] bg-white">
                    <option value="">Semua Tipe Aksi</option>
                    @foreach ($availableActions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ $act }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <x-button type="submit" variant="secondary" size="sm" class="w-full">
                    Filter
                </x-button>
                @if (request()->hasAny(['search', 'action']))
                    <a href="{{ route('admin.audit-logs.index') }}" class="px-2.5 py-1.5 text-xs text-[#6B7280] hover:text-[#171717] bg-[#F3F4F6] rounded flex items-center justify-center" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-subtle overflow-hidden">
        @if ($logs->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#6B7280] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3 px-4">Waktu (WIB)</th>
                            <th class="py-3 px-4">Pelaksana</th>
                            <th class="py-3 px-4">Aksi / Event</th>
                            <th class="py-3 px-4">Deskripsi Aktivitas</th>
                            <th class="py-3 px-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F3F4F6]">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-[#F9FAFB] transition-colors">
                                <td class="py-3 px-4 text-[#6B7280] whitespace-nowrap">
                                    <span class="font-semibold text-[#171717] block">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                    <span class="text-[11px] text-[#9CA3AF]">{{ $log->created_at->diffForHumans() }}</span>
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($log->user)
                                        <a href="{{ route('admin.users.show', $log->user) }}" class="font-bold text-[#171717] hover:text-[#E50914] transition-colors">
                                            {{ $log->user->name }}
                                        </a>
                                        <span class="block text-[11px] text-[#9CA3AF] font-mono">{{ $log->user->email }}</span>
                                    @else
                                        <span class="text-[#6B7280] font-semibold italic">Sistem / CLI / Tamu</span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded text-[11px] font-mono bg-[#F3F4F6] text-[#171717] border border-[#E5E7EB]">
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 text-[#4B5563] max-w-md">
                                    <span>{{ $log->description ?? '-' }}</span>
                                    @if ($log->metadata)
                                        <details class="mt-1">
                                            <summary class="text-[11px] text-[#E50914] cursor-pointer hover:underline font-semibold">Lihat Metadata</summary>
                                            <pre class="mt-1 p-2 bg-[#F9FAFB] rounded border border-[#E5E7EB] text-[10px] font-mono text-[#171717] overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </details>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-[#6B7280] font-mono text-[11px] whitespace-nowrap">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[#E5E7EB]">
                {{ $logs->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="Tidak Ada Catatan Log Ditemukan" 
                description="Tidak ada catatan audit yang cocok dengan filter atau kata kunci pencarian yang Anda masukkan."
                actionLabel="Reset Filter"
                :actionUrl="route('admin.audit-logs.index')"
            />
        @endif
    </div>
</div>
@endsection
