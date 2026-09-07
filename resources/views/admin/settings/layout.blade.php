@extends('layouts.admin')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold font-headline text-gray-900">Pengaturan Situs</h1>
        <p class="text-sm text-gray-500 mt-1">Konfigurasi identitas media, kontak publik, jejaring sosial, dan standar redaksi TopNews.</p>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Settings Navigation Tabs -->
    <div class="border-b border-gray-200 bg-white rounded-t-lg px-4 shadow-sm">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            <a href="{{ route('admin.settings.general') }}" class="{{ request()->routeIs('admin.settings.general') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Umum & Identitas
            </a>
            <a href="{{ route('admin.settings.branding') }}" class="{{ request()->routeIs('admin.settings.branding') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Branding & Logo
            </a>
            <a href="{{ route('admin.settings.contact') }}" class="{{ request()->routeIs('admin.settings.contact') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Kontak & Alamat
            </a>
            <a href="{{ route('admin.settings.social') }}" class="{{ request()->routeIs('admin.settings.social') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Media Sosial
            </a>
            <a href="{{ route('admin.settings.footer') }}" class="{{ request()->routeIs('admin.settings.footer') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Footer Situs
            </a>
            <a href="{{ route('admin.settings.editorial') }}" class="{{ request()->routeIs('admin.settings.editorial') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Standar Redaksi
            </a>
            <a href="{{ route('admin.settings.seo') }}" class="{{ request()->routeIs('admin.settings.seo') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                SEO & Metadata
            </a>
            <a href="{{ route('admin.settings.newsletter') }}" class="{{ request()->routeIs('admin.settings.newsletter') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Buletin Newsletter
            </a>
        </nav>
    </div>

    <!-- Tab Content -->
    <div>
        @yield('settings-content')
    </div>
</div>
@endsection
