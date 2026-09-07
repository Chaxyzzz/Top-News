@extends('account.layout')

@section('title', 'Komentar Saya — Ruang Pembaca TopNews')

@section('account_content')
<div class="bg-white border border-neutral-200 rounded-sm p-6 shadow-xs">
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-neutral-200">
        <div>
            <h2 class="font-headline font-bold text-lg text-neutral-900">Jejak Komentar Saya</h2>
            <p class="text-xs text-neutral-500 mt-0.5">Semua komentar yang pernah Anda kirimkan pada artikel TopNews.</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 bg-neutral-100 text-neutral-700 rounded-sm">
            Total: {{ $comments->total() }}
        </span>
    </div>

    @if ($comments->count() > 0)
        <div class="divide-y divide-neutral-200">
            @foreach ($comments as $comment)
                <div class="py-4 first:pt-0 last:pb-0 space-y-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            {{-- Status Badge --}}
                            @if ($comment->status === \App\Enums\CommentStatus::Approved)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider rounded-xs">
                                    Disetujui
                                </span>
                            @elseif ($comment->status === \App\Enums\CommentStatus::Pending)
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wider rounded-xs">
                                    Menunggu Moderasi
                                </span>
                            @elseif ($comment->status === \App\Enums\CommentStatus::Rejected)
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold uppercase tracking-wider rounded-xs">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-neutral-200 text-neutral-700 text-[10px] font-bold uppercase tracking-wider rounded-xs">
                                    Spam
                                </span>
                            @endif

                            <span class="text-[11px] text-neutral-400">
                                {{ $comment->created_at->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB
                            </span>
                        </div>

                        {{-- Delete Comment Form --}}
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-neutral-400 hover:text-red-600 transition-colors font-medium cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>

                    {{-- Article Title Link --}}
                    @if ($comment->article)
                        <p class="text-xs text-neutral-500">
                            Pada artikel: 
                            <a href="{{ route('news.show', $comment->article->slug) }}#comments" class="font-semibold text-neutral-900 hover:text-red-600 transition-colors">
                                "{{ $comment->article->title }}"
                            </a>
                        </p>
                    @endif

                    {{-- Comment Body --}}
                    <div class="bg-neutral-50 p-3 rounded-sm text-xs text-neutral-700 leading-relaxed border-l-2 border-neutral-300">
                        {{ $comment->body }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-6 border-t border-neutral-200 mt-6">
            {{ $comments->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-neutral-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <h3 class="font-headline font-bold text-sm text-neutral-900 mb-1">Belum Ada Komentar</h3>
            <p class="text-xs text-neutral-500 max-w-sm mx-auto">
                Komentar berbobot Anda membantu memperkaya ruang publik. Ikuti diskusi pada artikel yang Anda baca.
            </p>
        </div>
    @endif
</div>
@endsection
