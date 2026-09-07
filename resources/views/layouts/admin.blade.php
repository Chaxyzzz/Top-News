<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F7F7F8]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') — TopNews Newsroom</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full flex flex-col bg-[#F7F7F8] text-[#171717] antialiased">
    <!-- Desktop Sidebar & Main Content Wrapper -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Desktop Sidebar -->
        <aside id="admin-sidebar" class="hidden lg:flex lg:flex-col w-[260px] bg-white border-r border-[#E5E7EB] shrink-0 transition-all duration-200 z-30 select-none">
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-[#E5E7EB]">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <x-logo size="sm" />
                    <span class="inline-block px-1.5 py-0.5 bg-[#171717] text-white text-[9px] font-black uppercase tracking-wider rounded-[2px]">
                        NEWSROOM
                    </span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                <!-- Group: Utama -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">
                        Utama
                    </span>

                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Group: CMS & Redaksi -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">
                        Redaksi & CMS
                    </span>

                    <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.articles.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span>Artikel & Berita</span>
                    </a>

                    <a href="{{ route('admin.newsroom.index') }}" class="flex items-center justify-between px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.newsroom.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Alur Redaksi (Queue)</span>
                        </div>
                    </a>

                    @if(Auth::user()->hasPermission('breaking_news.view'))
                        <a href="{{ route('admin.breaking-news.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.breaking-news.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Breaking News</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('homepage.view'))
                        <a href="{{ route('admin.homepage.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.homepage.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                            <span>Homepage CMS</span>
                        </a>
                    @endif

                    @can('viewAny', App\Models\Category::class)
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>Kategori & Rubrik</span>
                        </a>
                    @endcan

                    @can('viewAny', App\Models\Tag::class)
                        <a href="{{ route('admin.tags.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.tags.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span>Tagar Berita (Tags)</span>
                        </a>
                    @endcan

                    @can('viewAny', App\Models\Media::class)
                        <a href="{{ route('admin.media.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.media.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Pustaka Media</span>
                        </a>
                    @endcan

                    @can('viewAny', App\Models\Gallery::class)
                        <a href="{{ route('admin.galleries.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.galleries.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Galeri Foto</span>
                        </a>
                    @endcan
                </div>

                <!-- Group: Interaksi & Pembaca (Phase 08) -->
                @if(Auth::user()->hasPermission('comments.view'))
                    @php
                        $pendingCommentsCount = \Illuminate\Support\Facades\DB::table('comments')->where('status', 'pending')->whereNull('deleted_at')->count();
                    @endphp
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">
                            Interaksi & Pembaca
                        </span>

                        <a href="{{ route('admin.comments.index') }}" class="flex items-center justify-between px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.comments.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <span>Komentar</span>
                            </div>
                            @if($pendingCommentsCount > 0)
                                <span class="px-1.5 py-0.5 text-[10px] font-black bg-amber-500 text-white rounded-full">
                                    {{ $pendingCommentsCount }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('admin.engagement.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.engagement.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Ringkasan Interaksi</span>
                        </a>
                    </div>
                @endif

                <!-- Group: Analitik & Performa (Phase 11) -->
                @if(Auth::user()->hasPermission('analytics.view'))
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">
                            Analitik & Performa
                        </span>

                        <a href="{{ route('admin.analytics.overview') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.analytics.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Analitik Redaksi</span>
                        </a>
                    </div>
                @endif

                <!-- Group: Periklanan & Komersial (Phase 09) -->
                @if(Auth::user()->hasPermission('ads.view'))
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">
                            Periklanan & Komersial
                        </span>

                        <a href="{{ route('admin.advertising.overview') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.advertising.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            <span>Manajemen Periklanan</span>
                        </a>
                    </div>
                @endif

                <!-- Group: Pengguna & Hak Akses -->
                @can('viewAny', App\Models\User::class)
                    <div class="space-y-1">
                        <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">
                            Pengguna & Akses
                        </span>

                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Pengguna / Staf</span>
                        </a>

                        @if(Auth::user()->hasPermission('roles.view'))
                            <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>Matriks Role & Izin</span>
                            </a>
                        @endif
                    </div>
                @endcan

                <!-- Group: Halaman & Komunikasi (Phase 10) -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">
                        Halaman & Komunikasi
                    </span>

                    @can('viewAny', App\Models\Page::class)
                        <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.pages.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Halaman Statis</span>
                        </a>
                    @endcan

                    @if(Auth::user()->hasPermission('editorial_team.manage'))
                        <a href="{{ route('admin.editorial-team.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.editorial-team.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Dewan Redaksi Publik</span>
                        </a>
                    @endif

                    @can('viewAny', App\Models\NewsletterSubscriber::class)
                        <a href="{{ route('admin.newsletter.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.newsletter.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Pelanggan Buletin</span>
                        </a>
                    @endcan

                    @can('viewAny', App\Models\ContactMessage::class)
                        <a href="{{ route('admin.contacts.index') }}" class="flex items-center justify-between px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.contacts.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span>Kotak Masuk Pesan</span>
                            </div>
                            @if($newContactsCount > 0)
                                <span class="bg-[#E50914] text-white text-[10px] font-black px-1.5 py-0.5 rounded-full">
                                    {{ $newContactsCount }}
                                </span>
                            @endif
                        </a>
                    @endcan
                </div>

                <!-- Group: Sistem & Keamanan -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">
                        Sistem & Keamanan
                    </span>

                    @if(Auth::user()->hasPermission('navigation.view'))
                        <a href="{{ route('admin.navigation.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.navigation.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                            <span>Navigasi Menu</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('audit.view'))
                        <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>Catatan Audit Log</span>
                        </a>
                    @endif

                    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasRole('admin'))
                        <a href="{{ route('admin.system.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.system.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Diagnostik Sistem</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('settings.view'))
                        <a href="{{ route('admin.settings.general') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-[5px] text-xs font-bold font-headline transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6]' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            <span>Pengaturan Situs</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Sidebar User Mini Profile -->
            <div class="p-3 border-t border-[#E5E7EB] bg-[#F7F7F8]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        @if (Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-[#E5E7EB] shrink-0">
                        @else
                            <div class="w-8 h-8 rounded-full bg-[#171717] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                {{ Auth::user()->initials }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <span class="block text-xs font-bold text-[#171717] truncate">{{ Auth::user()->name }}</span>
                            <span class="block text-[10px] text-[#6B7280] font-semibold truncate">{{ Auth::user()->primary_role?->label ?? 'Staf' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right / Main Content Column -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-[#E5E7EB] sticky top-0 z-20 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger Button -->
                    <button id="admin-mobile-toggle" type="button" class="lg:hidden p-2 text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6] rounded-[4px] cursor-pointer" aria-label="Buka Menu Sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Breadcrumbs Context -->
                    <div class="hidden sm:block text-xs text-[#6B7280]">
                        <span class="font-bold text-[#171717]">TopNews Control Center</span>
                    </div>
                </div>

                <!-- Topbar Actions -->
                <div class="flex items-center gap-3">
                    <!-- Admin Notification Center -->
                    <div class="relative" id="admin-notification-container">
                        @php
                            $unreadCount = Auth::user()?->unreadNotifications()->count() ?? 0;
                            $latestNotifications = Auth::user()?->unreadNotifications()->take(5)->get() ?? collect();
                        @endphp
                        <button type="button" 
                                id="admin-notification-bell" 
                                class="relative p-2 text-[#6B7280] hover:text-[#111111] hover:bg-[#F3F4F6] rounded-[6px] transition-colors cursor-pointer focus:outline-none" 
                                title="Pusat Notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="admin-notification-badge" class="absolute -top-1 -right-1 bg-[#E50914] text-white text-[9px] font-black px-1.5 py-0.2 rounded-full min-w-[18px] text-center {{ $unreadCount > 0 ? '' : 'hidden' }}">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        </button>

                        <!-- Notification Dropdown Panel -->
                        <div id="admin-notification-dropdown" 
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-[#E8E8E8] rounded-[8px] shadow-xl hidden z-50 overflow-hidden divide-y divide-[#E8E8E8]">
                            <div class="px-4 py-3 bg-[#F8F9FA] flex items-center justify-between">
                                <h4 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Notifikasi</h4>
                                <div class="flex items-center gap-2">
                                    @if($unreadCount > 0)
                                        <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-[11px] font-bold text-[#E50914] hover:underline cursor-pointer">
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.notifications.index') }}" class="text-[11px] font-bold text-[#555555] hover:text-[#111111] underline">
                                        Lihat Semua
                                    </a>
                                </div>
                            </div>
                            <div id="admin-notification-list" class="max-h-80 overflow-y-auto divide-y divide-[#F0F0F0]">
                                @forelse($latestNotifications as $n)
                                    <a href="{{ route('admin.notifications.read', $n->id) }}" class="p-3 block hover:bg-[#FFF9F9] transition-colors">
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-2 h-2 rounded-full bg-[#E50914] mt-1.5 shrink-0"></span>
                                            <div class="flex-1 min-w-0">
                                                <h5 class="text-xs font-bold text-[#111111] truncate">{{ $n->data['title'] ?? 'Notifikasi' }}</h5>
                                                <p class="text-[11px] text-[#555555] line-clamp-2 mt-0.5 leading-snug">{{ $n->data['message'] ?? '' }}</p>
                                                <span class="text-[10px] text-[#888888] mt-1 block">{{ $n->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-6 text-center text-xs text-[#777777]">
                                        Belum ada notifikasi baru.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- View Live Portal -->
                    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4B5563] hover:text-[#171717] bg-[#F7F7F8] hover:bg-[#F3F4F6] px-3 py-1.5 rounded-[4px] border border-[#E5E7EB] transition-colors">
                        <span>Portal Publik</span>
                        <svg class="w-3.5 h-3.5 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>

                    <!-- User Account Action / Logout -->
                    <div class="flex items-center gap-2 pl-3 border-l border-[#E5E7EB]">
                        <a href="{{ route('profile.edit') }}" class="p-1.5 text-[#6B7280] hover:text-[#171717] hover:bg-[#F3F4F6] rounded transition-colors" title="Pengaturan Akun & Profil">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-1.5 text-[#6B7280] hover:text-[#E50914] hover:bg-[#FFF1F2] rounded transition-colors cursor-pointer" title="Keluar dari Sistem">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content Viewport -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-[1600px] w-full mx-auto">
                <!-- Toast Flash Messages -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-[6px] bg-[#E6F4EA] border border-[#137333] text-xs text-[#137333] flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#137333] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @elseif (session('error'))
                    <div class="mb-6 p-4 rounded-[6px] bg-[#FDE8E9] border border-[#E50914] text-xs text-[#C8102E] flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#E50914] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @elseif (session('warning'))
                    <div class="mb-6 p-4 rounded-[6px] bg-[#FFF8E1] border border-[#B78103] text-xs text-[#B78103] flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#B78103] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ session('warning') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Admin Minimal Footer -->
            <footer class="bg-white border-t border-[#E5E7EB] py-3.5 px-4 sm:px-6 lg:px-8 text-xs text-[#6B7280]">
                <div class="max-w-[1600px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                    <span>TopNews Control Center & Newsroom Administration — Phase 03.</span>
                    <span class="text-[11px] text-[#9CA3AF]">Versi Framework: Laravel {{ app()->version() }} | PHP {{ PHP_VERSION }}</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Sidebar Drawer & Backdrop -->
    <div id="admin-mobile-backdrop" class="fixed inset-0 bg-black/60 z-40 hidden opacity-0 transition-opacity duration-300 backdrop-blur-xs" aria-hidden="true"></div>
    <aside id="admin-mobile-drawer" class="fixed top-0 left-0 bottom-0 w-[280px] bg-white z-50 -translate-x-full transition-transform duration-300 shadow-2xl flex flex-col select-none" aria-label="Menu Sidebar Mobile">
        <div class="h-16 p-4 border-b border-[#E5E7EB] flex items-center justify-between">
            <x-logo size="sm" />
            <button id="admin-mobile-close" type="button" class="p-1.5 text-[#6B7280] hover:text-[#171717] rounded cursor-pointer" aria-label="Tutup Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <div class="space-y-1 font-headline font-bold text-xs">
                <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">Utama</span>
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Dashboard</a>
            </div>

            <div class="space-y-1 font-headline font-bold text-xs">
                <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">Redaksi & CMS</span>
                <a href="{{ route('admin.articles.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.articles.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Artikel & Berita</a>
                <a href="{{ route('admin.newsroom.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.newsroom.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Alur Redaksi (Queue)</a>
                @if(Auth::user()->hasPermission('breaking_news.view'))
                    <a href="{{ route('admin.breaking-news.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.breaking-news.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Breaking News</a>
                @endif
                @if(Auth::user()->hasPermission('homepage.view'))
                    <a href="{{ route('admin.homepage.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.homepage.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Homepage CMS</a>
                @endif
                @can('viewAny', App\Models\Category::class)
                    <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.categories.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Kategori & Rubrik</a>
                @endcan
                @can('viewAny', App\Models\Tag::class)
                    <a href="{{ route('admin.tags.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.tags.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Tagar Berita (Tags)</a>
                @endcan
                @can('viewAny', App\Models\Media::class)
                    <a href="{{ route('admin.media.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.media.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Pustaka Media</a>
                @endcan
                @can('viewAny', App\Models\Gallery::class)
                    <a href="{{ route('admin.galleries.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.galleries.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Galeri Foto</a>
                @endcan
            </div>

            @if(Auth::user()->hasPermission('ads.view'))
                <div class="space-y-1 font-headline font-bold text-xs">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">Periklanan</span>
                    <a href="{{ route('admin.advertising.overview') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.advertising.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Manajemen Iklan</a>
                </div>
            @endif

            @if(Auth::user()->hasPermission('comments.view'))
                <div class="space-y-1 font-headline font-bold text-xs">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">Interaksi & Pembaca</span>
                    <a href="{{ route('admin.comments.index') }}" class="flex items-center justify-between px-3 py-2 rounded {{ request()->routeIs('admin.comments.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">
                        <span>Komentar</span>
                        @if($pendingCommentsCount > 0)
                            <span class="px-1.5 py-0.5 text-[10px] font-black bg-amber-500 text-white rounded-full">
                                {{ $pendingCommentsCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.engagement.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.engagement.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Ringkasan Interaksi</a>
                </div>
            @endif

            @if(Auth::user()->hasPermission('analytics.view'))
                <div class="space-y-1 font-headline font-bold text-xs">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">Analitik & Performa</span>
                    <a href="{{ route('admin.analytics.overview') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.analytics.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Dashboard Analitik</a>
                </div>
            @endif

            @can('viewAny', App\Models\User::class)
                <div class="space-y-1 font-headline font-bold text-xs">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">Pengguna & Akses</span>
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Pengguna / Staf</a>
                    @if(Auth::user()->hasPermission('roles.view'))
                        <a href="{{ route('admin.roles.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.roles.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Matriks Role & Izin</a>
                    @endif
                </div>
            @endcan

            @can('viewAny', App\Models\Page::class)
                <div class="space-y-1 font-headline font-bold text-xs">
                    <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">Halaman & Komunikasi</span>
                    <a href="{{ route('admin.pages.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.pages.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Halaman Statis</a>
                    @if(Auth::user()->hasPermission('editorial_team.manage'))
                        <a href="{{ route('admin.editorial-team.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.editorial-team.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Dewan Redaksi</a>
                    @endif
                    @can('viewAny', App\Models\NewsletterSubscriber::class)
                        <a href="{{ route('admin.newsletter.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.newsletter.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Pelanggan Buletin</a>
                    @endcan
                    @can('viewAny', App\Models\ContactMessage::class)
                        <a href="{{ route('admin.contacts.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.contacts.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Kotak Masuk Pesan</a>
                    @endcan
                </div>
            @endcan

            <div class="space-y-1 font-headline font-bold text-xs">
                <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-[#9CA3AF] block mb-1">Sistem</span>
                @if(Auth::user()->hasPermission('navigation.view'))
                    <a href="{{ route('admin.navigation.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.navigation.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Navigasi Menu</a>
                @endif
                @if(Auth::user()->hasPermission('audit.view'))
                    <a href="{{ route('admin.audit-logs.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Catatan Audit</a>
                @endif
                @if(Auth::user()->isSuperAdmin() || Auth::user()->hasRole('admin'))
                    <a href="{{ route('admin.system.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.system.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Diagnostik Sistem</a>
                @endif
                @if(Auth::user()->hasPermission('settings.view'))
                    <a href="{{ route('admin.settings.general') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.settings.*') ? 'bg-[#FFF1F2] text-[#E50914]' : 'text-[#4B5563]' }}">Pengaturan Situs</a>
                @endif
            </div>
        </div>
    </aside>

    <!-- TopNews Reusable Delete Modal -->
    <x-admin.delete-modal />

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Topbar Notification Dropdown Toggle
        const bellBtn = document.getElementById('admin-notification-bell');
        const dropdown = document.getElementById('admin-notification-dropdown');

        if (bellBtn && dropdown) {
            bellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
