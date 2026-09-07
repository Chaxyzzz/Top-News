@extends('account.layout')

@section('title', 'Artikel Tersimpan — Ruang Pembaca TopNews')

@section('account_content')
<div class="bg-white border border-neutral-200 rounded-sm p-6 shadow-xs">
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-neutral-200">
        <div>
            <h2 class="font-headline font-bold text-lg text-neutral-900">Artikel Tersimpan</h2>
            <p class="text-xs text-neutral-500 mt-0.5">Daftar bacaan yang Anda simpan untuk dibaca nanti.</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 bg-neutral-100 text-neutral-700 rounded-sm">
            Total: {{ $bookmarks->total() }}
        </span>
    </div>

    @if ($bookmarks->count() > 0)
        <div class="divide-y divide-neutral-200">
            @foreach ($bookmarks as $bookmark)
                @if ($bookmark->article)
                    <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row gap-4 items-start justify-between">
                        <div class="flex gap-4 items-start flex-1 min-w-0">
                            @if ($bookmark->article->featured_image_url)
                                <a href="{{ route('news.show', $bookmark->article->slug) }}" class="w-24 sm:w-28 shrink-0 aspect-16/10 rounded-sm bg-neutral-900 overflow-hidden block">
                                    <img src="{{ $bookmark->article->featured_image_url }}" alt="{{ $bookmark->article->title }}" class="w-full h-full object-cover">
                                </a>
                            @endif
                            <div class="flex-1 min-w-0 space-y-1">
                                @if ($bookmark->article->category)
                                    <a href="{{ route('category.show', $bookmark->article->category->slug) }}" class="text-[11px] font-bold text-red-600 uppercase tracking-wider hover:underline">
                                        {{ $bookmark->article->category->name }}
                                    </a>
                                @endif
                                <h3 class="font-headline font-bold text-sm sm:text-base text-neutral-900 hover:text-red-600 transition-colors leading-snug">
                                    <a href="{{ route('news.show', $bookmark->article->slug) }}">
                                        {{ $bookmark->article->title }}
                                    </a>
                                </h3>
                                <div class="flex items-center gap-2 text-[11px] text-neutral-400">
                                    <span>Disimpan {{ $bookmark->created_at->locale('id')->diffForHumans() }}</span>
                                    <span>•</span>
                                    <span>{{ $bookmark->article->reading_time }} mnt baca</span>
                                </div>
                            </div>
                        </div>

                        {{-- Remove Bookmark Form --}}
                        <div class="shrink-0 pt-1">
                            <form action="{{ route('bookmarks.destroy', $bookmark) }}" method="POST" onsubmit="return confirm('Hapus artikel ini dari daftar simpanan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-neutral-400 hover:text-red-600 flex items-center gap-1 font-medium transition-colors cursor-pointer" title="Hapus dari simpanan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="pt-6 border-t border-neutral-200 mt-6">
            {{ $bookmarks->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-neutral-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
            <h3 class="font-headline font-bold text-sm text-neutral-900 mb-1">Belum Ada Artikel Tersimpan</h3>
            <p class="text-xs text-neutral-500 mb-4 max-w-sm mx-auto">
                Temukan liputan jurnalistik yang menarik di TopNews dan klik tombol "Simpan" pada artikel untuk membacanya nanti.
            </p>
            <a href="{{ route('latest') }}" class="inline-block px-4 py-2 bg-neutral-900 hover:bg-neutral-800 text-white font-semibold text-xs rounded-sm transition-colors">
                Eksplorasi Berita Terkini
            </a>
        </div>
    @endif
</div>
@endsection
