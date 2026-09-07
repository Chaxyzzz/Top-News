@extends('layouts.app')

@section('title', ($page?->seo_title ?: ($page?->title ?: 'Tentang TopNews')) . ' — Visi & Standar Jurnalistik')
@section('meta_description', $page?->seo_description ?: ($page?->excerpt ?: 'Mengenal TopNews, visi jurnalisme independen, standar etika redaksi, dan komitmen penyajian informasi terpercaya.'))

@section('content')
<div class="tn-container py-8 pb-16">
    <x-breadcrumb :items="[['label' => $page?->title ?: 'Tentang Kami', 'url' => null]]" />

    <div class="max-w-4xl mx-auto space-y-12">
        <!-- Hero Header -->
        <div class="border-b border-[#E8E8E8] pb-8">
            <span class="inline-block px-2.5 py-0.5 bg-[#E50914] text-white text-[11px] font-black uppercase tracking-wider rounded-[3px] mb-3">
                PROFIL PERUSAHAAN
            </span>
            <h1 class="font-headline font-black text-3xl sm:text-4xl lg:text-5xl text-[#111111] tracking-tight mb-4">
                {{ $page?->title ?: 'Informasi Cepat. Perspektif Jelas. Berita Terpercaya.' }}
            </h1>
            <p class="text-base sm:text-lg text-[#5F6368] leading-relaxed font-sans">
                {{ $page?->excerpt ?: config('topnews.description') }}
            </p>
        </div>

        @if(!empty($sanitizedContent))
            <!-- Dynamic Content from Page CMS -->
            <div class="prose prose-neutral max-w-none text-[#222222] font-serif text-base sm:text-lg leading-relaxed space-y-6">
                {!! $sanitizedContent !!}
            </div>
        @else
            <!-- Fallback Static Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-[#F7F7F7] border border-[#E8E8E8] rounded-[6px] p-6">
                    <h2 class="font-headline font-bold text-lg text-[#111111] uppercase tracking-wide mb-3 flex items-center gap-2">
                        <span class="w-2 h-4 bg-[#E50914] rounded-[1px]"></span>
                        Visi Kami
                    </h2>
                    <p class="text-sm text-[#5F6368] leading-relaxed">
                        Menjadi platform media digital rujukan utama di Indonesia yang menyajikan informasi kredibel, mencerahkan publik, serta mendorong kemajuan peradaban dan demokrasi yang berkeadilan.
                    </p>
                </div>

                <div class="bg-[#F7F7F7] border border-[#E8E8E8] rounded-[6px] p-6">
                    <h2 class="font-headline font-bold text-lg text-[#111111] uppercase tracking-wide mb-3 flex items-center gap-2">
                        <span class="w-2 h-4 bg-[#E50914] rounded-[1px]"></span>
                        Misi Kami
                    </h2>
                    <ul class="text-sm text-[#5F6368] space-y-2 leading-relaxed list-disc list-inside">
                        <li>Menyajikan berita dengan standar verifikasi fakta yang ketat dan berlapis.</li>
                        <li>Mengutamakan kepentingan publik melalui jurnalisme berimbang dan berani.</li>
                        <li>Memanfaatkan teknologi modern demi kecepatan akses tanpa mengorbankan akurasi.</li>
                    </ul>
                </div>
            </div>
        @endif

        <!-- Quick Links to Institutional Pages -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-[#E8E8E8]">
            <a href="{{ route('editorial.team') }}" class="block p-5 bg-[#F8F9FA] hover:bg-[#F1F3F4] border border-[#E8E8E8] rounded-[6px] transition-colors group">
                <h3 class="font-headline font-bold text-sm text-[#111111] group-hover:text-[#E50914] flex items-center justify-between">
                    Dewan Redaksi
                    <span class="text-xs">&rarr;</span>
                </h3>
                <p class="text-xs text-[#5F6368] mt-1">Struktur pimpinan redaksi dan jurnalis TopNews.</p>
            </a>

            <a href="{{ route('editorial.guidelines') }}" class="block p-5 bg-[#F8F9FA] hover:bg-[#F1F3F4] border border-[#E8E8E8] rounded-[6px] transition-colors group">
                <h3 class="font-headline font-bold text-sm text-[#111111] group-hover:text-[#E50914] flex items-center justify-between">
                    Pedoman Redaksi
                    <span class="text-xs">&rarr;</span>
                </h3>
                <p class="text-xs text-[#5F6368] mt-1">Kode etik, verifikasi fakta, dan standar peliputan.</p>
            </a>

            <a href="{{ route('contact') }}" class="block p-5 bg-[#F8F9FA] hover:bg-[#F1F3F4] border border-[#E8E8E8] rounded-[6px] transition-colors group">
                <h3 class="font-headline font-bold text-sm text-[#111111] group-hover:text-[#E50914] flex items-center justify-between">
                    Hubungi Redaksi
                    <span class="text-xs">&rarr;</span>
                </h3>
                <p class="text-xs text-[#5F6368] mt-1">Pengiriman rilis pers, hak jawab, dan alamat kantor.</p>
            </a>
        </div>

        <!-- In-page Newsletter CTA -->
        <div class="pt-6">
            <x-newsletter-cta variant="inline" source="about_page" />
        </div>
    </div>
</div>
@endsection
