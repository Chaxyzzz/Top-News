@extends('layouts.admin')

@section('title', 'Ringkasan Interaksi & Keterlibatan Pembaca')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Ringkasan Interaksi Pembaca
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Ikhtisar metrik keterlibatan pembaca, aktivitas simpan artikel, respon, dan status moderasi komentar.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.comments.index') }}" class="px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-[4px] transition-colors shadow-xs">
                Kelola Komentar
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-[6px] border border-[#E8E8E8] shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Komentar Menunggu</span>
            <div class="flex items-baseline justify-between">
                <span class="font-headline font-black text-3xl text-amber-600">{{ $overview['pending_comments'] }}</span>
                <span class="text-xs text-neutral-400">Butuh tinjauan</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[6px] border border-[#E8E8E8] shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Komentar Disetujui</span>
            <div class="flex items-baseline justify-between">
                <span class="font-headline font-black text-3xl text-emerald-600">{{ $overview['approved_comments'] }}</span>
                <span class="text-xs text-neutral-400">Tayang publik</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[6px] border border-[#E8E8E8] shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Total Artikel Tersimpan</span>
            <div class="flex items-baseline justify-between">
                <span class="font-headline font-black text-3xl text-blue-600">{{ $overview['total_bookmarks'] }}</span>
                <span class="text-xs text-neutral-400">Bookmark pembaca</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[6px] border border-[#E8E8E8] shadow-xs space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700">Total Respon Pembaca</span>
            <div class="flex items-baseline justify-between">
                <span class="font-headline font-black text-3xl text-purple-600">{{ $overview['total_reactions'] }}</span>
                <span class="text-xs text-neutral-400">Tanggapan artikel</span>
            </div>
        </div>
    </div>

    <!-- Leaderboards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Most Bookmarked Articles -->
        <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
            <div class="p-4 border-b border-[#E8E8E8] bg-[#FAFAFA]">
                <h3 class="font-headline font-bold text-sm text-[#111111]">
                    Artikel Paling Banyak Disimpan (Bookmarked)
                </h3>
            </div>
            @if ($overview['most_bookmarked']->count() > 0)
                <div class="divide-y divide-[#E8E8E8]">
                    @foreach ($overview['most_bookmarked'] as $art)
                        <div class="p-4 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('news.show', $art->slug) }}" target="_blank" class="font-bold text-[#111111] hover:text-red-600 truncate block">
                                    {{ $art->title }}
                                </a>
                                <span class="text-[11px] text-[#80868B]">
                                    Terbit {{ $art->published_at?->format('d/m/Y') }}
                                </span>
                            </div>
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded shrink-0">
                                {{ $art->bookmarks_count }} simpan
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-xs text-[#80868B]">
                    Belum ada data artikel yang disimpan oleh pembaca.
                </div>
            @endif
        </div>

        <!-- Most Commented Articles -->
        <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
            <div class="p-4 border-b border-[#E8E8E8] bg-[#FAFAFA]">
                <h3 class="font-headline font-bold text-sm text-[#111111]">
                    Artikel dengan Diskusi Teraktif (Approved Comments)
                </h3>
            </div>
            @if ($overview['most_commented']->count() > 0)
                <div class="divide-y divide-[#E8E8E8]">
                    @foreach ($overview['most_commented'] as $art)
                        <div class="p-4 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('news.show', $art->slug) }}#comments" target="_blank" class="font-bold text-[#111111] hover:text-red-600 truncate block">
                                    {{ $art->title }}
                                </a>
                                <span class="text-[11px] text-[#80868B]">
                                    Terbit {{ $art->published_at?->format('d/m/Y') }}
                                </span>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded shrink-0">
                                {{ $art->comments_count }} komentar
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-xs text-[#80868B]">
                    Belum ada data diskusi komentar yang disetujui.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
