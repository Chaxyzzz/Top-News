@extends('layouts.admin')

@section('title', 'Moderasi Komentar Pembaca')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline font-black text-2xl text-[#111111] tracking-tight">
                Moderasi Komentar
            </h1>
            <p class="text-xs text-[#5F6368] mt-1">
                Tinjau dan kelola tanggapan pembaca guna menjaga etika dan peradaban ruang publik TopNews.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.engagement.index') }}" class="px-3.5 py-2 bg-white border border-[#CCCCCC] hover:border-[#111111] text-[#111111] font-bold text-xs rounded-[4px] transition-colors">
                Ringkasan Interaksi
            </a>
        </div>
    </div>

    <!-- Status Tabs & Search Filter -->
    <div class="bg-white p-4 rounded-[6px] border border-[#E8E8E8] shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Status Filter Tabs --}}
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-semibold pb-1 md:pb-0">
            <a href="{{ route('admin.comments.index', array_merge(request()->except(['status', 'page']))) }}" class="px-3 py-1.5 rounded-[4px] whitespace-nowrap transition-colors {{ !request()->filled('status') ? 'bg-[#111111] text-white' : 'text-[#5F6368] hover:bg-[#F3F4F6]' }}">
                Semua ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.comments.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" class="px-3 py-1.5 rounded-[4px] whitespace-nowrap transition-colors flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-amber-600 text-white' : 'text-amber-800 bg-amber-50 hover:bg-amber-100' }}">
                <span>Menunggu</span>
                <span class="px-1.5 py-0.2 text-[10px] rounded-full {{ request('status') === 'pending' ? 'bg-white/30 text-white' : 'bg-amber-200 text-amber-900' }}">
                    {{ $counts['pending'] }}
                </span>
            </a>
            <a href="{{ route('admin.comments.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" class="px-3 py-1.5 rounded-[4px] whitespace-nowrap transition-colors {{ request('status') === 'approved' ? 'bg-emerald-600 text-white' : 'text-emerald-800 bg-emerald-50 hover:bg-emerald-100' }}">
                Disetujui ({{ $counts['approved'] }})
            </a>
            <a href="{{ route('admin.comments.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" class="px-3 py-1.5 rounded-[4px] whitespace-nowrap transition-colors {{ request('status') === 'rejected' ? 'bg-rose-600 text-white' : 'text-rose-800 bg-rose-50 hover:bg-rose-100' }}">
                Ditolak ({{ $counts['rejected'] }})
            </a>
            <a href="{{ route('admin.comments.index', array_merge(request()->except('page'), ['status' => 'spam'])) }}" class="px-3 py-1.5 rounded-[4px] whitespace-nowrap transition-colors {{ request('status') === 'spam' ? 'bg-neutral-800 text-white' : 'text-neutral-600 bg-neutral-100 hover:bg-neutral-200' }}">
                Spam ({{ $counts['spam'] }})
            </a>
        </div>

        {{-- Search Input --}}
        <form action="{{ route('admin.comments.index') }}" method="GET" class="flex items-center gap-2">
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari isi komentar, pembaca..." 
                    class="text-xs bg-[#F9FAFB] border border-[#CCCCCC] rounded-[4px] pl-8 pr-3 py-1.5 focus:outline-none focus:border-[#E50914] w-48 sm:w-64"
                />
                <svg class="w-3.5 h-3.5 text-[#9CA3AF] absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="px-3 py-1.5 bg-[#111111] text-white text-xs font-bold rounded-[4px] hover:bg-black transition-colors cursor-pointer">
                Filter
            </button>
            @if(request()->filled('search') || request()->filled('status'))
                <a href="{{ route('admin.comments.index') }}" class="text-xs text-neutral-500 hover:text-red-600" title="Reset filter">✕</a>
            @endif
        </form>
    </div>

    <!-- Comments List -->
    <div class="bg-white rounded-[6px] border border-[#E8E8E8] shadow-xs overflow-hidden">
        @if ($comments->count() > 0)
            <div class="divide-y divide-[#E8E8E8]">
                @foreach ($comments as $comment)
                    <div class="p-4 sm:p-5 hover:bg-[#FAFAFA] transition-colors space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            {{-- Commenter & Article Attribution --}}
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#111111] text-white flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ $comment->user?->initials ?? 'U' }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-[#111111] truncate">
                                            {{ $comment->user?->name ?? 'Pengguna Anonim' }}
                                        </span>
                                        <span class="text-[11px] text-[#80868B]">
                                            ({{ $comment->user?->email }})
                                        </span>
                                        <span class="text-[10px] px-1.5 py-0.2 bg-neutral-100 text-neutral-600 rounded">
                                            {{ $comment->user?->account_type ?? 'reader' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-[#5F6368] truncate mt-0.5">
                                        Pada artikel: 
                                        @if ($comment->article)
                                            <a href="{{ route('news.show', $comment->article->slug) }}#comments" target="_blank" class="font-semibold text-[#111111] hover:text-[#E50914] underline">
                                                {{ $comment->article->title }}
                                            </a>
                                        @else
                                            <span class="italic text-neutral-400">Artikel telah dihapus</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Status Badge & Timestamp --}}
                            <div class="flex items-center gap-2 shrink-0">
                                @if ($comment->status === \App\Enums\CommentStatus::Pending)
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-900 font-bold text-[10px] uppercase tracking-wider rounded">
                                        Menunggu Moderasi
                                    </span>
                                @elseif ($comment->status === \App\Enums\CommentStatus::Approved)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold text-[10px] uppercase tracking-wider rounded">
                                        Disetujui
                                    </span>
                                @elseif ($comment->status === \App\Enums\CommentStatus::Rejected)
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold text-[10px] uppercase tracking-wider rounded">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-neutral-200 text-neutral-700 font-bold text-[10px] uppercase tracking-wider rounded">
                                        Spam
                                    </span>
                                @endif

                                <span class="text-[11px] text-[#80868B]">
                                    {{ $comment->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }} WIB
                                </span>
                            </div>
                        </div>

                        {{-- Parent Context if Reply --}}
                        @if ($comment->parent)
                            <div class="text-[11px] text-neutral-500 bg-neutral-50 p-2 rounded border-l-2 border-neutral-300">
                                Balasan untuk <strong>{{ $comment->parent->user?->name ?? 'Pembaca' }}</strong>:
                                <span class="italic">"{{ \Illuminate\Support\Str::limit($comment->parent->body, 90) }}"</span>
                            </div>
                        @endif

                        {{-- Comment Content --}}
                        <div class="text-xs text-[#202124] leading-relaxed bg-[#F8F9FA] p-3 rounded border border-[#E8E8E8] whitespace-pre-line">
                            {{ $comment->body }}
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-between pt-1 text-xs">
                            <div class="text-[11px] text-neutral-400">
                                @if ($comment->approved_at)
                                    Disetujui pada {{ $comment->approved_at->format('d/m/Y H:i') }}
                                @elseif ($comment->rejected_at)
                                    Ditolak pada {{ $comment->rejected_at->format('d/m/Y H:i') }}
                                    @if ($comment->spam_reason) — Alasan: {{ $comment->spam_reason }} @endif
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5">
                                {{-- Approve Action --}}
                                @if ($comment->status !== \App\Enums\CommentStatus::Approved)
                                    <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded transition-colors cursor-pointer">
                                            Setujui
                                        </button>
                                    </form>
                                @endif

                                {{-- Reject Action --}}
                                @if ($comment->status !== \App\Enums\CommentStatus::Rejected)
                                    <form action="{{ route('admin.comments.reject', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Tolak komentar ini?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-white border border-[#CCCCCC] hover:bg-rose-50 hover:border-rose-300 hover:text-rose-700 text-[#4B5563] font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                            Tolak
                                        </button>
                                    </form>
                                @endif

                                {{-- Mark Spam Action --}}
                                @if ($comment->status !== \App\Enums\CommentStatus::Spam)
                                    <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Tandai komentar sebagai spam?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-white border border-[#CCCCCC] hover:bg-neutral-100 text-[#5F6368] font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                            Spam
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete Action --}}
                                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline" data-confirm-delete data-delete-title="Hapus Komentar Permanen?" data-delete-name="Komentar oleh {{ $comment->author_name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white font-semibold text-[11px] rounded transition-colors cursor-pointer">
                                        Hapus Permanen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-[#E8E8E8]">
                {{ $comments->links() }}
            </div>
        @else
            <div class="p-12 text-center text-xs text-[#5F6368]">
                <svg class="w-10 h-10 text-[#CCCCCC] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="font-bold text-sm text-[#111111]">Tidak ada komentar pada filter ini</p>
                <p class="mt-1">Antrean komentar bersih atau belum ada komentar baru yang masuk.</p>
            </div>
        @endif
    </div>
</div>
@endsection
