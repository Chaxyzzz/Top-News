<div id="tn-search-modal" class="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24 px-4 bg-black/60 backdrop-blur-xs hidden opacity-0 transition-opacity duration-200" aria-modal="true" role="dialog">
    <div class="bg-white w-full max-w-2xl rounded-[8px] shadow-2xl overflow-hidden border border-[#E8E8E8] transition-all transform scale-100">
        <!-- Search Input Bar Form -->
        <form action="{{ route('search') }}" method="GET" class="p-4 border-b border-[#E8E8E8] flex items-center gap-3">
            <svg class="w-5 h-5 text-[#80868B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
                id="tn-search-input" 
                name="q"
                type="search" 
                placeholder="Cari topik, berita, atau tokoh..." 
                class="w-full text-base md:text-lg text-[#111111] placeholder-[#80868B] focus:outline-none bg-transparent"
                autocomplete="off"
            />
            <button id="tn-search-close" type="button" class="p-1 text-[#80868B] hover:text-[#111111] rounded-[4px] cursor-pointer" aria-label="Tutup Pencarian">
                <kbd class="bg-[#F2F2F2] px-2 py-0.5 rounded text-xs font-mono text-[#5F6368]">ESC</kbd>
            </button>
        </form>

        <!-- Quick Trending suggestions -->
        <div class="p-4 bg-[#F7F7F7]">
            <p class="text-xs font-bold uppercase tracking-wider text-[#80868B] mb-2">Paling Sering Dicari</p>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('search', ['q' => 'Transformasi Digital']) }}" class="px-2.5 py-1 bg-white border border-[#E8E8E8] hover:border-[#E50914] text-xs text-[#111111] rounded-[4px] transition-colors">
                    #TransformasiDigital
                </a>
                <a href="{{ route('search', ['q' => 'Kecerdasan Buatan']) }}" class="px-2.5 py-1 bg-white border border-[#E8E8E8] hover:border-[#E50914] text-xs text-[#111111] rounded-[4px] transition-colors">
                    #KecerdasanBuatan
                </a>
                <a href="{{ route('search', ['q' => 'Ibu Kota Nusantara']) }}" class="px-2.5 py-1 bg-white border border-[#E8E8E8] hover:border-[#E50914] text-xs text-[#111111] rounded-[4px] transition-colors">
                    #IbuKotaNusantara
                </a>
                <a href="{{ route('search', ['q' => 'Bursa Saham']) }}" class="px-2.5 py-1 bg-white border border-[#E8E8E8] hover:border-[#E50914] text-xs text-[#111111] rounded-[4px] transition-colors">
                    #BursaSaham
                </a>
                <a href="{{ route('search', ['q' => 'Piala Dunia']) }}" class="px-2.5 py-1 bg-white border border-[#E8E8E8] hover:border-[#E50914] text-xs text-[#111111] rounded-[4px] transition-colors">
                    #PialaDunia
                </a>
            </div>
        </div>
    </div>
</div>
