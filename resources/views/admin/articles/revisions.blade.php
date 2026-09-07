@extends('layouts.admin')

@section('title', 'Riwayat Revisi: ' . $article->title)

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Riwayat Revisi & Snapshot Naskah" 
        :description="'Riwayat snapshot naskah untuk: ' . $article->title"
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Artikel Berita', 'url' => route('admin.articles.index')],
                ['label' => $article->title, 'url' => route('admin.articles.edit', $article)],
                ['label' => 'Riwayat Revisi']
            ]" />
        </x-slot>

        <x-slot name="actions">
            <x-button variant="outline" size="sm" :href="route('admin.articles.edit', $article)">
                ← Kembali ke Editor Naskah
            </x-button>
        </x-slot>
    </x-admin.page-header>

    <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-subtle overflow-hidden">
        @if ($revisions->isNotEmpty())
            <div class="divide-y divide-[#E5E7EB]">
                @foreach ($revisions as $rev)
                    <div class="p-5 hover:bg-[#F9FAFB] transition-colors space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-1 bg-[#171717] text-white font-mono font-bold text-xs rounded">
                                    Revisi #{{ $rev->revision_number }}
                                </span>
                                <span class="text-xs text-[#6B7280]">
                                    Disimpan oleh: <strong class="text-[#171717]">{{ $rev->user?->name ?? 'Sistem' }}</strong>
                                </span>
                                <span class="text-xs text-[#9CA3AF]">
                                    ({{ $rev->created_at->format('d/m/Y H:i:s') }} WIB - {{ $rev->created_at->diffForHumans() }})
                                </span>
                            </div>

                            @can('update', $article)
                                <form action="{{ route('admin.articles.revisions.restore', [$article, $rev]) }}" method="POST" onsubmit="return confirm('Pulihkan naskah artikel ke versi Revisi #{{ $rev->revision_number }}?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-[#F3F4F6] hover:bg-[#E5E7EB] text-[#171717] font-bold text-xs rounded transition-colors cursor-pointer flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <span>Pulihkan Versi Ini</span>
                                    </button>
                                </form>
                            @endcan
                        </div>

                        <div>
                            <h4 class="font-headline font-bold text-sm text-[#171717]">{{ $rev->title }}</h4>
                            @if ($rev->subtitle)
                                <p class="text-xs text-[#6B7280] mt-0.5">{{ $rev->subtitle }}</p>
                            @endif
                        </div>

                        <!-- Snapshot preview collapsible -->
                        <details class="text-xs bg-[#F9FAFB] p-3 rounded border border-[#E5E7EB]">
                            <summary class="font-semibold text-[#E50914] cursor-pointer hover:underline">
                                Lihat Cuplikan Konten Snapshot (Revisi #{{ $rev->revision_number }})
                            </summary>
                            <div class="mt-3 pt-3 border-t border-[#E5E7EB] prose max-w-none text-xs leading-relaxed">
                                {!! App\Services\ContentSanitizerService::sanitize($rev->content) !!}
                            </div>
                        </details>
                    </div>
                @endforeach
            </div>
        @else
            <x-admin.empty-state 
                title="Belum Ada Snapshot Riwayat" 
                description="Snapshot revisi akan dibuat secara otomatis setiap kali naskah disimpan atau diterbitkan."
            />
        @endif
    </div>
</div>
@endsection
