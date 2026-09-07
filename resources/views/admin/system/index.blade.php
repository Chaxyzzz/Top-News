@extends('layouts.admin')

@section('title', 'Diagnostik Sistem')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Diagnostik & Informasi Sistem" 
        description="Ringkasan status environment framework Laravel, versi PHP, konfigurasi driver, dan integritas infrastruktur sistem TopNews."
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Diagnostik Sistem']
            ]" />
        </x-slot>
    </x-admin.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- App & Framework -->
        <x-admin.card title="Aplikasi & Framework">
            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Nama Platform:</span>
                    <span class="font-bold text-[#171717]">{{ $systemDetails['app_name'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Versi Laravel:</span>
                    <span class="font-mono font-bold text-[#171717]">{{ $systemDetails['laravel_version'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Versi PHP:</span>
                    <span class="font-mono font-bold text-[#171717]">{{ $systemDetails['php_version'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Environment:</span>
                    <span class="font-bold uppercase text-[#171717]">{{ $systemDetails['environment'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Debug Mode:</span>
                    <span class="font-semibold text-[#171717]">{{ $systemDetails['debug_mode'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Timezone:</span>
                    <span class="font-mono text-[#171717]">{{ $systemDetails['timezone'] }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-[#6B7280]">Locale Default:</span>
                    <span class="font-mono text-[#171717]">{{ $systemDetails['locale'] }}</span>
                </div>
            </div>
        </x-admin.card>

        <!-- Database & Drivers -->
        <x-admin.card title="Basis Data & Drivers">
            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Driver Database:</span>
                    <span class="font-mono font-bold uppercase text-[#171717]">{{ $systemDetails['database_driver'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Status Koneksi:</span>
                    <x-admin.badge variant="success" size="xs">{{ $systemDetails['database_status'] }}</x-admin.badge>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Versi Server DB:</span>
                    <span class="font-mono text-[#171717]">{{ $systemDetails['database_version'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Cache Driver:</span>
                    <span class="font-mono text-[#171717]">{{ $systemDetails['cache_driver'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Session Driver:</span>
                    <span class="font-mono text-[#171717]">{{ $systemDetails['session_driver'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#6B7280]">Queue Driver:</span>
                    <span class="font-mono text-[#171717]">{{ $systemDetails['queue_driver'] }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-[#6B7280]">Storage Symlink:</span>
                    <span class="font-semibold text-[#137333]">{{ $systemDetails['storage_symlink'] }}</span>
                </div>
            </div>
        </x-admin.card>
    </div>
</div>
@endsection
