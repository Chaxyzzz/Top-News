@extends('layouts.app')

@section('title', $page->seo_title ?: $page->title . ' — TopNews')
@section('meta_description', $page->seo_description ?: ($page->excerpt ?: Str::limit(strip_tags($sanitizedContent), 160)))

@if(!empty($isPreview))
    @push('styles')
        <meta name="robots" content="noindex, nofollow">
    @endpush
@endif

@section('content')
<div class="tn-container py-8 pb-16">
    <x-breadcrumb :items="[['label' => $page->title, 'url' => null]]" />

    @if(!empty($isPreview))
        <div class="mb-6 p-4 bg-amber-50 border border-amber-300 rounded-[6px] text-amber-900 text-xs flex items-center justify-between">
            <span class="font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Mode Pratinjau Redaksi: Halaman ini berstatus {{ $page->status->label() }} dan tidak diindeks mesin pencari.
            </span>
            <a href="{{ route('admin.pages.edit', $page) }}" class="underline font-semibold hover:text-amber-700">Edit Halaman</a>
        </div>
    @endif

    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Page Header -->
        <div class="border-b border-[#E8E8E8] pb-6">
            <span class="inline-block px-2.5 py-0.5 bg-[#E50914] text-white text-[11px] font-black uppercase tracking-wider rounded-[3px] mb-3">
                INFORMASI PUBLIK
            </span>
            <h1 class="font-headline font-black text-3xl sm:text-4xl lg:text-5xl text-[#111111] tracking-tight mb-4">
                {{ $page->title }}
            </h1>
            @if($page->excerpt)
                <p class="text-base sm:text-lg text-[#5F6368] leading-relaxed font-sans">
                    {{ $page->excerpt }}
                </p>
            @endif
            @if($page->published_at)
                <div class="mt-4 text-xs text-[#80868B]">
                    Terakhir diperbarui: {{ $page->updated_at->isoFormat('D MMMM Y') }}
                </div>
            @endif
        </div>

        <!-- Rich Text Content with Premium Article Typography -->
        <div class="prose prose-neutral max-w-none text-[#222222] font-serif text-base sm:text-lg leading-relaxed space-y-6">
            {!! $sanitizedContent !!}
        </div>

        <!-- Optional In-page Newsletter CTA -->
        <div class="pt-8 border-t border-[#E8E8E8]">
            <x-newsletter-cta variant="inline" source="static_page" />
        </div>
    </div>
</div>
@endsection
