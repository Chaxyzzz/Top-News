@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Standardized Page Header -->
    <x-admin.page-header 
        title="Dashboard Newsroom" 
        description="Ringkasan kendali sistem, aktivitas keamanan terkini, dan status operasional platform TopNews."
    >
        <x-slot name="actions">
            @can('create', App\Models\User::class)
                <x-button variant="primary" size="sm" :href="route('admin.users.create')">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>+ Tambah Pengguna</span>
                </x-button>
            @endcan

            <x-button variant="outline" size="sm" :href="route('home')" target="_blank" rel="noopener noreferrer">
                <span>Lihat Portal</span>
                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </x-button>
        </x-slot>
    </x-admin.page-header>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <x-admin.stat-card 
            label="Total Pengguna" 
            :value="$totalUsers" 
            helper="Akun staf terdaftar"
        />

        <x-admin.stat-card 
            label="Pengguna Aktif" 
            :value="$activeUsers" 
            variant="success" 
            helper="Dapat login ke sistem"
        />

        <x-admin.stat-card 
            label="Ditangguhkan" 
            :value="$suspendedUsers" 
            :variant="$suspendedUsers > 0 ? 'danger' : 'default'" 
            helper="Akses diblokir"
        />

        <x-admin.stat-card 
            label="Role Terdefinisi" 
            :value="$totalRoles" 
            helper="Struktur hak akses"
        />

        <x-admin.stat-card 
            label="Audit Hari Ini" 
            :value="$auditToday" 
            variant="primary" 
            helper="Catatan keamanan 24 jam"
        />

        <x-admin.stat-card 
            label="Login Pekan Ini" 
            :value="$recentLoginsCount" 
            helper="Sesi staf 7 hari terakhir"
        />
    </div>

    <!-- Main Content Grid: Quick Actions + Role Distribution + Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Column: Quick Actions & Role Distribution (Cols 1-4) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Quick Actions Hub -->
            <x-admin.card title="Aksi Cepat & Navigasi">
                <div class="space-y-2">
                    @can('create', App\Models\User::class)
                        <a href="{{ route('admin.users.create') }}" class="flex items-center justify-between p-3 rounded-[5px] border border-[#E5E7EB] hover:border-[#E50914] hover:bg-[#FFF1F2]/30 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-[#FFF1F2] text-[#E50914] flex items-center justify-center font-bold text-xs shrink-0">
                                    +
                                </div>
                                <div>
                                    <span class="font-bold text-xs text-[#171717] group-hover:text-[#E50914] block">Tambah Pengguna Baru</span>
                                    <span class="text-[11px] text-[#6B7280]">Registrasi akun redaksi/staf</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-[#9CA3AF] group-hover:text-[#E50914] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endcan

                    @if(Auth::user()->hasPermission('roles.view'))
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center justify-between p-3 rounded-[5px] border border-[#E5E7EB] hover:border-[#E50914] hover:bg-[#FFF1F2]/30 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-[#F3F4F6] text-[#4B5563] flex items-center justify-center font-bold text-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-bold text-xs text-[#171717] group-hover:text-[#E50914] block">Matriks Role & Izin</span>
                                    <span class="text-[11px] text-[#6B7280]">Lihat konfigurasi perizinan</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-[#9CA3AF] group-hover:text-[#E50914] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('audit.view'))
                        <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center justify-between p-3 rounded-[5px] border border-[#E5E7EB] hover:border-[#E50914] hover:bg-[#FFF1F2]/30 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-[#F3F4F6] text-[#4B5563] flex items-center justify-center font-bold text-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-bold text-xs text-[#171717] group-hover:text-[#E50914] block">Eksplor Catatan Audit</span>
                                    <span class="text-[11px] text-[#6B7280]">Riwayat log keamanan lengkap</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-[#9CA3AF] group-hover:text-[#E50914] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif

                    <a href="{{ route('admin.newsroom.index') }}" class="flex items-center justify-between p-3 rounded-[5px] border border-[#E5E7EB] hover:border-[#E50914] hover:bg-[#FFF1F2]/30 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded bg-[#F3F4F6] text-[#4B5563] flex items-center justify-center font-bold text-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-xs text-[#171717] group-hover:text-[#E50914] block">Alur Redaksi (Newsroom)</span>
                                <span class="text-[11px] text-[#6B7280]">Struktur pipeline editorial (Phase 04)</span>
                            </div>
                        </div>
                        <span class="text-[10px] bg-[#F3F4F6] text-[#6B7280] px-1.5 py-0.5 rounded font-semibold uppercase">P04</span>
                    </a>
                </div>
            </x-admin.card>

            <!-- Staff Role Distribution -->
            <x-admin.card title="Distribusi Role Staf" subtitle="Jumlah akun per level otoritas">
                <div class="space-y-3">
                    @foreach ($roleDistribution as $role)
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-[#F3F4F6] last:border-0">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $role->name === 'super_admin' ? 'bg-[#E50914]' : 'bg-[#4B5563]' }}"></span>
                                <span class="font-bold text-[#171717]">{{ $role->label }}</span>
                            </div>
                            <span class="font-mono text-[#6B7280] font-semibold bg-[#F3F4F6] px-2 py-0.5 rounded-[3px]">
                                {{ $role->users_count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>

            <!-- Safe System Diagnostic Summary -->
            <x-admin.card title="Status Platform & Environment">
                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-[#F3F4F6]">
                        <span class="text-[#6B7280]">Environment:</span>
                        <span class="font-bold uppercase text-[#171717]">{{ $systemStatus['app_env'] }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-[#F3F4F6]">
                        <span class="text-[#6B7280]">Koneksi Database:</span>
                        @if ($systemStatus['db_connected'])
                            <x-admin.badge variant="success" size="xs">Terkoneksi (OK)</x-admin.badge>
                        @else
                            <x-admin.badge variant="danger" size="xs">Gagal</x-admin.badge>
                        @endif
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-[#F3F4F6]">
                        <span class="text-[#6B7280]">Laravel / PHP:</span>
                        <span class="font-mono text-[#171717] text-[11px]">{{ $systemStatus['laravel_version'] }} / PHP {{ $systemStatus['php_version'] }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-[#F3F4F6]">
                        <span class="text-[#6B7280]">Storage Public Link:</span>
                        @if ($systemStatus['storage_linked'])
                            <span class="text-[#137333] font-bold">✓ Tersambung</span>
                        @else
                            <span class="text-[#C8102E] font-bold">✕ Belum tersambung</span>
                        @endif
                    </div>
                </div>

                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasRole('admin'))
                    <div class="mt-4 pt-3 border-t border-[#F3F4F6] text-right">
                        <a href="{{ route('admin.system.index') }}" class="text-xs text-[#E50914] hover:text-[#C8102E] font-bold transition-colors">
                            Lihat Diagnostik Lengkap →
                        </a>
                    </div>
                @endif
            </x-admin.card>
        </div>

        <!-- Right Column: Recent Activity Feed (Cols 5-12) -->
        <div class="lg:col-span-8">
            <x-admin.card title="Aktivitas & Catatan Keamanan Terkini" subtitle="8 peristiwa keamanan dan perubahan sistem terbaru">
                <x-slot name="headerActions">
                    @if(Auth::user()->hasPermission('audit.view'))
                        <a href="{{ route('admin.audit-logs.index') }}" class="text-xs text-[#E50914] hover:text-[#C8102E] font-bold transition-colors">
                            Lihat Semua Log →
                        </a>
                    @endif
                </x-slot>

                @if ($recentActivities->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-[#E5E7EB] text-[#6B7280] uppercase tracking-wider font-bold">
                                    <th class="py-2.5">Waktu</th>
                                    <th class="py-2.5">Pelaksana</th>
                                    <th class="py-2.5">Aksi</th>
                                    <th class="py-2.5">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F3F4F6]">
                                @foreach ($recentActivities as $activity)
                                    <tr class="hover:bg-[#F9FAFB] transition-colors">
                                        <td class="py-3 text-[#6B7280] whitespace-nowrap" title="{{ $activity->created_at->format('d F Y, H:i:s') }} WIB">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </td>
                                        <td class="py-3 font-semibold text-[#171717] whitespace-nowrap">
                                            {{ $activity->user?->name ?? 'Sistem / CLI' }}
                                        </td>
                                        <td class="py-3 whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-[#F3F4F6] text-[#171717] border border-[#E5E7EB]">
                                                {{ $activity->action }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-[#4B5563] max-w-sm truncate">
                                            {{ $activity->description ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-admin.empty-state 
                        title="Belum Ada Catatan Aktivitas" 
                        description="Seluruh aktivitas autentikasi dan modifikasi pengguna akan terekam secara otomatis di sini."
                    />
                @endif
            </x-admin.card>
        </div>
    </div>
</div>
@endsection
