@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header with Title & Date Range Filter -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-950 flex items-center gap-2.5">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Analitik & Performa Redaksi
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Data kinerja pembaca, agregat distribusi konten, dan statistik performa editorial portal.
            </p>
        </div>

        <!-- Date Range Filter Buttons -->
        <div class="flex flex-wrap items-center gap-2 bg-gray-100 p-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-600">
            @php
                $currentRoute = request()->route()->getName();
                $currentQuery = request()->except(['period', 'start_date', 'end_date', 'page']);
            @endphp

            <a href="{{ route($currentRoute, array_merge($currentQuery, ['period' => 'today'])) }}"
               class="px-3 py-1.5 rounded-md transition-colors {{ ($preset ?? '7days') === 'today' ? 'bg-white text-gray-950 font-semibold shadow-xs' : 'hover:text-gray-950' }}">
                Hari Ini
            </a>
            <a href="{{ route($currentRoute, array_merge($currentQuery, ['period' => '7days'])) }}"
               class="px-3 py-1.5 rounded-md transition-colors {{ ($preset ?? '7days') === '7days' ? 'bg-white text-gray-950 font-semibold shadow-xs' : 'hover:text-gray-950' }}">
                7 Hari
            </a>
            <a href="{{ route($currentRoute, array_merge($currentQuery, ['period' => '30days'])) }}"
               class="px-3 py-1.5 rounded-md transition-colors {{ ($preset ?? '7days') === '30days' ? 'bg-white text-gray-950 font-semibold shadow-xs' : 'hover:text-gray-950' }}">
                30 Hari
            </a>
            <a href="{{ route($currentRoute, array_merge($currentQuery, ['period' => '90days'])) }}"
               class="px-3 py-1.5 rounded-md transition-colors {{ ($preset ?? '7days') === '90days' ? 'bg-white text-gray-950 font-semibold shadow-xs' : 'hover:text-gray-950' }}">
                90 Hari
            </a>
        </div>
    </div>

    <!-- Active Filter Badge -->
    <div class="flex items-center justify-between text-xs text-gray-500 bg-gray-50 px-4 py-2.5 rounded-md border border-gray-200">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Rentang Waktu Aktif: <strong class="text-gray-800">{{ $range['label'] }}</strong></span>
            @if(!empty($isScoped))
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800">
                    Mode Penulis: Khusus Artikel Anda
                </span>
            @endif
        </div>
        <span class="text-gray-400">Data terenkripsi dan diagregasi secara berkala</span>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex space-x-6" aria-label="Tabs">
            <a href="{{ route('admin.analytics.overview', request()->query()) }}"
               class="whitespace-nowrap pb-3 px-1 border-b-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.analytics.overview') ? 'border-red-600 text-red-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Ringkasan Eksekutif
            </a>
            @if(auth()->user()->hasPermission('analytics.content'))
            <a href="{{ route('admin.analytics.content', request()->query()) }}"
               class="whitespace-nowrap pb-3 px-1 border-b-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.analytics.content') ? 'border-red-600 text-red-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Performa Konten & Penulis
            </a>
            @endif
            @if(auth()->user()->hasPermission('analytics.traffic'))
            <a href="{{ route('admin.analytics.traffic', request()->query()) }}"
               class="whitespace-nowrap pb-3 px-1 border-b-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.analytics.traffic') ? 'border-red-600 text-red-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Sumber Trafik & Perangkat
            </a>
            @endif
        </nav>
    </div>

    <!-- Tab View Content -->
    @yield('analytics_content')
</div>
@endsection
