@extends('layouts.admin')

@section('title', 'Sunting: ' . $article->title)

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Sunting Artikel & Naskah" 
        :description="'Mengubah naskah: ' . $article->title"
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Artikel Berita', 'url' => route('admin.articles.index')],
                ['label' => 'Sunting Naskah']
            ]" />
        </x-slot>

        <x-slot name="actions">
            <!-- Preview Action -->
            <a href="{{ route('admin.articles.preview', $article) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#4B5563] hover:text-[#171717] bg-white border border-[#CCCCCC] rounded-[4px] shadow-2xs hover:bg-[#F3F4F6] transition-colors">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>Pratinjau (Preview)</span>
            </a>

            <!-- Revision History -->
            <a href="{{ route('admin.articles.revisions.index', $article) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#4B5563] hover:text-[#171717] bg-white border border-[#CCCCCC] rounded-[4px] shadow-2xs hover:bg-[#F3F4F6] transition-colors">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Riwayat Revisi ({{ $article->revisions()->count() }})</span>
            </a>

            <!-- Review & Timeline Details -->
            <a href="{{ route('admin.articles.show', $article) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#171717] bg-[#F3F4F6] border border-[#E5E7EB] rounded-[4px] hover:bg-[#E5E7EB] transition-colors">
                <span>Alur & Catatan Redaksi</span>
            </a>
        </x-slot>
    </x-admin.page-header>

    <!-- Revision Request Alert Banner (if applicable) -->
    @if ($article->status === App\Enums\ArticleStatus::RevisionRequested)
        @php
            $latestRevisionAction = $article->editorialActions()->where('action', 'revision_requested')->first();
        @endphp
        <div class="p-4 rounded-[6px] bg-[#FFF8E1] border border-[#FFE082] text-xs text-[#B78103] shadow-xs space-y-1">
            <div class="flex items-center gap-2 font-bold">
                <svg class="w-4 h-4 text-[#B78103] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>PERMINTAAN REVISI DARI EDITOR:</span>
            </div>
            <p class="text-xs text-[#171717] pl-6 font-medium leading-relaxed">
                "{{ $latestRevisionAction?->note ?? 'Mohon perbaiki dan lengkapi naskah artikel sebelum diajukan kembali.' }}"
            </p>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-[6px] bg-[#FDE8E9] border border-[#E50914] text-xs text-[#C8102E]">
            <span class="font-bold block mb-1">Harap perbaiki kesalahan berikut:</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Main Form Column (Cols 1-8) -->
        <div class="lg:col-span-8 space-y-6">
            <form id="article-form" action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Title & Subtitle Card -->
                <x-admin.card>
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-xs font-bold uppercase tracking-wider text-[#171717] mb-1">
                                Judul Utama Berita (Headline) <span class="text-[#E50914]">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                value="{{ old('title', $article->title) }}" 
                                required 
                                class="w-full font-headline font-bold text-lg sm:text-xl border border-[#CCCCCC] rounded-[4px] p-3 focus:outline-none focus:border-[#E50914]"
                            />
                        </div>

                        <div>
                            <label for="slug" class="block text-xs font-bold uppercase tracking-wider text-[#6B7280] mb-1">
                                Slug URL Artikel
                            </label>
                            <input 
                                type="text" 
                                name="slug" 
                                id="slug" 
                                value="{{ old('slug', $article->slug) }}" 
                                @if($article->status === App\Enums\ArticleStatus::Published && !Auth::user()->hasPermission('articles.change_published_slug')) readonly @endif
                                class="w-full text-xs font-mono border border-[#CCCCCC] rounded-[4px] p-2 bg-[#F9FAFB] focus:outline-none focus:border-[#E50914]"
                            />
                            @if($article->status === App\Enums\ArticleStatus::Published)
                                <span class="text-[10px] text-[#9CA3AF] block mt-0.5">Slug dikunci untuk stabilitas SEO artikel terbit.</span>
                            @endif
                        </div>

                        <div>
                            <label for="subtitle" class="block text-xs font-bold uppercase tracking-wider text-[#6B7280] mb-1">
                                Subjudul / Deck (Opsional)
                            </label>
                            <input 
                                type="text" 
                                name="subtitle" 
                                id="subtitle" 
                                value="{{ old('subtitle', $article->subtitle) }}" 
                                class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2.5 focus:outline-none focus:border-[#E50914]"
                            />
                        </div>
                    </div>
                </x-admin.card>

                <!-- Rich Body Editor Card -->
                <x-admin.card title="Isi Naskah Berita (Content)">
                    <div class="space-y-3">
                        <div class="flex items-center gap-1.5 p-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-[4px] flex-wrap text-xs">
                            <button type="button" onclick="formatDoc('formatBlock', '<h2>')" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded font-bold hover:bg-[#F3F4F6]" title="Heading 2">H2</button>
                            <button type="button" onclick="formatDoc('formatBlock', '<h3>')" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded font-bold hover:bg-[#F3F4F6]" title="Heading 3">H3</button>
                            <button type="button" onclick="formatDoc('bold')" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded font-black hover:bg-[#F3F4F6]" title="Tebal (Bold)">B</button>
                            <button type="button" onclick="formatDoc('italic')" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded italic hover:bg-[#F3F4F6]" title="Miring (Italic)">I</button>
                            <button type="button" onclick="formatDoc('insertUnorderedList')" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded hover:bg-[#F3F4F6]" title="Poin (Bullet List)">• List</button>
                            <button type="button" onclick="formatDoc('insertOrderedList')" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded hover:bg-[#F3F4F6]" title="Nomor (Numbered List)">1. List</button>
                            <button type="button" onclick="formatDoc('formatBlock', '<blockquote>')" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded italic hover:bg-[#F3F4F6]" title="Kutipan (Blockquote)">“ Kutipan</button>
                            <button type="button" onclick="createLinkPrompt()" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded hover:bg-[#F3F4F6]" title="Tautan (Link)">🔗 Link</button>
                            <button type="button" onclick="formatDoc('removeFormat')" class="px-2 py-1 bg-white border border-[#CCCCCC] rounded text-[#9CA3AF] hover:text-[#171717]" title="Hapus Format">Bersihkan</button>
                        </div>

                        <div 
                            id="editor-surface" 
                            contenteditable="true" 
                            class="min-h-[400px] max-h-[700px] overflow-y-auto p-4 border border-[#CCCCCC] rounded-[4px] focus:outline-none focus:border-[#E50914] text-sm leading-relaxed prose max-w-none bg-white"
                        >{!! old('content', $article->content) !!}</div>

                        <textarea name="content" id="content-hidden" class="hidden">{{ old('content', $article->content) }}</textarea>

                        <div class="flex items-center justify-between text-xs text-[#6B7280] pt-1">
                            <span id="word-count">0 kata</span>
                            <span id="reading-time-est">~{{ $article->reading_time }} menit estimasi baca</span>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Excerpt / Ringkasan -->
                <x-admin.card title="Kutipan Ringkas (Excerpt)">
                    <div>
                        <textarea 
                            name="excerpt" 
                            id="excerpt" 
                            rows="3" 
                            class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2.5 focus:outline-none focus:border-[#E50914]"
                        >{{ old('excerpt', $article->excerpt) }}</textarea>
                    </div>
                </x-admin.card>

                <!-- Basic SEO Accordion -->
                <x-admin.card title="Optimasi Mesin Pencari (SEO)" subtitle="Pengaturan metadata Google / OpenGraph">
                    <div class="space-y-3">
                        <div>
                            <label for="seo_title" class="block text-xs font-semibold text-[#6B7280] mb-1">SEO Title (Opsional)</label>
                            <input type="text" name="seo_title" id="seo_title" value="{{ old('seo_title', $article->seo_title) }}" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2 focus:outline-none focus:border-[#E50914]" />
                        </div>

                        <div>
                            <label for="seo_description" class="block text-xs font-semibold text-[#6B7280] mb-1">Meta Description (Opsional)</label>
                            <textarea name="seo_description" id="seo_description" rows="2" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2 focus:outline-none focus:border-[#E50914]">{{ old('seo_description', $article->seo_description) }}</textarea>
                        </div>

                        <div>
                            <label for="canonical_url" class="block text-xs font-semibold text-[#6B7280] mb-1">Canonical URL (Opsional)</label>
                            <input type="url" name="canonical_url" id="canonical_url" value="{{ old('canonical_url', $article->canonical_url) }}" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2 focus:outline-none focus:border-[#E50914]" />
                        </div>
                    </div>
                </x-admin.card>
            </form>
        </div>

        <!-- Sidebar / Workflow Controls (Cols 9-12) -->
        <div class="lg:col-span-4 space-y-6 sticky top-20">
            <!-- Publishing Actions Box -->
            <x-admin.card title="Status & Aksi Redaksi">
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-1 border-b border-[#F3F4F6] text-xs">
                        <span class="text-[#6B7280]">Status Redaksi:</span>
                        <x-admin.badge :variant="$article->status->badgeVariant()" size="xs">
                            {{ $article->status->label() }}
                        </x-admin.badge>
                    </div>

                    <!-- Save Changes Button -->
                    <div>
                        <button type="submit" form="article-form" class="w-full py-2.5 px-4 bg-[#171717] hover:bg-black text-white font-bold text-xs rounded-[4px] transition-colors cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <span>Simpan Pembaruan</span>
                        </button>
                    </div>

                    <!-- Direct State Machine Actions -->
                    <div class="pt-2 border-t border-[#F3F4F6] space-y-2">
                        <!-- Submit for Review (Draft / Revision Requested) -->
                        @can('submit', $article)
                            <form action="{{ route('admin.articles.submit', $article) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 bg-[#E50914] hover:bg-[#C8102E] text-white font-bold text-xs rounded-[4px] transition-colors cursor-pointer flex items-center justify-center gap-1.5" onclick="return confirm('Ajukan naskah artikel ini ke meja redaksi untuk ditinjau?');">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Kirim untuk Review Redaksi</span>
                                </button>
                            </form>
                        @endcan

                        <!-- Start Review (Submitted) -->
                        @can('startReview', $article)
                            <form action="{{ route('admin.articles.review.start', $article) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 bg-[#1E3A8A] hover:bg-[#1E40AF] text-white font-bold text-xs rounded-[4px] transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                                    <span>Mulai Tinjau Naskah</span>
                                </button>
                            </form>
                        @endcan

                        <!-- Approve (In Review / Submitted) -->
                        @can('approve', $article)
                            <form action="{{ route('admin.articles.approve', $article) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 bg-[#137333] hover:bg-[#0F5C28] text-white font-bold text-xs rounded-[4px] transition-colors cursor-pointer flex items-center justify-center gap-1.5" onclick="return confirm('Setujui naskah artikel ini untuk diterbitkan?');">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Setujui Naskah (Approve)</span>
                                </button>
                            </form>
                        @endcan

                        <!-- Publish Immediately (Approved / Scheduled) -->
                        @can('publish', $article)
                            <form action="{{ route('admin.articles.publish', $article) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 bg-[#E50914] hover:bg-[#C8102E] text-white font-bold text-xs rounded-[4px] transition-colors cursor-pointer flex items-center justify-center gap-1.5" onclick="return confirm('Terbitkan artikel ini sekarang ke publik?');">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Terbitkan Sekarang</span>
                                </button>
                            </form>
                        @endcan

                        <!-- Schedule Publishing (Approved) -->
                        @can('schedule', $article)
                            <details class="group bg-[#F9FAFB] p-2.5 rounded-[4px] border border-[#E5E7EB]">
                                <summary class="text-xs font-bold text-[#171717] cursor-pointer flex items-center justify-between">
                                    <span>Jadwalkan Waktu Terbit</span>
                                    <span class="text-[10px] text-[#6B7280]">▼</span>
                                </summary>
                                <form action="{{ route('admin.articles.schedule', $article) }}" method="POST" class="mt-2 space-y-2">
                                    @csrf
                                    <input type="datetime-local" name="scheduled_at" required min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}" class="w-full text-xs border border-[#CCCCCC] rounded p-1.5 bg-white">
                                    <button type="submit" class="w-full py-1.5 bg-[#4B5563] hover:bg-[#171717] text-white text-xs font-bold rounded cursor-pointer">
                                        Tetapkan Jadwal
                                    </button>
                                </form>
                            </details>
                        @endcan

                        <!-- Unpublish (Published) -->
                        @can('unpublish', $article)
                            <form action="{{ route('admin.articles.unpublish', $article) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menarik artikel ini dari publikasi publik?');">
                                @csrf
                                <button type="submit" class="w-full py-1.5 px-3 bg-[#FFF8E1] hover:bg-[#FFE082] text-[#B78103] border border-[#FFE082] font-bold text-xs rounded-[4px] transition-colors cursor-pointer">
                                    Tarik Publikasi (Unpublish)
                                </button>
                            </form>
                        @endcan

                        <!-- Archive -->
                        @can('archive', $article)
                            @if ($article->status !== App\Enums\ArticleStatus::Archived)
                                <form action="{{ route('admin.articles.archive', $article) }}" method="POST" onsubmit="return confirm('Arsipkan naskah artikel ini?');">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 px-3 text-[#6B7280] hover:text-[#171717] hover:bg-[#F3F4F6] text-xs font-semibold rounded-[4px] transition-colors cursor-pointer">
                                        Arsipkan Artikel
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>
            </x-admin.card>

            <!-- Taxonomy: Category & Tags -->
            <x-admin.card title="Rubrik & Topik Berita">
                <div class="space-y-4 text-xs">
                    <div>
                        <label for="category_id" class="block font-bold text-[#171717] mb-1">
                            Rubrik / Kategori <span class="text-[#E50914]">*</span>
                        </label>
                        <select form="article-form" name="category_id" id="category_id" required class="w-full border border-[#CCCCCC] rounded-[4px] p-2 bg-white focus:outline-none focus:border-[#E50914]">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="content_type" class="block font-bold text-[#171717] mb-1">
                            Format Naskah <span class="text-[#E50914]">*</span>
                        </label>
                        <select form="article-form" name="content_type" id="content_type" required onchange="handleContentTypeChange(this.value)" class="w-full border border-[#CCCCCC] rounded-[4px] p-2 bg-white focus:outline-none focus:border-[#E50914]">
                            @foreach (App\Enums\ArticleType::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('content_type', $article->content_type->value) === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Specialized Video News Fields --}}
                    <div id="video-fields-wrapper" class="{{ old('content_type', $article->content_type->value) === 'video' ? '' : 'hidden' }} p-3 bg-red-50 rounded border border-red-200 space-y-2">
                        <label class="block font-bold text-red-900 text-xs uppercase tracking-wider">Kanal Video News</label>
                        <div>
                            <label for="video_url" class="block text-neutral-700 font-semibold mb-0.5 text-[11px]">Tautan / ID YouTube <span class="text-red-600">*</span></label>
                            <input form="article-form" type="text" name="video_url" id="video_url" value="{{ old('video_url', $article->video?->video_id ? 'https://www.youtube.com/watch?v='.$article->video->video_id : '') }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full text-xs border border-neutral-300 rounded p-1.5 focus:border-red-600">
                        </div>
                        <div>
                            <label for="duration_seconds" class="block text-neutral-700 font-semibold mb-0.5 text-[11px]">Durasi Video (Detik, Opsional)</label>
                            <input form="article-form" type="number" name="duration_seconds" id="duration_seconds" value="{{ old('duration_seconds', $article->video?->duration_seconds) }}" placeholder="contoh: 225" class="w-full text-xs border border-neutral-300 rounded p-1.5 focus:border-red-600">
                        </div>
                    </div>

                    {{-- Specialized Photo Story Gallery Selector --}}
                    <div id="photo-story-fields-wrapper" class="{{ old('content_type', $article->content_type->value) === 'photo_story' ? '' : 'hidden' }} p-3 bg-neutral-100 rounded border border-neutral-300 space-y-2">
                        <label class="block font-bold text-neutral-900 text-xs uppercase tracking-wider">Pilih Galeri Foto Cerita</label>
                        <div>
                            <select form="article-form" name="gallery_id" id="gallery_id" class="w-full text-xs border border-neutral-300 rounded p-1.5 focus:border-red-600 bg-white">
                                <option value="">— Pilih Galeri Terdaftar —</option>
                                @foreach($galleries as $gal)
                                    <option value="{{ $gal->id }}" {{ old('gallery_id', $article->photoStory?->gallery_id) == $gal->id ? 'selected' : '' }}>
                                        {{ $gal->title }} ({{ $gal->media()->count() }} foto)
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-neutral-500 block mt-1">Atau <a href="{{ route('admin.galleries.create') }}" target="_blank" class="text-red-600 font-semibold underline">buat galeri foto baru</a> di tab terpisah.</span>
                        </div>
                    </div>

                    @if ($authors->isNotEmpty() && Auth::user()->can('assignAuthor', Article::class))
                        <div>
                            <label for="author_id" class="block font-bold text-[#171717] mb-1">
                                Penugasan Penulis (Author)
                            </label>
                            <select form="article-form" name="author_id" id="author_id" class="w-full border border-[#CCCCCC] rounded-[4px] p-2 bg-white focus:outline-none focus:border-[#E50914]">
                                @foreach ($authors as $author)
                                    <option value="{{ $author->id }}" {{ old('author_id', $article->author_id) == $author->id ? 'selected' : '' }}>
                                        {{ $author->name }} ({{ $author->primary_role?->label ?? 'Staf' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block font-bold text-[#171717] mb-1">Tagar Berita Terkait</label>
                        <div class="max-h-36 overflow-y-auto border border-[#CCCCCC] rounded-[4px] p-2 space-y-1.5 bg-[#F9FAFB]">
                            @foreach ($tags as $tag)
                                <label class="flex items-center gap-2 text-xs cursor-pointer hover:text-[#E50914]">
                                    <input form="article-form" type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $selectedTags)) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]">
                                    <span>#{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <!-- Featured Image -->
            <x-admin.card title="Foto Utama (Featured Image)">
                <div class="space-y-3 text-xs">
                    {{-- Media Picker Integration --}}
                    <input form="article-form" type="hidden" name="featured_media_id" id="featured_media_id" value="{{ old('featured_media_id', $article->featured_media_id) }}">

                    <div id="media-preview-container" class="{{ ($article->featured_image_url || old('featured_media_id')) ? '' : 'hidden' }} mb-2">
                        <div class="relative aspect-16/9 bg-neutral-900 rounded overflow-hidden border border-neutral-300">
                            <img id="media-preview-img" src="{{ $article->featured_image_url }}" alt="Pratinjau Foto" class="w-full h-full object-cover">
                            <button type="button" onclick="removeSelectedMedia()" class="absolute top-1 right-1 p-1 bg-red-600 text-white rounded text-[10px] font-bold">
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openArticleMediaPicker()" class="flex-1 px-3 py-2 bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-semibold rounded transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Pilih dari Pustaka Media</span>
                        </button>
                    </div>

                    <div class="pt-2 border-t border-neutral-100">
                        <span class="text-[11px] text-neutral-400 block mb-1">Atau unggah langsung dari komputer:</span>
                        <input form="article-form" type="file" name="featured_image" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-[#6B7280] file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#171717] file:text-white hover:file:bg-[#E50914] cursor-pointer" />
                    </div>

                    <div>
                        <label for="featured_image_alt" class="block font-semibold text-[#6B7280] mb-1">Teks Alt</label>
                        <input form="article-form" type="text" name="featured_image_alt" id="featured_image_alt" value="{{ old('featured_image_alt', $article->featured_image_alt) }}" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2 focus:outline-none focus:border-[#E50914]" />
                    </div>

                    <div>
                        <label for="featured_image_caption" class="block font-semibold text-[#6B7280] mb-1">Keterangan & Kredit Foto</label>
                        <input form="article-form" type="text" name="featured_image_caption" id="featured_image_caption" value="{{ old('featured_image_caption', $article->featured_image_caption) }}" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2 focus:outline-none focus:border-[#E50914]" />
                    </div>
                </div>
            </x-admin.card>

            <!-- Editorial Flags -->
            <x-admin.card title="Opsi Redaksi & Sumber">
                <div class="space-y-3 text-xs">
                    <div class="space-y-2">
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasRole('editor') || Auth::user()->hasRole('editor_in_chief'))
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-[#171717]">
                                <input form="article-form" type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]">
                                <span>Jadikan Berita Utama (Headline)</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer font-bold text-[#E50914]">
                                <input form="article-form" type="checkbox" name="is_breaking" value="1" {{ old('is_breaking', $article->is_breaking) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]">
                                <span>Breaking News Ticker</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer font-bold text-[#171717]">
                                <input form="article-form" type="checkbox" name="is_editor_choice" value="1" {{ old('is_editor_choice', $article->is_editor_choice) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]">
                                <span>Pilihan Editor (Editor's Choice)</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer font-bold text-[#137333]">
                                <input form="article-form" type="checkbox" name="is_sponsored" value="1" {{ old('is_sponsored', $article->is_sponsored) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#137333] focus:ring-[#137333]">
                                <span>Konten Sponsor / Advertorial</span>
                            </label>

                            <div class="pt-2 border-t border-[#F3F4F6]">
                                <label for="homepage_priority" class="block font-bold text-[#171717] mb-1">
                                    Prioritas Beranda / Headline (0 - 100)
                                </label>
                                <input form="article-form" type="number" name="homepage_priority" id="homepage_priority" min="0" max="100" value="{{ old('homepage_priority', $article->homepage_priority ?? 0) }}" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2 focus:outline-none focus:border-[#E50914]">
                                <span class="text-[10px] text-[#6B7280] block mt-0.5">Nilai tinggi (misal: 100) menjadikan artikel kandidat utama Lead Story Beranda.</span>
                            </div>
                        @endif

                        <label class="flex items-center gap-2 cursor-pointer text-[#4B5563]">
                            <input form="article-form" type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', $article->allow_comments) ? 'checked' : '' }} class="rounded border-[#CCCCCC] text-[#E50914] focus:ring-[#E50914]">
                            <span>Izinkan Komentar Pembaca</span>
                        </label>
                    </div>

                    <div class="pt-2 border-t border-[#F3F4F6] space-y-2">
                        <div>
                            <label for="source_name" class="block font-semibold text-[#6B7280] mb-0.5">Nama Sumber Berita</label>
                            <input form="article-form" type="text" name="source_name" id="source_name" value="{{ old('source_name', $article->source_name) }}" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2 focus:outline-none focus:border-[#E50914]" />
                        </div>
                        <div>
                            <label for="source_url" class="block font-semibold text-[#6B7280] mb-0.5">URL Sumber Asli</label>
                            <input form="article-form" type="url" name="source_url" id="source_url" value="{{ old('source_url', $article->source_url) }}" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] p-2 focus:outline-none focus:border-[#E50914]" />
                        </div>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatDoc(cmd, value = null) {
        document.execCommand(cmd, false, value);
        syncContent();
    }

    function createLinkPrompt() {
        const url = prompt('Masukkan URL tautan (contoh: https://topnews.id):');
        if (url) {
            formatDoc('createLink', url);
        }
    }

    function syncContent() {
        const surface = document.getElementById('editor-surface');
        const hidden = document.getElementById('content-hidden');
        if (surface && hidden) {
            hidden.value = surface.innerHTML;
            
            const text = surface.innerText || '';
            const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
            document.getElementById('word-count').innerText = words + ' kata';
            const readingTime = Math.max(1, Math.ceil(words / 220));
            document.getElementById('reading-time-est').innerText = '~' + readingTime + ' menit estimasi baca';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const surface = document.getElementById('editor-surface');
        if (surface) {
            surface.addEventListener('input', syncContent);
            surface.addEventListener('blur', syncContent);
            syncContent();
        }

        const form = document.getElementById('article-form');
        if (form) {
            form.addEventListener('submit', () => {
                syncContent();
            });
        }
    });

    function handleContentTypeChange(type) {
        const videoWrapper = document.getElementById('video-fields-wrapper');
        const photoWrapper = document.getElementById('photo-story-fields-wrapper');

        if (videoWrapper) {
            if (type === 'video') videoWrapper.classList.remove('hidden');
            else videoWrapper.classList.add('hidden');
        }

        if (photoWrapper) {
            if (type === 'photo_story') photoWrapper.classList.remove('hidden');
            else photoWrapper.classList.add('hidden');
        }
    }

    function openArticleMediaPicker() {
        if (window.openMediaPicker) {
            window.openMediaPicker(function(selectedMedia) {
                document.getElementById('featured_media_id').value = selectedMedia.id;
                document.getElementById('media-preview-img').src = selectedMedia.thumbnail_url || selectedMedia.url;
                document.getElementById('media-preview-container').classList.remove('hidden');

                const altInput = document.getElementById('featured_image_alt');
                const captionInput = document.getElementById('featured_image_caption');

                if (altInput && !altInput.value && selectedMedia.alt_text) {
                    altInput.value = selectedMedia.alt_text;
                }
                if (captionInput && !captionInput.value && selectedMedia.caption) {
                    captionInput.value = selectedMedia.caption;
                }
            });
        }
    }

    function removeSelectedMedia() {
        document.getElementById('featured_media_id').value = '';
        document.getElementById('media-preview-container').classList.add('hidden');
    }
</script>
@endpush

@include('admin.media.partials.modal-picker')
@endsection
