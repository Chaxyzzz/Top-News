@props([
    'variant' => 'box', // 'box', 'inline', 'footer'
    'source' => 'article',
])

@php
    $settings = app(\App\Services\SettingsService::class);
    $enabled = (bool) $settings->get('newsletter.enabled', true);
    $ctaTitle = $settings->get('newsletter.cta_title', 'Berita Penting, Langsung ke Inbox Anda.');
    $ctaDesc = $settings->get('newsletter.cta_description', 'Dapatkan kurasi liputan terbaik, investigasi eksklusif, dan analisis harian dari meja redaksi TopNews setiap pagi.');
@endphp

@if($enabled)
    @if($variant === 'footer')
        <div class="space-y-2">
            <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-1">{{ $ctaTitle }}</h4>
            <p class="text-[11px] text-[#9AA0A6] leading-relaxed mb-3">{{ $ctaDesc }}</p>

            @if(session('success') && old('source') === $source)
                <div class="p-2 bg-emerald-900/50 border border-emerald-500 text-emerald-200 text-xs rounded">
                    {{ session('success') }}
                </div>
            @else
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                    @csrf
                    <input type="hidden" name="source" value="{{ $source }}">
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        placeholder="Alamat email Anda" 
                        class="bg-[#222222] border border-[#333333] text-xs text-white placeholder-[#80868B] px-3 py-2 rounded-[4px] flex-1 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                    >
                    <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold px-4 py-2 rounded-[4px] transition-colors whitespace-nowrap">
                        Daftar
                    </button>
                </form>
            @endif
        </div>
    @elseif($variant === 'inline')
        <div class="my-8 p-6 bg-[#F8F9FA] border border-[#E8E8E8] rounded-[8px] flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="max-w-md space-y-1">
                <span class="inline-block px-2 py-0.5 bg-[#E50914] text-white text-[10px] font-black uppercase tracking-wider rounded-[3px]">
                    BULETIN HARIAN
                </span>
                <h3 class="font-headline font-bold text-lg text-[#111111]">{{ $ctaTitle }}</h3>
                <p class="text-xs text-[#5F6368] leading-relaxed">{{ $ctaDesc }}</p>
            </div>

            <div class="w-full md:w-auto min-w-[280px]">
                @if(session('success') && old('source') === $source)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-[4px]">
                        {{ session('success') }}
                    </div>
                @else
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <input type="hidden" name="source" value="{{ $source }}">
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            placeholder="Alamat email Anda" 
                            class="text-xs border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 flex-1 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                        >
                        <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold px-5 py-2.5 rounded-[4px] transition-colors whitespace-nowrap shadow-sm">
                            Berlangganan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @else
        {{-- Default box variant --}}
        <div class="bg-[#111111] text-white p-6 rounded-[8px] space-y-3 newsletter-box border border-[#222222]">
            <div class="space-y-1">
                <span class="text-[10px] font-bold tracking-widest text-[#E50914] uppercase">TopNews Morning Brief</span>
                <h3 class="font-headline font-bold text-base text-white">{{ $ctaTitle }}</h3>
                <p class="text-xs text-[#9AA0A6] leading-relaxed">
                    {{ $ctaDesc }}
                </p>
            </div>

            @if(session('success'))
                <div class="p-3 bg-emerald-950/60 border border-emerald-500/50 text-emerald-200 text-xs rounded-[4px]">
                    {{ session('success') }}
                </div>
            @else
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-2">
                    @csrf
                    <input type="hidden" name="source" value="{{ $source }}">
                    <div class="space-y-2">
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            placeholder="Alamat email Anda" 
                            class="w-full bg-[#222222] border border-[#333333] text-xs text-white placeholder-[#80868B] px-3 py-2.5 rounded-[4px] focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                        >
                        <button type="submit" class="w-full bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold py-2.5 px-4 rounded-[4px] transition-colors">
                            Langganan Gratis
                        </button>
                    </div>
                    <p class="text-[10px] text-[#80868B] leading-tight text-center">
                        Bebas spam. Berhenti berlangganan kapan saja dengan 1 klik.
                    </p>
                </form>
            @endif
        </div>
    @endif
@endif
