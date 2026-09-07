@extends('layouts.admin')

@section('title', 'Alur Redaksi & Newsroom Pipeline')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Alur Redaksi & Antrean Newsroom" 
        description="Pusat orkestrasi editorial berita TopNews. Mengelola naskah dari antrean peninjauan meja redaksi hingga persetujuan dan penjadwalan tayang."
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Newsroom Pipeline']
            ]" />
        </x-slot>

        <x-slot name="actions">
            @can('create', App\Models\Article::class)
                <x-button variant="primary" size="sm" :href="route('admin.articles.create')">
                    + Tulis Artikel Baru
                </x-button>
            @endcan
        </x-slot>
    </x-admin.page-header>

    <!-- Newsroom Queue Counter Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-[6px] border border-[#E5E7EB] shadow-subtle">
            <span class="text-[10px] font-extrabold uppercase text-[#B78103] block mb-1">Menunggu Review</span>
            <span class="font-headline font-black text-2xl text-[#171717]">{{ $counts['needs_review'] }}</span>
        </div>

        <div class="bg-white p-4 rounded-[6px] border border-[#E5E7EB] shadow-subtle">
            <span class="text-[10px] font-extrabold uppercase text-[#1E3A8A] block mb-1">Sedang Ditinjau</span>
            <span class="font-headline font-black text-2xl text-[#171717]">{{ $counts['in_review'] }}</span>
        </div>

        <div class="bg-white p-4 rounded-[6px] border border-[#E5E7EB] shadow-subtle">
            <span class="text-[10px] font-extrabold uppercase text-[#C8102E] block mb-1">Perlu Revisi</span>
            <span class="font-headline font-black text-2xl text-[#171717]">{{ $counts['revision_requested'] }}</span>
        </div>

        <div class="bg-white p-4 rounded-[6px] border border-[#E5E7EB] shadow-subtle">
            <span class="text-[10px] font-extrabold uppercase text-[#137333] block mb-1">Disetujui (Approved)</span>
            <span class="font-headline font-black text-2xl text-[#171717]">{{ $counts['approved'] }}</span>
        </div>

        <div class="bg-white p-4 rounded-[6px] border border-[#E5E7EB] shadow-subtle">
            <span class="text-[10px] font-extrabold uppercase text-[#6D28D9] block mb-1">Terjadwal Tayang</span>
            <span class="font-headline font-black text-2xl text-[#171717]">{{ $counts['scheduled'] }}</span>
        </div>
    </div>

    <!-- Queue Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <!-- 1. Needs Review Queue (Oldest Submitted First) -->
        <x-admin.card title="Antrean Menunggu Review (Needs Review)" subtitle="Naskah yang diajukan wartawan dan menunggu peninjauan editor">
            @if ($needsReviewArticles->isNotEmpty())
                <div class="divide-y divide-[#F3F4F6]">
                    @foreach ($needsReviewArticles as $art)
                        <div class="py-3 flex items-start justify-between gap-3 text-xs">
                            <div class="space-y-1">
                                <span class="font-bold text-[10px] px-1.5 py-0.5 rounded" style="color: {{ $art->category->accent_color ?? '#171717' }}; background-color: {{ $art->category->accent_color ?? '#171717' }}15;">
                                    {{ $art->category->name }}
                                </span>
                                <a href="{{ route('admin.articles.show', $art) }}" class="font-headline font-bold text-xs text-[#171717] hover:text-[#E50914] block leading-snug">
                                    {{ $art->title }}
                                </a>
                                <div class="text-[11px] text-[#6B7280]">
                                    Oleh: <strong>{{ $art->author?->name ?? '-' }}</strong> • Diajukan: {{ $art->submitted_at?->diffForHumans() }}
                                </div>
                            </div>

                            @can('startReview', $art)
                                <form action="{{ route('admin.articles.review.start', $art) }}" method="POST" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-[#1E3A8A] hover:bg-[#1E40AF] text-white text-[11px] font-bold rounded cursor-pointer">
                                        Mulai Tinjau
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @else
                <x-admin.empty-state 
                    title="Tidak Ada Naskah Menunggu" 
                    description="Semua naskah yang diajukan telah diproses oleh tim redaksi."
                />
            @endif
        </x-admin.card>

        <!-- 2. In Review Queue -->
        <x-admin.card title="Sedang Ditinjau Editor (In Review)" subtitle="Naskah yang sedang dalam tahap telaah dan verifikasi fakta">
            @if ($inReviewArticles->isNotEmpty())
                <div class="divide-y divide-[#F3F4F6]">
                    @foreach ($inReviewArticles as $art)
                        <div class="py-3 flex items-start justify-between gap-3 text-xs">
                            <div class="space-y-1">
                                <a href="{{ route('admin.articles.show', $art) }}" class="font-headline font-bold text-xs text-[#171717] hover:text-[#E50914] block leading-snug">
                                    {{ $art->title }}
                                </a>
                                <div class="text-[11px] text-[#6B7280]">
                                    Penulis: <strong>{{ $art->author?->name }}</strong> • Editor: <strong class="text-[#1E3A8A]">{{ $art->editor?->name ?? '-' }}</strong>
                                </div>
                            </div>

                            <a href="{{ route('admin.articles.show', $art) }}" class="px-2.5 py-1 bg-[#F3F4F6] hover:bg-[#E5E7EB] text-[#171717] text-[11px] font-bold rounded shrink-0">
                                Keputusan →
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <x-admin.empty-state 
                    title="Tidak Ada Naskah Ditinjau" 
                    description="Editor dapat memilih naskah dari antrean menunggu review untuk mulai meninjau."
                />
            @endif
        </x-admin.card>

        <!-- 3. Approved Queue (Ready to Publish/Schedule) -->
        <x-admin.card title="Naskah Disetujui (Approved)" subtitle="Siap untuk langsung diterbitkan atau dijadwalkan tayang">
            @if ($approvedArticles->isNotEmpty())
                <div class="divide-y divide-[#F3F4F6]">
                    @foreach ($approvedArticles as $art)
                        <div class="py-3 flex items-start justify-between gap-3 text-xs">
                            <div class="space-y-1">
                                <a href="{{ route('admin.articles.show', $art) }}" class="font-headline font-bold text-xs text-[#171717] hover:text-[#E50914] block leading-snug">
                                    {{ $art->title }}
                                </a>
                                <div class="text-[11px] text-[#6B7280]">
                                    Disetujui oleh: <strong>{{ $art->editor?->name ?? 'Pemred' }}</strong> • {{ $art->approved_at?->diffForHumans() }}
                                </div>
                            </div>

                            @can('publish', $art)
                                <form action="{{ route('admin.articles.publish', $art) }}" method="POST" class="shrink-0" onsubmit="return confirm('Terbitkan sekarang?');">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-[#E50914] hover:bg-[#C8102E] text-white text-[11px] font-bold rounded cursor-pointer">
                                        Terbitkan
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @else
                <x-admin.empty-state 
                    title="Tidak Ada Naskah Siap Terbit" 
                    description="Naskah yang telah disetujui akan tampil di sini untuk diterbitkan."
                />
            @endif
        </x-admin.card>

        <!-- 4. Scheduled Queue -->
        <x-admin.card title="Antrean Terjadwal (Scheduled)" subtitle="Artikel yang akan tayang otomatis pada waktu yang ditentukan">
            @if ($scheduledArticles->isNotEmpty())
                <div class="divide-y divide-[#F3F4F6]">
                    @foreach ($scheduledArticles as $art)
                        <div class="py-3 flex items-start justify-between gap-3 text-xs">
                            <div class="space-y-1">
                                <a href="{{ route('admin.articles.show', $art) }}" class="font-headline font-bold text-xs text-[#171717] hover:text-[#E50914] block leading-snug">
                                    {{ $art->title }}
                                </a>
                                <div class="text-[11px] text-[#6D28D9] font-bold">
                                    Tayang: {{ $art->scheduled_at?->format('d/m/Y H:i') }} WIB ({{ $art->scheduled_at?->diffForHumans() }})
                                </div>
                            </div>

                            <a href="{{ route('admin.articles.edit', $art) }}" class="px-2.5 py-1 bg-[#F3F4F6] hover:bg-[#E5E7EB] text-[#171717] text-[11px] font-bold rounded shrink-0">
                                Ubah Jadwal
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <x-admin.empty-state 
                    title="Tidak Ada Artikel Terjadwal" 
                    description="Gunakan fitur penjadwalan saat menyetujui artikel untuk tayang otomatis di masa mendatang."
                />
            @endif
        </x-admin.card>
    </div>
</div>
@endsection
