@extends('layouts.app')

@section('title', 'Kontak Redaksi & Layanan Informasi — TopNews')
@section('meta_description', 'Hubungi meja redaksi TopNews, hak jawab, pengiriman siaran pers, atau penawaran kerja sama iklan.')

@section('content')
<div class="tn-container py-8 pb-16">
    <x-breadcrumb :items="[['label' => 'Kontak', 'url' => null]]" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start pt-4">
        <!-- Contact Information (Cols 1-5) -->
        <div class="lg:col-span-5 space-y-6">
            <div>
                <span class="inline-block px-2.5 py-0.5 bg-[#E50914] text-white text-[11px] font-black uppercase tracking-wider rounded-[3px] mb-2">
                    HUBUNGI KAMI
                </span>
                <h1 class="font-headline font-black text-3xl sm:text-4xl text-[#111111] tracking-tight">
                    {{ $page?->title ?: 'Kontak Redaksi & Manajemen' }}
                </h1>
                <p class="text-sm text-[#5F6368] mt-2 leading-relaxed">
                    {{ $page?->excerpt ?: 'Punya pertanyaan, informasi berita, hak jawab, atau tawaran kerja sama iklan? Tim kami siap melayani Anda.' }}
                </p>
            </div>

            @if(!empty($sanitizedContent))
                <div class="text-xs text-[#5F6368] leading-relaxed">
                    {!! $sanitizedContent !!}
                </div>
            @endif

            <!-- Detail Cards from Settings -->
            <div class="space-y-4 pt-2">
                <div class="bg-[#F7F7F7] border border-[#E8E8E8] rounded-[6px] p-5">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">Kantor Redaksi Pusat & Lokasi</h2>
                    <p class="text-sm text-[#5F6368] leading-relaxed font-medium">
                        {{ $contactSettings['office_address'] ?? $contactSettings['location'] ?? 'Bireuen, Aceh, Indonesia' }}
                    </p>
                    @if(!empty($contactSettings['business_hours']))
                        <p class="text-xs text-[#80868B] mt-2">
                            <strong>Jam Operasional:</strong> {{ $contactSettings['business_hours'] }}
                        </p>
                    @endif
                </div>

                <div class="bg-[#F7F7F7] border border-[#E8E8E8] rounded-[6px] p-5 space-y-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">Email & Komunikasi</h2>
                    <p class="text-sm text-[#5F6368]">
                        <strong class="text-[#111111]">Redaksi & Rilis Pers:</strong> 
                        <a href="mailto:{{ $contactSettings['public_email'] ?? 'topnews90@gmail.com' }}" class="text-[#E50914] hover:underline font-medium">
                            {{ $contactSettings['public_email'] ?? 'topnews90@gmail.com' }}
                        </a>
                    </p>
                    @if(!empty($contactSettings['public_phone']))
                        @php
                            $phoneRaw = $contactSettings['public_phone'];
                            $digits = preg_replace('/[^0-9]/', '', $phoneRaw);
                            $telHref = str_starts_with($digits, '08') ? '+628' . substr($digits, 2) : (str_starts_with($digits, '62') ? '+' . $digits : $digits);
                        @endphp
                        <p class="text-sm text-[#5F6368]">
                            <strong class="text-[#111111]">Telepon Kantor:</strong> 
                            <a href="tel:{{ $telHref }}" class="text-[#111111] hover:text-[#E50914] hover:underline font-medium">
                                {{ $contactSettings['public_phone'] }}
                            </a>
                        </p>
                    @endif
                    @if(!empty($contactSettings['whatsapp']))
                        <p class="text-sm text-[#5F6368]">
                            <strong class="text-[#111111]">Hotline WhatsApp:</strong> {{ $contactSettings['whatsapp'] }}
                        </p>
                    @endif
                    <p class="text-sm text-[#5F6368]">
                        <strong class="text-[#111111]">Iklan & Sponsorship:</strong> 
                        <a href="mailto:{{ $contactSettings['public_email'] ?? 'topnews90@gmail.com' }}" class="text-[#E50914] hover:underline font-medium">
                            {{ $contactSettings['public_email'] ?? 'topnews90@gmail.com' }}
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Form (Cols 6-12) -->
        <div class="lg:col-span-7">
            <div class="bg-white border border-[#E8E8E8] rounded-[8px] p-6 sm:p-8 shadow-subtle">
                <h2 class="font-headline font-bold text-xl text-[#111111] mb-1">
                    Kirim Pesan ke Meja Redaksi
                </h2>
                <p class="text-xs text-[#5F6368] mb-6">
                    Isi formulir di bawah ini dengan lengkap untuk terhubung dengan staf redaksi kami.
                </p>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-[6px] bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-[4px] bg-[#FDE8E9] border border-[#E50914] text-xs text-[#C8102E] space-y-1">
                        <p class="font-bold">Mohon perbaiki data input berikut:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                                Nama Lengkap <span class="text-[#E50914]">*</span>
                            </label>
                            <input 
                                id="name" 
                                name="name" 
                                type="text" 
                                value="{{ old('name') }}" 
                                required 
                                class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                                placeholder="Nama Anda"
                            />
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                                Alamat Email <span class="text-[#E50914]">*</span>
                            </label>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                value="{{ old('email') }}" 
                                required 
                                class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                                placeholder="email@contoh.com"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                                Kategori Pesan
                            </label>
                            <select 
                                id="category" 
                                name="category" 
                                class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914] bg-white"
                            >
                                <option value="editorial" {{ old('category') === 'editorial' ? 'selected' : '' }}>Redaksi / Siaran Pers / Hak Jawab</option>
                                <option value="advertising" {{ old('category') === 'advertising' ? 'selected' : '' }}>Pemasangan Iklan & Kerja Sama</option>
                                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Kendala Teknis Situs</option>
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>Pertanyaan Umum</option>
                            </select>
                        </div>

                        <div>
                            <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                                Subjek / Perihal <span class="text-[#E50914]">*</span>
                            </label>
                            <input 
                                id="subject" 
                                name="subject" 
                                type="text" 
                                value="{{ old('subject') }}" 
                                required 
                                class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                                placeholder="Contoh: Hak Jawab atas Berita..."
                            />
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-bold uppercase tracking-wider text-[#111111] mb-1">
                            Pesan Anda <span class="text-[#E50914]">*</span>
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="5" 
                            required 
                            class="w-full text-sm border border-[#CCCCCC] rounded-[4px] px-3.5 py-2.5 focus:outline-none focus:border-[#E50914] focus:ring-1 focus:ring-[#E50914]"
                            placeholder="Tuliskan pesan atau informasi Anda secara lengkap dan jelas..."
                        >{{ old('message') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="bg-[#E50914] hover:bg-[#B80710] text-white font-bold text-sm px-8 py-2.5 rounded-[4px] transition-colors shadow-sm">
                            Kirim Pesan ke Redaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
