@php
    $navService = app(\App\Services\NavigationService::class);
    $settings = app(\App\Services\SettingsService::class);

    // Dynamic Footer Menu
    $footerMenu = $navService->getMenu('footer');

    // Dynamic Settings
    $aboutText = $settings->get('footer.about_text', config('topnews.description'));
    $copyrightText = $settings->get('footer.copyright_text', 'Hak cipta dilindungi undang-undang. Redaksi mematuhi Kode Etik Jurnalistik Dewan Pers.');
    $contactSettings = $settings->getGroup('contact');
    $socialSettings = array_filter($settings->getGroup('social'));
@endphp

<footer class="bg-[#111111] text-white border-t-4 border-[#E50914] mt-auto">
    <!-- Main Footer Content -->
    <div class="tn-container py-12 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12">
            <!-- Brand & Company Info (Cols 1-4) -->
            <div class="lg:col-span-4 space-y-4">
                <x-logo variant="white" size="lg" />
                <p class="text-sm text-[#A0A0A0] leading-relaxed font-sans">
                    {{ $aboutText }}
                </p>
                
                <div class="pt-2 text-xs text-[#80868B] space-y-1">
                    <p><strong class="text-[#D0D0D0]">Alamat / Lokasi:</strong> {{ $contactSettings['office_address'] ?? $contactSettings['location'] ?? 'Bireuen, Aceh, Indonesia' }}</p>
                    <p><strong class="text-[#D0D0D0]">Email:</strong> <a href="mailto:{{ $contactSettings['public_email'] ?? 'topnews90@gmail.com' }}" class="hover:text-white transition-colors">{{ $contactSettings['public_email'] ?? 'topnews90@gmail.com' }}</a></p>
                    @if(!empty($contactSettings['public_phone']))
                        @php
                            $phoneRaw = $contactSettings['public_phone'];
                            $digits = preg_replace('/[^0-9]/', '', $phoneRaw);
                            $telHref = str_starts_with($digits, '08') ? '+628' . substr($digits, 2) : (str_starts_with($digits, '62') ? '+' . $digits : $digits);
                        @endphp
                        <p><strong class="text-[#D0D0D0]">Telepon:</strong> <a href="tel:{{ $telHref }}" class="hover:text-white transition-colors">{{ $contactSettings['public_phone'] }}</a></p>
                    @endif
                </div>

                <!-- Social Icons -->
                @if(!empty($socialSettings))
                    <div class="flex items-center gap-3 pt-2">
                        @foreach ($socialSettings as $platform => $link)
                            @if($link)
                                <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-[#222222] hover:bg-[#E50914] flex items-center justify-center text-[#D0D0D0] hover:text-white transition-colors" aria-label="{{ ucfirst($platform) }}">
                                    <span class="text-xs font-bold uppercase">{{ substr($platform, 0, 2) }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Categories Grid (Cols 5-8) -->
            <div class="lg:col-span-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#A0A0A0] mb-4 pb-2 border-b border-[#222222]">
                    Rubrik Utama
                </h3>
                
                @if ($footerMenu && $footerMenu->items->isNotEmpty())
                    <ul class="grid grid-cols-2 gap-y-2.5 gap-x-4 text-xs font-medium text-[#C0C0C0]">
                        @foreach ($footerMenu->items as $item)
                            <li>
                                <a href="{{ $item->url }}" target="{{ $item->target }}" class="hover:text-white transition-colors">
                                    {{ $item->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <ul class="grid grid-cols-2 gap-y-2.5 gap-x-4 text-xs font-medium text-[#C0C0C0]">
                        <li><a href="{{ route('latest') }}" class="hover:text-white transition-colors">Terkini</a></li>
                        <li><a href="{{ route('popular.index') }}" class="hover:text-white transition-colors">Terpopuler</a></li>
                        <li><a href="{{ route('trending.index') }}" class="hover:text-white transition-colors">Trending</a></li>
                        <li><a href="{{ route('opinion.index') }}" class="hover:text-white transition-colors">Opini & Kolom</a></li>
                        <li><a href="{{ route('video.index') }}" class="hover:text-white transition-colors">Video Berita</a></li>
                        <li><a href="{{ route('photo-story.index') }}" class="hover:text-white transition-colors">Foto Cerita</a></li>
                    </ul>
                @endif
            </div>

            <!-- Institutional Links & Newsletter (Cols 9-12) -->
            <div class="lg:col-span-4 space-y-6">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#A0A0A0] mb-4 pb-2 border-b border-[#222222]">
                        Informasi & Lembaga
                    </h3>
                    <ul class="grid grid-cols-2 gap-y-2 gap-x-4 text-xs text-[#C0C0C0]">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ route('editorial.team') }}" class="hover:text-white transition-colors">Dewan Redaksi</a></li>
                        <li><a href="{{ route('editorial.guidelines') }}" class="hover:text-white transition-colors">Pedoman Redaksi</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('disclaimer') }}" class="hover:text-white transition-colors">Disclaimer</a></li>
                        <li><a href="{{ route('advertise') }}" class="hover:text-white transition-colors">Info Iklan</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Kontak Redaksi</a></li>
                    </ul>
                </div>

                <!-- Newsletter Subscription Block -->
                <div class="bg-[#1A1A1A] p-4 rounded-[6px] border border-[#2A2A2A]">
                    <x-newsletter-cta variant="footer" source="footer" />
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Copyright Bar -->
    <div class="border-t border-[#222222] bg-[#0A0A0A] py-5">
        <div class="tn-container flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-[#707070]">
            <p>© {{ date('Y') }} {{ $settings->get('general.site_name', 'TopNews') }}. {{ $copyrightText }}</p>
            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                <span>Platform media digital independen dengan verifikasi fakta.</span>
                @if($devName = $settings->get('footer.developer_name', 'Zakky Mubaraq'))
                    <span class="opacity-40">•</span>
                    <span class="text-[#888888]">{{ $settings->get('footer.developer_label', 'Website developed by') }} <strong class="text-[#B0B0B0] font-medium">{{ $devName }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</footer>
