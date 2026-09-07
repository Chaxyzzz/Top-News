@extends('layouts.admin')

@section('title', 'Kelola Artikel & Berita')

@section('content')
<div class="space-y-6">
    <x-admin.page-header 
        title="Daftar Artikel & Naskah Berita" 
        description="Kelola seluruh naskah jurnalistik, draf liputan, antrean review redaksi, dan publikasi berita TopNews."
    >
        <x-slot name="breadcrumbs">
            <x-admin.breadcrumb :items="[
                ['label' => 'Artikel Berita']
            ]" />
        </x-slot>

        <x-slot name="actions">
            @can('create', App\Models\Article::class)
                <x-button variant="primary" size="sm" :href="route('admin.articles.create')">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tulis Artikel Baru</span>
                </x-button>
            @endcan
        </x-slot>
    </x-admin.page-header>

    <!-- Status Tabs Bar -->
    <div class="border-b border-[#E5E7EB] flex items-center gap-2 overflow-x-auto pb-px text-xs font-bold font-headline select-none">
        <a href="{{ route('admin.articles.index', request()->except('status', 'page')) }}" 
           class="px-3.5 py-2.5 border-b-2 transition-colors whitespace-nowrap {{ !request('status') ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-[#6B7280] hover:text-[#171717]' }}">
            Semua ({{ array_sum($rawStatusCounts) }})
        </a>

        @foreach (App\Enums\ArticleStatus::cases() as $st)
            @php $count = $rawStatusCounts[$st->value] ?? 0; @endphp
            <a href="{{ route('admin.articles.index', array_merge(request()->except('page'), ['status' => $st->value])) }}" 
               class="px-3.5 py-2.5 border-b-2 transition-colors whitespace-nowrap flex items-center gap-1.5 {{ request('status') === $st->value ? 'border-[#E50914] text-[#E50914]' : 'border-transparent text-[#6B7280] hover:text-[#171717]' }}">
                <span>{{ $st->label() }}</span>
                @if ($count > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === $st->value ? 'bg-[#FFF1F2] text-[#E50914]' : 'bg-[#F3F4F6] text-[#6B7280]' }}">
                        {{ $count }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-4 rounded-[6px] border border-[#E5E7EB] shadow-subtle">
        <form action="{{ route('admin.articles.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="sm:col-span-4">
                <input 
                    type="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari judul, subjudul, atau kutipan..." 
                    class="w-full text-xs border border-[#CCCCCC] rounded-[4px] px-3.5 py-2 focus:outline-none focus:border-[#E50914]"
                />
            </div>

            <div class="sm:col-span-3">
                <select name="category_id" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:outline-none focus:border-[#E50914] bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <select name="type" class="w-full text-xs border border-[#CCCCCC] rounded-[4px] px-3 py-2 focus:outline-none focus:border-[#E50914] bg-white">
                    <option value="">Semua Format</option>
                    @foreach (App\Enums\ArticleType::cases() as $t)
                        <option value="{{ $t->value }}" {{ request('type') === $t->value ? 'selected' : '' }}>
                            {{ $t->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <x-button type="submit" variant="secondary" size="sm" class="w-full">
                    Filter
                </x-button>
                @if (request()->hasAny(['search', 'category_id', 'type', 'author_id']))
                    <a href="{{ route('admin.articles.index', request()->only('status')) }}" class="px-2.5 py-1.5 text-xs text-[#6B7280] hover:text-[#171717] bg-[#F3F4F6] rounded flex items-center justify-center" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Articles Table List -->
    <div class="bg-white rounded-[8px] border border-[#E5E7EB] shadow-subtle overflow-hidden">
        @if ($articles->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#6B7280] uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3 px-4">Judul & Format Naskah</th>
                            <th class="py-3 px-4">Rubrik</th>
                            <th class="py-3 px-4">Penulis / Editor</th>
                            <th class="py-3 px-4">Status Redaksi</th>
                            <th class="py-3 px-4">Waktu Terbit / Diperbarui</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F3F4F6]">
                        @foreach ($articles as $article)
                            <tr class="hover:bg-[#F9FAFB] transition-colors">
                                <!-- Title & Format -->
                                <td class="py-3.5 px-4 max-w-md">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#F3F4F6] text-[#4B5563]">
                                                {{ $article->content_type->label() }}
                                            </span>
                                            @if ($article->is_breaking)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#FFF1F2] text-[#E50914] border border-[#FECDD3]">
                                                    Breaking
                                                </span>
                                            @endif
                                            @if ($article->is_featured)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#FFF8E1] text-[#B78103]">
                                                    Headline
                                                </span>
                                            @endif
                                            @if ($article->is_sponsored)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#E6F4EA] text-[#137333]">
                                                    Advertorial
                                                </span>
                                            @endif
                                        </div>

                                        <a href="{{ route('admin.articles.show', $article) }}" class="font-headline font-bold text-sm text-[#171717] hover:text-[#E50914] transition-colors block line-clamp-2 leading-snug">
                                            {{ $article->title }}
                                        </a>

                                        @if ($article->subtitle)
                                            <p class="text-[11px] text-[#6B7280] line-clamp-1">
                                                {{ $article->subtitle }}
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-bold text-[11px] px-2 py-0.5 rounded" style="color: {{ $article->category->accent_color ?? '#171717' }}; background-color: {{ $article->category->accent_color ?? '#171717' }}15;">
                                        {{ $article->category->name }}
                                    </span>
                                </td>

                                <!-- Author & Editor -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-semibold text-[#171717] block">{{ $article->author?->name ?? '-' }}</span>
                                    @if ($article->editor)
                                        <span class="text-[10px] text-[#6B7280] block">Ed: {{ $article->editor->name }}</span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <x-admin.badge :variant="$article->status->badgeVariant()" size="xs">
                                        {{ $article->status->label() }}
                                    </x-admin.badge>
                                </td>

                                <!-- Dates -->
                                <td class="py-3.5 px-4 text-[#6B7280] whitespace-nowrap">
                                    @if ($article->status === App\Enums\ArticleStatus::Published && $article->published_at)
                                        <span class="font-semibold text-[#137333] block">Terbit: {{ $article->published_at->format('d/m/Y H:i') }}</span>
                                    @elseif ($article->status === App\Enums\ArticleStatus::Scheduled && $article->scheduled_at)
                                        <span class="font-semibold text-[#E50914] block">Jadwal: {{ $article->scheduled_at->format('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="block">Edit: {{ $article->updated_at->diffForHumans() }}</span>
                                    @endif
                                    <span class="text-[10px] text-[#9CA3AF]">Estimasi: {{ $article->reading_time }} mnt baca</span>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Review / Timeline Action -->
                                        <a href="{{ route('admin.articles.show', $article) }}" class="p-1.5 text-[#4B5563] hover:text-[#171717] hover:bg-[#F3F4F6] rounded" title="Lihat Naskah & Alur Redaksi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Edit Action (Policy Checked) -->
                                        @can('update', $article)
                                            <a href="{{ route('admin.articles.edit', $article) }}" class="p-1.5 text-[#4B5563] hover:text-[#E50914] hover:bg-[#FFF1F2] rounded" title="Sunting Naskah">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        <!-- Delete Action (Permanent Delete) -->
                                        @can('delete', $article)
                                            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline" data-confirm-delete data-delete-title="Hapus Artikel Permanen?" data-delete-name="{{ $article->title }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-[#6B7280] hover:text-[#C8102E] hover:bg-[#FDE8E9] rounded cursor-pointer" title="Hapus Artikel Permanen">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[#E5E7EB]">
                {{ $articles->links() }}
            </div>
        @else
            <x-admin.empty-state 
                title="Tidak Ada Artikel Ditemukan" 
                description="Belum ada artikel yang sesuai dengan filter atau kata kunci pencarian yang dipilih."
                actionLabel="+ Tulis Artikel Baru"
                :actionUrl="route('admin.articles.create')"
            />
        @endif
    </div>
</div>
@endsection
