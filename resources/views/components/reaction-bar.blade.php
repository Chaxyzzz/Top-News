@props(['article'])

@php
    $user = auth()->user();
    $currentReaction = $article->getUserReaction($user);
    $counts = $article->getReactionCounts();
@endphp

<div class="border-y border-neutral-200 py-6 my-8 bg-neutral-50 rounded-sm px-4 sm:px-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-headline font-bold text-sm uppercase tracking-wider text-neutral-900">
                Respon Pembaca
            </h3>
            <p class="text-xs text-neutral-500 mt-0.5">
                Bagaimana penilaian Anda terhadap kualitas dan faedah liputan ini?
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap" role="group" aria-label="Respon artikel">
            {{-- Useful / Bermanfaat --}}
            <form action="{{ route('news.reaction', $article) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="reaction" value="useful">
                <button
                    type="submit"
                    aria-pressed="{{ $currentReaction?->value === 'useful' ? 'true' : 'false' }}"
                    class="flex items-center gap-2 px-3.5 py-1.5 rounded-sm border text-xs font-semibold transition-colors cursor-pointer {{ $currentReaction?->value === 'useful' ? 'bg-red-600 border-red-600 text-white shadow-xs' : 'bg-white border-neutral-300 text-neutral-700 hover:border-red-600 hover:text-red-600' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Bermanfaat</span>
                    <span class="text-[11px] px-1.5 py-0.2 rounded-xs {{ $currentReaction?->value === 'useful' ? 'bg-white/20 text-white' : 'bg-neutral-100 text-neutral-600' }}">
                        {{ $counts['useful'] ?? 0 }}
                    </span>
                </button>
            </form>

            {{-- Interesting / Menarik --}}
            <form action="{{ route('news.reaction', $article) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="reaction" value="interesting">
                <button
                    type="submit"
                    aria-pressed="{{ $currentReaction?->value === 'interesting' ? 'true' : 'false' }}"
                    class="flex items-center gap-2 px-3.5 py-1.5 rounded-sm border text-xs font-semibold transition-colors cursor-pointer {{ $currentReaction?->value === 'interesting' ? 'bg-red-600 border-red-600 text-white shadow-xs' : 'bg-white border-neutral-300 text-neutral-700 hover:border-red-600 hover:text-red-600' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Menarik</span>
                    <span class="text-[11px] px-1.5 py-0.2 rounded-xs {{ $currentReaction?->value === 'interesting' ? 'bg-white/20 text-white' : 'bg-neutral-100 text-neutral-600' }}">
                        {{ $counts['interesting'] ?? 0 }}
                    </span>
                </button>
            </form>

            {{-- Important / Penting --}}
            <form action="{{ route('news.reaction', $article) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="reaction" value="important">
                <button
                    type="submit"
                    aria-pressed="{{ $currentReaction?->value === 'important' ? 'true' : 'false' }}"
                    class="flex items-center gap-2 px-3.5 py-1.5 rounded-sm border text-xs font-semibold transition-colors cursor-pointer {{ $currentReaction?->value === 'important' ? 'bg-red-600 border-red-600 text-white shadow-xs' : 'bg-white border-neutral-300 text-neutral-700 hover:border-red-600 hover:text-red-600' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Penting</span>
                    <span class="text-[11px] px-1.5 py-0.2 rounded-xs {{ $currentReaction?->value === 'important' ? 'bg-white/20 text-white' : 'bg-neutral-100 text-neutral-600' }}">
                        {{ $counts['important'] ?? 0 }}
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
