@php
    $navService = app(\App\Services\NavigationService::class);
    $primaryMenu = $navService->getMenu('primary');

    $navCategories = $navService->getNavigationCategories();

    // If database has no categories yet, fallback to config navigation
    if ($navCategories->isEmpty()) {
        $fallbackConfig = config('topnews.navigation', []);
    } else {
        $fallbackConfig = null;
    }

    $currentDate = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');
@endphp

<header class="w-full bg-white border-b border-[#E8E8E8] sticky top-0 z-40">
    <!-- Top Information Bar -->
    <div class="hidden lg:block bg-[#F7F7F7] border-b border-[#E8E8E8] text-xs text-[#5F6368] py-1.5">
        <div class="tn-container flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="font-medium text-[#111111] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#E50914]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $currentDate }}
                </span>
                <span class="text-[#E8E8E8]">|</span>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-[#111111] text-[11px] uppercase tracking-wider">Topik Pilihan:</span>
                    <a href="{{ route('trending.index') }}" class="hover:text-[#E50914] transition-colors flex items-center gap-1">
                        <span>🔥 Trending</span>
                    </a>
                    <span class="text-[#E8E8E8]">•</span>
                    <a href="{{ route('popular.index') }}" class="hover:text-[#E50914] transition-colors">Terpopuler</a>
                    <span class="text-[#E8E8E8]">•</span>
                    <a href="{{ route('opinion.index') }}" class="hover:text-[#E50914] transition-colors">Opini</a>
                    <span class="text-[#E8E8E8]">•</span>
                    <a href="{{ route('editors-choice.index') }}" class="hover:text-[#E50914] transition-colors">Pilihan Editor</a>
                </div>
            </div>

            <div class="flex items-center gap-4 font-medium">
                <a href="{{ route('about') }}" class="hover:text-[#111111] transition-colors">Tentang Kami</a>
                <a href="{{ route('contact') }}" class="hover:text-[#111111] transition-colors">Kontak Redaksi</a>
                <a href="{{ route('latest') }}" class="hover:text-[#111111] transition-colors">Arsip Berita</a>
            </div>
        </div>
    </div>

    <!-- Main Header Bar -->
    <div class="tn-container py-3 md:py-4 flex items-center justify-between gap-4">
        <!-- Left: Mobile Menu Toggle -->
        <div class="flex items-center gap-2 lg:hidden">
            <button id="tn-mobile-toggle" type="button" class="p-2 text-[#111111] hover:bg-[#F2F2F2] rounded-[4px] focus:outline-none cursor-pointer" aria-label="Buka Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Center / Brand Logo -->
        <div class="flex items-center gap-6">
            <x-logo size="md" />
            <span class="hidden xl:inline-block text-xs text-[#5F6368] font-normal border-l border-[#E8E8E8] pl-4 max-w-[200px] leading-tight">
                {{ config('topnews.tagline_id') }}
            </span>
        </div>

        <!-- Right: Search trigger & Actions -->
        <div class="flex items-center gap-2 md:gap-3">
            <!-- Search Trigger Button -->
            <button id="tn-search-trigger" type="button" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[#5F6368] bg-[#F7F7F7] hover:bg-[#F2F2F2] border border-[#E8E8E8] hover:border-[#CCCCCC] rounded-[4px] transition-colors focus:outline-none cursor-pointer" aria-label="Cari Berita">
                <svg class="w-4 h-4 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="hidden sm:inline font-medium">Cari berita...</span>
                <kbd class="hidden md:inline-block bg-white text-[#80868B] px-1.5 py-0.5 rounded-[3px] border border-[#E8E8E8] text-[10px] font-mono">Ctrl+K</kbd>
            </button>

            <!-- Latest News Quick Action -->
            <x-button variant="outline" size="sm" :href="route('latest')">
                Terbaru
            </x-button>

            @auth
                <x-button variant="secondary" size="sm" :href="route('account.bookmarks')">
                    Ruang Pembaca
                </x-button>
            @else
                <x-button variant="secondary" size="sm" :href="route('login')">
                    Masuk
                </x-button>
            @endauth
        </div>
    </div>

    <!-- Category Navigation Bar -->
    <nav class="border-t border-[#E8E8E8] bg-white hidden lg:block" aria-label="Navigasi Kategori">
        <div class="tn-container">
            <ul class="flex items-center gap-1 overflow-x-auto py-1 scrollbar-none font-headline font-bold text-sm tracking-tight text-[#111111]">
                @if ($primaryMenu && $primaryMenu->activeItems->isNotEmpty())
                    @foreach ($primaryMenu->activeItems as $mItem)
                        @php
                            $isActive = $mItem->isCurrentActive();
                        @endphp
                        <li>
                            <a 
                                href="{{ $mItem->getResolvedUrl() }}" 
                                class="inline-block px-3 py-2 rounded-[3px] hover:text-[#E50914] hover:bg-[#F7F7F7] transition-colors whitespace-nowrap {{ $isActive ? 'text-[#E50914]' : '' }}"
                                {{ $mItem->open_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}
                            >
                                @if($mItem->route_name === 'video.index')
                                    <span class="text-red-600 font-black mr-0.5">▶</span>
                                @endif
                                {{ $mItem->label }}
                            </a>
                        </li>
                    @endforeach
                @else
                    <li>
                        <a href="{{ route('home') }}" class="inline-block px-3 py-2 rounded-[3px] hover:text-[#E50914] hover:bg-[#F7F7F7] transition-colors whitespace-nowrap {{ request()->routeIs('home') ? 'text-[#E50914]' : '' }}">
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('latest') }}" class="inline-block px-3 py-2 rounded-[3px] hover:text-[#E50914] hover:bg-[#F7F7F7] transition-colors whitespace-nowrap {{ request()->routeIs('latest') ? 'text-[#E50914]' : '' }}">
                            Terbaru
                        </a>
                    </li>

                    @if ($navCategories->isNotEmpty())
                        @foreach ($navCategories as $cat)
                            @php
                                $isActive = request()->is('category/' . $cat->slug);
                            @endphp
                            <li>
                                <a href="{{ route('category.show', $cat->slug) }}" class="inline-block px-3 py-2 rounded-[3px] hover:text-[#E50914] hover:bg-[#F7F7F7] transition-colors whitespace-nowrap {{ $isActive ? 'text-[#E50914]' : '' }}">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    @elseif ($fallbackConfig)
                        @foreach ($fallbackConfig as $fbCat)
                            @php
                                $isActive = request()->is(ltrim($fbCat['url'], '/')) || (isset($fbCat['slug']) && request()->is('category/' . $fbCat['slug']));
                            @endphp
                            <li>
                                <a href="{{ $fbCat['url'] }}" class="inline-block px-3 py-2 rounded-[3px] hover:text-[#E50914] hover:bg-[#F7F7F7] transition-colors whitespace-nowrap {{ $isActive ? 'text-[#E50914]' : '' }}">
                                    {{ $fbCat['label'] }}
                                </a>
                            </li>
                        @endforeach
                    @endif

                    <li>
                        <a href="{{ route('opinion.index') }}" class="inline-block px-3 py-2 rounded-[3px] hover:text-[#E50914] hover:bg-[#F7F7F7] transition-colors whitespace-nowrap {{ request()->routeIs('opinion.index') ? 'text-[#E50914]' : '' }}">
                            Opini
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('video.index') }}" class="inline-block px-3 py-2 rounded-[3px] hover:text-[#E50914] hover:bg-[#F7F7F7] transition-colors whitespace-nowrap {{ request()->routeIs('video.index') ? 'text-[#E50914]' : '' }}">
                            <span class="text-red-600 font-black mr-0.5">▶</span> Video
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('photo-story.index') }}" class="inline-block px-3 py-2 rounded-[3px] hover:text-[#E50914] hover:bg-[#F7F7F7] transition-colors whitespace-nowrap {{ request()->routeIs('photo-story.index') ? 'text-[#E50914]' : '' }}">
                            Foto Cerita
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>

<!-- Mobile Navigation Drawer -->
<div id="tn-mobile-backdrop" class="fixed inset-0 bg-black/60 z-50 hidden opacity-0 transition-opacity duration-300 backdrop-blur-xs" aria-hidden="true"></div>
<aside id="tn-mobile-drawer" class="fixed top-0 left-0 bottom-0 w-[280px] sm:w-[320px] bg-white z-50 -translate-x-full transition-transform duration-300 shadow-2xl flex flex-col" aria-label="Menu Navigasi Mobile">
    <div class="p-4 border-b border-[#E8E8E8] flex items-center justify-between">
        <x-logo size="sm" />
        <button id="tn-mobile-close" type="button" class="p-2 text-[#5F6368] hover:text-[#111111] rounded-[4px] cursor-pointer" aria-label="Tutup Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Drawer Content Scrollable -->
    <div class="flex-1 overflow-y-auto p-4 space-y-6">
        <!-- Rubrik & Halaman Navigasi -->
        <div class="space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#80868B] block mb-1">
                Kanal Berita
            </span>
            <ul class="space-y-1 font-headline font-semibold text-sm text-[#111111]">
                @if ($primaryMenu && $primaryMenu->activeItems->isNotEmpty())
                    @foreach ($primaryMenu->activeItems as $mobItem)
                        <li>
                            <a 
                                href="{{ $mobItem->getResolvedUrl() }}" 
                                class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors {{ $mobItem->isCurrentActive() ? 'text-[#E50914] font-bold' : '' }}"
                                {{ $mobItem->open_new_tab ? 'target="_blank" rel="noopener noreferrer"' : '' }}
                            >
                                @if($mobItem->route_name === 'video.index')
                                    <span class="text-red-600 font-bold">▶</span>
                                @endif
                                {{ $mobItem->label }}
                            </a>
                        </li>
                    @endforeach
                @else
                    <li>
                        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                            Beranda
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('latest') }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                            Berita Terbaru
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('video.index') }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                            <span class="text-red-600 font-bold">▶</span> Video Berita
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('photo-story.index') }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                            Foto Cerita & Galeri
                        </a>
                    </li>

                    @if ($navCategories->isNotEmpty())
                        @foreach ($navCategories as $cat)
                            <li>
                                <a href="{{ route('category.show', $cat->slug) }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    @elseif ($fallbackConfig)
                        @foreach ($fallbackConfig as $fbCat)
                            <li>
                                <a href="{{ $fbCat['url'] }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                                    {{ $fbCat['label'] }}
                                </a>
                            </li>
                        @endforeach
                    @endif

                    <li>
                        <a href="{{ route('opinion.index') }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                            Opini & Kolom
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('trending.index') }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                            Sedang Tren
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('popular.index') }}" class="block px-3 py-2 rounded-[4px] hover:bg-[#F7F7F7] hover:text-[#E50914] transition-colors">
                            Paling Banyak Dibaca
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="pt-4 border-t border-[#E8E8E8] space-y-1 text-xs text-[#5F6368]">
            <a href="{{ route('about') }}" class="block px-3 py-2 hover:text-[#111111]">Tentang TopNews</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 hover:text-[#111111]">Kontak Redaksi</a>
        </div>
    </div>
</aside>
