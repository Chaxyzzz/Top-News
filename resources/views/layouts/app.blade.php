<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteSettings = app(\App\Services\SettingsService::class);
        $faviconMedia = $siteSettings->getMedia('branding.favicon_media_id');
    @endphp

    <x-seo-head :seo="$seoData ?? null" />

    <!-- Favicon -->
    @if($faviconMedia && $faviconMedia->url)
        <link rel="icon" href="{{ $faviconMedia->url }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Google Fonts / Instrument Sans -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800|merriweather:400,700" rel="stylesheet" />

    <!-- Scripts and CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-white text-[#111111] antialiased selection:bg-[#E50914] selection:text-white">
    <!-- Accessible Skip to Content Link -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-[#E50914] focus:text-white focus:font-bold focus:rounded">
        Lewati ke Konten Utama
    </a>

    <!-- Global Header -->
    @include('layouts.partials.header')

    <!-- Search Modal -->
    @include('layouts.partials.search-modal')

    <!-- Main Content Area -->
    <main id="main-content" class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Global Footer -->
    @include('layouts.partials.footer')

    <!-- Flash Messages / Toast Feedback -->
    @if (session('success'))
        <x-toast type="success" :message="session('success')" />
    @elseif (session('error'))
        <x-toast type="error" :message="session('error')" />
    @elseif (session('warning'))
        <x-toast type="warning" :message="session('warning')" />
    @elseif (session('info') || session('status'))
        <x-toast type="info" :message="session('info') ?? session('status')" />
    @endif

    @stack('scripts')
</body>
</html>
