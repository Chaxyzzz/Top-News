@extends('layouts.app')

@section('title', $hasQuery ? 'Hasil Pencarian: ' . e($query) : 'Pencarian Berita')
@section('meta_description', 'Cari berita terkini, investigasi mendalam, opini, dan arsip liputan jurnalistik TopNews.')

@section('content')
<div class="tn-container py-8 pb-16">
    <x-breadcrumb :items="[['label' => 'Pencarian', 'url' => null]]" />

    {{-- Search Form Box --}}
    <div class="bg-neutral-50 border border-neutral-200 rounded-lg p-6 md:p-8 mb-8 shadow-xs">
        <form action="{{ route('search') }}" method="GET" class="space-y-4">
            <div>
                <label for="search-page-input" class="block font-headline font-bold text-sm uppercase tracking-wider text-neutral-900 mb-2">
                    Cari Berita & Arsip TopNews
                </label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <input
                            id="search-page-input"
                            name="q"
                            type="search"
                            value="{{ $query ?? '' }}"
                            placeholder="Ketik topik, isu penting, atau kata kunci (min. 2 karakter)..."
                            class="w-full bg-white border border-neutral-300 text-neutral-900 text-sm md:text-base px-4 py-2.5 rounded-sm focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600"
                            required
                            minlength="2"
                            maxlength="200"
                        />
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-sm transition-colors cursor-pointer shadow-xs">
                        Cari Berita
                    </button>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="pt-4 border-t border-neutral-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                {{-- Category Filter --}}
                <div>
                    <label class="block font-semibold text-neutral-600 mb-1">Kategori</label>
                    <select name="category" onchange="this.form.submit()" class="w-full bg-white border border-neutral-300 rounded-sm py-1.5 px-2 text-xs focus:outline-none focus:border-red-600">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ $selectedCategory == $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Content Type Filter --}}
                <div>
                    <label class="block font-semibold text-neutral-600 mb-1">Format Konten</label>
                    <select name="type" onchange="this.form.submit()" class="w-full bg-white border border-neutral-300 rounded-sm py-1.5 px-2 text-xs focus:outline-none focus:border-red-600">
                        <option value="">Semua Format</option>
                        @foreach(App\Enums\ArticleType::cases() as $typeCase)
                            <option value="{{ $typeCase->value }}" {{ $selectedType == $typeCase->value ? 'selected' : '' }}>
                                {{ $typeCase->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Filter --}}
                <div>
                    <label class="block font-semibold text-neutral-600 mb-1">Waktu Terbit</label>
                    <select name="date" onchange="this.form.submit()" class="w-full bg-white border border-neutral-300 rounded-sm py-1.5 px-2 text-xs focus:outline-none focus:border-red-600">
                        <option value="">Semua Waktu</option>
                        <option value="today" {{ $selectedDate === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="last_7_days" {{ $selectedDate === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="last_30_days" {{ $selectedDate === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        <option value="this_year" {{ $selectedDate === 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                </div>

                {{-- Sort Filter --}}
                <div>
                    <label class="block font-semibold text-neutral-600 mb-1">Urutan</label>
                    <select name="sort" onchange="this.form.submit()" class="w-full bg-white border border-neutral-300 rounded-sm py-1.5 px-2 text-xs focus:outline-none focus:border-red-600">
                        <option value="relevance" {{ $selectedSort === 'relevance' ? 'selected' : '' }}>Paling Relevan</option>
                        <option value="newest" {{ $selectedSort === 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ $selectedSort === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="most_read" {{ $selectedSort === 'most_read' ? 'selected' : '' }}>Paling Banyak Dibaca</option>
                    </select>
                </div>
            </div>

            {{-- Active Filter Reset --}}
            @if($selectedCategory || $selectedType || $selectedDate || ($selectedSort && $selectedSort !== 'relevance'))
                <div class="flex items-center gap-2 pt-1 text-xs">
                    <span class="text-neutral-500">Filter aktif:</span>
                    <a href="{{ route('search', ['q' => $query]) }}" class="text-red-600 hover:text-red-700 font-semibold flex items-center gap-1">
                        <span>✕ Reset Semua Filter</span>
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- Search Results Content --}}
    <div class="space-y-6">
        @if ($hasQuery)
            <div class="border-b border-neutral-200 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <h1 class="font-headline font-bold text-xl text-neutral-900">
                    Hasil pencarian untuk: <strong class="text-red-600">"{{ e($query) }}"</strong>
                </h1>
                <span class="text-xs font-semibold text-neutral-600 bg-neutral-100 px-2.5 py-1 rounded-sm">
                    {{ $results->total() }} berita ditemukan
                </span>
            </div>

            @if ($results->count() > 0)
                <div class="divide-y divide-neutral-200">
                    @foreach ($results as $item)
                        <article class="py-6 first:pt-0 flex flex-col md:flex-row gap-5 items-start">
                            {{-- Thumbnail --}}
                            @if($item->featured_image_url)
                                <a href="{{ route('news.show', $item->slug) }}" class="w-full md:w-56 shrink-0 aspect-16/10 rounded-sm bg-neutral-900 overflow-hidden block group">
                                    <img src="{{ $item->featured_image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </a>
                            @endif

                            {{-- Details --}}
                            <div class="flex-1 min-w-0 space-y-2">
                                <div class="flex items-center gap-2 text-xs">
                                    @if($item->category)
                                        <a href="{{ route('category.show', $item->category->slug) }}" class="font-bold text-red-600 hover:underline uppercase tracking-wider text-[11px]">
                                            {{ $item->category->name }}
                                        </a>
                                        <span class="text-neutral-300">•</span>
                                    @endif
                                    <span class="text-neutral-500 text-[11px] font-semibold">
                                        {{ $item->content_type->label() }}
                                    </span>
                                </div>

                                <h2 class="font-headline font-bold text-lg md:text-xl text-neutral-900 hover:text-red-600 transition-colors leading-snug">
                                    <a href="{{ route('news.show', $item->slug) }}">
                                        {{ $item->title }}
                                    </a>
                                </h2>

                                @if($item->excerpt)
                                    <p class="text-neutral-600 text-xs md:text-sm leading-relaxed line-clamp-2">
                                        {{ $item->excerpt }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-3 text-xs text-neutral-400 pt-1">
                                    @if($item->author)
                                        <span class="font-medium text-neutral-700">{{ $item->author->name }}</span>
                                        <span>•</span>
                                    @endif
                                    <span>{{ $item->published_at?->locale('id')->isoFormat('D MMMM Y') }}</span>
                                    <span>•</span>
                                    <span>{{ $item->reading_time }} mnt baca</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="pt-8 border-t border-neutral-200">
                    {{ $results->links() }}
                </div>
            @else
                {{-- Empty Search Results State --}}
                <div class="bg-neutral-50 border border-dashed border-neutral-300 rounded-lg p-12 text-center">
                    <svg class="w-12 h-12 text-neutral-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="font-headline font-bold text-base text-neutral-900 mb-1">Tidak Ada Berita Ditemukan</h3>
                    <p class="text-xs text-neutral-500 mb-6 max-w-md mx-auto leading-relaxed">
                        Kami tidak dapat menemukan berita publik yang cocok dengan kata kunci <strong>"{{ e($query) }}"</strong>. Silakan periksa kembali ejaan kata kunci Anda atau coba topik lain.
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('latest') }}" class="px-4 py-2 bg-neutral-900 hover:bg-neutral-800 text-white font-semibold text-xs rounded-sm transition-colors">
                            Lihat Berita Terbaru
                        </a>
                        <a href="{{ route('home') }}" class="px-4 py-2 bg-neutral-200 hover:bg-neutral-300 text-neutral-800 font-semibold text-xs rounded-sm transition-colors">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            @endif
        @else
            {{-- Default / Initial State --}}
            <div class="bg-neutral-50 border border-neutral-200 rounded-lg p-12 text-center">
                <svg class="w-12 h-12 text-neutral-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="font-headline font-bold text-base text-neutral-900 mb-1">Pusat Pencarian Berita</h3>
                <p class="text-xs text-neutral-500 max-w-sm mx-auto">
                    Ketik topik atau nama peristiwa pada kolom pencarian di atas untuk menelusuri seluruh arsip berita terbitan TopNews.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
