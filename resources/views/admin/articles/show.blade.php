@extends('layouts.admin')

@section('title', 'Tinjau Naskah: ' . $article->title)

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        :title="$article->title" 
        :description="'Format: ' . $article->content_type->label() . ' | Rubrik: ' . $article->category->name . ' | Estimasi: ' . $article->reading_time . ' menit baca'"
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Artikel Berita', 'url' => route('admin.articles.index')],
                ['label' => 'Tinjau Naskah']
            ]" />
        </x-slot>

        <x-slot name="actions">
            <!-- Preview Action -->
            <a href="{{ route('admin.articles.preview', $article) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#4B5563] hover:text-[#171717] bg-white border border-[#CCCCCC] rounded-[4px] shadow-2xs hover:bg-[#F3F4F6] transition-colors">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>Pratinjau Publik</span>
            </a>

            @can('update', $article)
                <x-button variant="primary" size="sm" :href="route('admin.articles.edit', $article)">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Sunting Naskah</span>
                </x-button>
            @endcan
        </x-slot>
    </x-admin.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Content Pane (Cols 1-8) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Article Body Card -->
            <div class="bg-white p-6 sm:p-8 rounded-[8px] border border-[#E5E7EB] shadow-subtle space-y-6">
                <!-- Metadata header -->
                <div class="border-b border-[#F3F4F6] pb-4 space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <x-admin.badge :variant="$article->status->badgeVariant()" size="sm">
                            {{ $article->status->label() }}
                        </x-admin.badge>

                        <span class="px-2 py-0.5 rounded text-xs font-bold" style="color: {{ $article->category->accent_color ?? '#171717' }}; background-color: {{ $article->category->accent_color ?? '#171717' }}15;">
                            {{ $article->category->name }}
                        </span>

                        <span class="text-xs text-[#6B7280]">
                            Oleh: <strong class="text-[#171717]">{{ $article->author?->name ?? '-' }}</strong>
                        </span>
                    </div>

                    <h1 class="font-headline font-black text-2xl sm:text-3xl text-[#171717] leading-tight">
                        {{ $article->title }}
                    </h1>

                    @if ($article->subtitle)
                        <p class="text-sm sm:text-base text-[#4B5563] font-medium leading-relaxed">
                            {{ $article->subtitle }}
                        </p>
                    @endif
                </div>

                <!-- Featured Image -->
                @if ($article->featured_image_url)
                    <figure class="rounded-[6px] overflow-hidden border border-[#E5E7EB]">
                        <img src="{{ $article->featured_image_url }}" alt="{{ $article->featured_image_alt ?? $article->title }}" class="w-full max-h-[450px] object-cover">
                        @if ($article->featured_image_caption)
                            <figcaption class="p-2.5 bg-[#F9FAFB] text-xs text-[#6B7280] italic border-t border-[#E5E7EB]">
                                {{ $article->featured_image_caption }}
                            </figcaption>
                        @endif
                    </figure>
                @endif

                <!-- Excerpt -->
                @if ($article->excerpt)
                    <div class="p-4 bg-[#F9FAFB] border-l-4 border-[#E50914] rounded-r text-xs text-[#4B5563] font-medium leading-relaxed">
                        {{ $article->excerpt }}
                    </div>
                @endif

                <!-- Sanitized Article Content -->
                <div class="prose max-w-none text-sm text-[#171717] leading-relaxed pt-2">
                    {!! App\Services\ContentSanitizerService::sanitize($article->content) !!}
                </div>

                <!-- Tags & Source Attribution -->
                <div class="pt-6 border-t border-[#E5E7EB] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[#6B7280] font-semibold">Tagar:</span>
                        @forelse ($article->tags as $t)
                            <span class="px-2 py-0.5 bg-[#F3F4F6] text-[#4B5563] rounded font-medium">#{{ $t->name }}</span>
                        @empty
                            <span class="text-[#9CA3AF] italic">Tidak ada tagar</span>
                        @endforelse
                    </div>

                    @if ($article->source_name)
                        <div class="text-[#6B7280]">
                            Sumber: <strong class="text-[#171717]">{{ $article->source_name }}</strong>
                            @if ($article->source_url)
                                (<a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" class="text-[#E50914] hover:underline">Tautan Asli</a>)
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar / Editorial Actions & Timeline (Cols 9-12) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Review Action Decision Box -->
            <x-admin.card title="Otorisasi & Aksi Redaksi">
                <div class="space-y-3 text-xs">
                    <!-- Start Review -->
                    @can('startReview', $article)
                        <form action="{{ route('admin.articles.review.start', $article) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2 px-3 bg-[#1E3A8A] hover:bg-[#1E40AF] text-white font-bold rounded transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                                <span>Mulai Tinjau Naskah</span>
                            </button>
                        </form>
                    @endcan

                    <!-- Request Revision Form -->
                    @can('requestRevision', $article)
                        <details class="bg-[#FFF8E1] border border-[#FFE082] p-3 rounded space-y-2">
                            <summary class="font-bold text-[#B78103] cursor-pointer">
                                ✍ Minta Revisi ke Penulis
                            </summary>
                            <form action="{{ route('admin.articles.revision.request', $article) }}" method="POST" class="pt-2 space-y-2">
                                @csrf
                                <textarea name="note" rows="3" required placeholder="Tuliskan poin-poin yang perlu diperbaiki oleh reporter..." class="w-full text-xs border border-[#CCCCCC] rounded p-2 bg-white focus:outline-none focus:border-[#E50914]"></textarea>
                                <button type="submit" class="w-full py-1.5 bg-[#B78103] hover:bg-[#926600] text-white font-bold rounded cursor-pointer">
                                    Kirim Permintaan Revisi
                                </button>
                            </form>
                        </details>
                    @endcan

                    <!-- Approve -->
                    @can('approve', $article)
                        <form action="{{ route('admin.articles.approve', $article) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2 px-3 bg-[#137333] hover:bg-[#0F5C28] text-white font-bold rounded transition-colors cursor-pointer flex items-center justify-center gap-1.5" onclick="return confirm('Setujui naskah artikel ini untuk diterbitkan?');">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Setujui Naskah (Approve)</span>
                            </button>
                        </form>
                    @endcan

                    <!-- Publish Now -->
                    @can('publish', $article)
                        <form action="{{ route('admin.articles.publish', $article) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2 px-3 bg-[#E50914] hover:bg-[#C8102E] text-white font-bold rounded transition-colors cursor-pointer flex items-center justify-center gap-1.5" onclick="return confirm('Terbitkan artikel ini sekarang ke publik?');">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Terbitkan Sekarang</span>
                            </button>
                        </form>
                    @endcan

                    <!-- Schedule -->
                    @can('schedule', $article)
                        <details class="bg-[#F9FAFB] border border-[#E5E7EB] p-3 rounded space-y-2">
                            <summary class="font-bold text-[#171717] cursor-pointer">
                                📅 Jadwalkan Publikasi
                            </summary>
                            <form action="{{ route('admin.articles.schedule', $article) }}" method="POST" class="pt-2 space-y-2">
                                @csrf
                                <input type="datetime-local" name="scheduled_at" required min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}" class="w-full text-xs border border-[#CCCCCC] rounded p-1.5 bg-white">
                                <button type="submit" class="w-full py-1.5 bg-[#171717] hover:bg-black text-white font-bold rounded cursor-pointer">
                                    Tetapkan Jadwal
                                </button>
                            </form>
                        </details>
                    @endcan

                    <!-- Unpublish -->
                    @can('unpublish', $article)
                        <form action="{{ route('admin.articles.unpublish', $article) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menarik artikel ini dari publikasi publik?');">
                            @csrf
                            <button type="submit" class="w-full py-1.5 px-3 bg-[#FFF8E1] hover:bg-[#FFE082] text-[#B78103] border border-[#FFE082] font-bold rounded cursor-pointer">
                                Tarik Publikasi (Unpublish)
                            </button>
                        </form>
                    @endcan
                </div>
            </x-admin.card>

            <!-- Editorial Action Timeline -->
            <x-admin.card title="Riwayat Alur Redaksi (Timeline)" subtitle="Peristiwa status dan catatan editor">
                <div class="space-y-4">
                    @forelse ($article->editorialActions as $action)
                        <div class="flex gap-3 text-xs relative pb-4 border-b border-[#F3F4F6] last:border-0 last:pb-0">
                            <div class="w-2 h-2 rounded-full bg-[#E50914] mt-1.5 shrink-0"></div>
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold text-[#171717] uppercase tracking-wide text-[11px]">{{ $action->action }}</span>
                                    <span class="text-[10px] text-[#9CA3AF] whitespace-nowrap">{{ $action->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="text-[#6B7280] block text-[11px]">Oleh: <strong>{{ $action->user?->name ?? 'Sistem' }}</strong></span>
                                @if ($action->note)
                                    <p class="p-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded text-[11px] text-[#171717] italic">
                                        "{{ $action->note }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[#9CA3AF] italic">Belum ada catatan aktivitas redaksi.</p>
                    @endforelse
                </div>
            </x-admin.card>
        </div>
    </div>
</div>
@endsection
