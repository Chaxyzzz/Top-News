@extends('layouts.admin')

@section('title', 'Kurasi Berita: ' . $section->title)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.homepage.index') }}" class="text-xs font-semibold text-[#5F6368] hover:text-[#111111] flex items-center gap-1 mb-1">
                ← Kembali ke Manajemen Beranda
            </a>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Kurasi Berita: {{ $section->title }}
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Tentukan secara tepat naskah berita yang tampil pada bagian ini dan atur urutan penayangannya.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Selected Curated Articles List (Left 5 Cols) --}}
        <div class="lg:col-span-5 bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
            <div class="p-4 border-b border-[#E8E8E8] bg-[#FAFAFA] flex items-center justify-between">
                <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">
                    Artikel Terpilih (<span id="selected-count">{{ $curatedArticles->count() }}</span> / {{ $section->item_limit }})
                </span>
                <span class="text-[11px] text-[#80868B]">Maksimal {{ $section->item_limit }} artikel</span>
            </div>

            <form action="{{ route('admin.homepage.curate.update', $section) }}" method="POST" id="curate-form">
                @csrf
                <div class="p-4 space-y-3 min-h-[300px]" id="selected-list">
                    @forelse ($curatedArticles as $art)
                        <div class="curated-item p-3 bg-[#F9FAFB] border border-[#E8E8E8] rounded-[4px] flex items-center justify-between gap-3 text-xs" data-id="{{ $art->id }}">
                            <input type="hidden" name="article_ids[]" value="{{ $art->id }}">
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-[#111111] line-clamp-1">{{ $art->title }}</span>
                                <div class="flex items-center gap-2 text-[10px] text-[#80868B] mt-0.5">
                                    <span>{{ $art->category?->name }}</span>
                                    <span>•</span>
                                    <span>{{ $art->published_at?->format('d/m/Y') }}</span>
                                    @if(! $art->isPublished())
                                        <span class="text-amber-700 bg-amber-100 px-1 rounded font-bold">Tidak Tayang</span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="text-neutral-400 hover:text-red-600 font-bold px-2 py-1 cursor-pointer" onclick="removeCuratedItem(this)">
                                ✕
                            </button>
                        </div>
                    @empty
                        <div id="empty-curated-msg" class="text-center py-10 text-xs text-[#80868B]">
                            Belum ada artikel yang dipilih untuk bagian ini. Silakan pilih dari daftar pencarian di sebelah kanan.
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-[#E8E8E8] bg-[#FAFAFA] flex items-center justify-between">
                    <span class="text-[11px] text-[#80868B]">Perubahan langsung aktif di beranda.</span>
                    <button type="submit" class="px-4 py-2 bg-[#E50914] hover:bg-[#B80710] text-white text-xs font-bold rounded-[4px] transition-colors cursor-pointer">
                        Simpan Pilihan Kurasi
                    </button>
                </div>
            </form>
        </div>

        {{-- Candidate Articles Search & Selector (Right 7 Cols) --}}
        <div class="lg:col-span-7 bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
            <div class="p-4 border-b border-[#E8E8E8] bg-[#FAFAFA]">
                <form action="{{ route('admin.homepage.curate', $section) }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari judul artikel terbit..." 
                        class="flex-1 text-xs bg-white border border-[#CCCCCC] rounded-[4px] px-3 py-1.5 focus:border-[#E50914] focus:outline-none"
                    />
                    <select name="category_id" class="text-xs bg-white border border-[#CCCCCC] rounded-[4px] px-2 py-1.5 focus:border-[#E50914] focus:outline-none">
                        <option value="">Semua Rubrik</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-[#111111] hover:bg-black text-white text-xs font-bold rounded-[4px] transition-colors cursor-pointer">
                        Cari
                    </button>
                </form>
            </div>

            <div class="divide-y divide-[#E8E8E8] max-h-[550px] overflow-y-auto">
                @forelse ($candidates as $cand)
                    <div class="p-3.5 hover:bg-[#F9FAFB] flex items-center justify-between gap-3 text-xs transition-colors">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('news.show', $cand->slug) }}" target="_blank" class="font-bold text-[#111111] hover:text-[#E50914] line-clamp-1">
                                {{ $cand->title }}
                            </a>
                            <div class="flex items-center gap-2 text-[10px] text-[#80868B] mt-0.5">
                                <span class="font-semibold text-neutral-700">{{ $cand->category?->name }}</span>
                                <span>•</span>
                                <span>Oleh {{ $cand->author?->name ?? 'Redaksi' }}</span>
                                <span>•</span>
                                <span>{{ $cand->published_at?->format('d M Y') }}</span>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            class="px-2.5 py-1 bg-white border border-[#CCCCCC] hover:border-emerald-600 hover:text-emerald-700 font-bold text-[11px] rounded transition-colors shrink-0 cursor-pointer"
                            onclick="addCuratedItem({{ $cand->id }}, '{{ addslashes($cand->title) }}', '{{ addslashes($cand->category?->name ?? '') }}', '{{ $cand->published_at?->format('d/m/Y') }}')"
                        >
                            + Tambahkan
                        </button>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-[#80868B]">
                        Tidak ada artikel publik yang cocok dengan kriteria pencarian.
                    </div>
                @endforelse
            </div>

            <div class="p-3 border-t border-[#E8E8E8]">
                {{ $candidates->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    const maxLimit = {{ $section->item_limit }};

    function removeCuratedItem(btn) {
        const item = btn.closest('.curated-item');
        item.remove();
        updateCount();
    }

    function addCuratedItem(id, title, category, date) {
        const list = document.getElementById('selected-list');
        const emptyMsg = document.getElementById('empty-curated-msg');
        if (emptyMsg) emptyMsg.remove();

        // Check if already in list
        const existing = list.querySelector(`.curated-item[data-id="${id}"]`);
        if (existing) {
            alert('Artikel ini sudah ada dalam daftar kurasi.');
            return;
        }

        const currentCount = list.querySelectorAll('.curated-item').length;
        if (currentCount >= maxLimit) {
            alert(`Batas maksimal untuk bagian ini adalah ${maxLimit} artikel.`);
            return;
        }

        const div = document.createElement('div');
        div.className = 'curated-item p-3 bg-[#F9FAFB] border border-[#E8E8E8] rounded-[4px] flex items-center justify-between gap-3 text-xs';
        div.setAttribute('data-id', id);
        div.innerHTML = `
            <input type="hidden" name="article_ids[]" value="${id}">
            <div class="min-w-0 flex-1">
                <span class="font-bold text-[#111111] line-clamp-1">${title}</span>
                <div class="flex items-center gap-2 text-[10px] text-[#80868B] mt-0.5">
                    <span>${category}</span>
                    <span>•</span>
                    <span>${date}</span>
                </div>
            </div>
            <button type="button" class="text-neutral-400 hover:text-red-600 font-bold px-2 py-1 cursor-pointer" onclick="removeCuratedItem(this)">
                ✕
            </button>
        `;

        list.appendChild(div);
        updateCount();
    }

    function updateCount() {
        const count = document.querySelectorAll('.curated-item').length;
        document.getElementById('selected-count').innerText = count;
    }
</script>
@endsection
