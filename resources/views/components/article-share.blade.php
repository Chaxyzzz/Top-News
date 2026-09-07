@props([
    'title' => '',
    'url' => '',
    'variant' => 'inline', // 'inline' | 'sticky' | 'bottom'
])

@php
    $shareUrl = rawurlencode($url ?: url()->current());
    $shareTitle = rawurlencode($title);
    $plainTitle = $title;
@endphp

@if ($variant === 'sticky')
    <!-- Desktop Vertical Sticky Share Rail -->
    <aside class="sticky-share-rail hidden xl:flex flex-col items-center gap-3 sticky top-28 z-20 no-print" aria-label="Bagikan Artikel">
        <span class="text-[10px] font-black uppercase tracking-wider text-[#9CA3AF] -rotate-90 origin-center mb-4">
            SHARE
        </span>

        <!-- WhatsApp -->
        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#F3F4F6] hover:bg-[#25D366] text-[#4B5563] hover:text-white flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#25D366]" aria-label="Bagikan ke WhatsApp" title="Bagikan ke WhatsApp">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
        </a>

        <!-- Facebook -->
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#F3F4F6] hover:bg-[#1877F2] text-[#4B5563] hover:text-white flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1877F2]" aria-label="Bagikan ke Facebook" title="Bagikan ke Facebook">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </a>

        <!-- X (Twitter) -->
        <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#F3F4F6] hover:bg-[#111111] text-[#4B5563] hover:text-white flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#111111]" aria-label="Bagikan ke X" title="Bagikan ke X">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
        </a>

        <!-- Telegram -->
        <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#F3F4F6] hover:bg-[#229ED9] text-[#4B5563] hover:text-white flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#229ED9]" aria-label="Bagikan ke Telegram" title="Bagikan ke Telegram">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.939z"/>
            </svg>
        </a>

        <!-- Copy Link -->
        <button type="button" data-action="copy-article-link" data-url="{{ $url ?: url()->current() }}" class="w-9 h-9 rounded-full bg-[#F3F4F6] hover:bg-[#E50914] text-[#4B5563] hover:text-white flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#E50914]" aria-label="Salin Tautan Artikel" title="Salin Tautan">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
        </button>

        <!-- Print -->
        <button type="button" data-action="print-article" class="w-9 h-9 rounded-full bg-[#F3F4F6] hover:bg-[#111111] text-[#4B5563] hover:text-white flex items-center justify-center transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#111111]" aria-label="Cetak Artikel" title="Cetak Artikel">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
        </button>
    </aside>
@else
    <!-- Inline / Bottom Share Controls -->
    <div class="article-share-tools flex items-center gap-2 flex-wrap no-print" aria-label="Bagikan Artikel">
        <span class="text-xs font-bold uppercase tracking-wider text-[#6B7280] mr-1">
            Bagikan:
        </span>

        <!-- WhatsApp -->
        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] bg-[#F3F4F6] hover:bg-[#25D366] text-[#374151] hover:text-white text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-[#25D366]" aria-label="Bagikan ke WhatsApp">
            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span>WhatsApp</span>
        </a>

        <!-- Facebook -->
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] bg-[#F3F4F6] hover:bg-[#1877F2] text-[#374151] hover:text-white text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-[#1877F2]" aria-label="Bagikan ke Facebook">
            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Facebook</span>
        </a>

        <!-- X -->
        <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] bg-[#F3F4F6] hover:bg-[#111111] text-[#374151] hover:text-white text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-[#111111]" aria-label="Bagikan ke X">
            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            <span>X</span>
        </a>

        <!-- Telegram -->
        <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] bg-[#F3F4F6] hover:bg-[#229ED9] text-[#374151] hover:text-white text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-[#229ED9]" aria-label="Bagikan ke Telegram">
            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.538-.196 1.006.128.832.939z"/>
            </svg>
            <span>Telegram</span>
        </a>

        <!-- Email -->
        <a href="mailto:?subject={{ $shareTitle }}&body=Baca%20artikel%20menarik%20di%20TopNews:%20{{ $shareUrl }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] bg-[#F3F4F6] hover:bg-[#4B5563] text-[#374151] hover:text-white text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-[#4B5563]" aria-label="Kirim melalui Email">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span>Email</span>
        </a>

        <!-- Copy Link -->
        <button type="button" data-action="copy-article-link" data-url="{{ $url ?: url()->current() }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] bg-[#F3F4F6] hover:bg-[#E50914] text-[#374151] hover:text-white text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-[#E50914]" aria-label="Salin Tautan">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <span>Salin Tautan</span>
        </button>

        <!-- Print -->
        <button type="button" data-action="print-article" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[4px] bg-[#F3F4F6] hover:bg-[#111111] text-[#374151] hover:text-white text-xs font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-[#111111]" aria-label="Cetak Artikel">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>Cetak</span>
        </button>
    </div>
@endif
