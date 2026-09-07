@extends('layouts.app')

@section('title', 'Susunan Dewan Redaksi & Manajemen — TopNews')
@section('meta_description', 'Susunan dewan redaksi, pimpinan keredaksian, dan jurnalis profesional TopNews.')

@section('content')
<div class="tn-container py-8 pb-16">
    <x-breadcrumb :items="[
        ['label' => 'Tentang Kami', 'url' => route('about')],
        ['label' => 'Dewan Redaksi', 'url' => null]
    ]" />

    <div class="max-w-4xl mx-auto space-y-12">
        <!-- Header -->
        <div class="border-b border-[#E8E8E8] pb-6">
            <span class="inline-block px-2.5 py-0.5 bg-[#E50914] text-white text-[11px] font-black uppercase tracking-wider rounded-[3px] mb-3">
                DEWAN REDAKSI
            </span>
            <h1 class="font-headline font-black text-3xl sm:text-4xl lg:text-5xl text-[#111111] tracking-tight mb-4">
                {{ $page?->title ?: 'Susunan Dewan Redaksi & Manajemen' }}
            </h1>
            @if($page?->excerpt)
                <p class="text-base sm:text-lg text-[#5F6368] leading-relaxed font-sans">
                    {{ $page->excerpt }}
                </p>
            @endif
        </div>

        @if(!empty($sanitizedContent))
            <div class="prose prose-neutral max-w-none text-[#222222] font-serif text-base leading-relaxed">
                {!! $sanitizedContent !!}
            </div>
        @endif

        <!-- Editorial Team Members Grid -->
        <div>
            <h2 class="font-headline font-black text-2xl text-[#111111] uppercase tracking-tight pb-3 border-b border-[#E8E8E8] mb-8">
                Jajaran Keredaksian
            </h2>

            @if($team->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($team as $member)
                        <div class="bg-white border border-[#E8E8E8] rounded-[8px] p-6 flex flex-col sm:flex-row gap-5 items-start shadow-subtle hover:border-[#CCCCCC] transition-colors">
                            <div class="w-20 h-20 rounded-full overflow-hidden bg-[#F0F0F0] border border-[#E8E8E8] flex-shrink-0 flex items-center justify-center">
                                @if($member->avatar)
                                    <img src="{{ Storage::url($member->avatar) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xl font-black text-[#80868B] uppercase font-headline">
                                        {{ Str::substr($member->name, 0, 2) }}
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-2 flex-1">
                                <div>
                                    <h3 class="font-headline font-bold text-lg text-[#111111] leading-tight">
                                        {{ $member->name }}
                                    </h3>
                                    @if($member->public_title)
                                        <p class="text-xs font-bold text-[#E50914] uppercase tracking-wider mt-0.5">
                                            {{ $member->public_title }}
                                        </p>
                                    @endif
                                </div>

                                @if($member->bio)
                                    <p class="text-xs text-[#5F6368] leading-relaxed line-clamp-4">
                                        {{ $member->bio }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-[#F8F9FA] border border-[#E8E8E8] rounded-[6px] p-8 text-center text-[#80868B] text-sm">
                    Susunan tim redaksi sedang dalam proses pembaruan data oleh sekretariat redaksi.
                </div>
            @endif
        </div>

        <!-- Pedoman Redaksi Banner -->
        <div class="p-6 bg-[#111111] text-white rounded-[8px] flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="space-y-1 text-center sm:text-left">
                <h3 class="font-headline font-bold text-base text-white">Standar Etika & Kode Etik Redaksi</h3>
                <p class="text-xs text-[#9AA0A6]">Baca pedoman pemberitaan dan standar penulisan yang dianut seluruh jurnalis TopNews.</p>
            </div>
            <a href="{{ route('editorial.guidelines') }}" class="bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold px-5 py-2.5 rounded-[4px] transition-colors whitespace-nowrap">
                Baca Pedoman &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
