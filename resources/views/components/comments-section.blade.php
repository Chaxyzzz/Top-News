@props(['article'])

@php
    $comments = $article->approvedComments;
    $totalApproved = $comments->count() + $comments->sum(fn($c) => $c->approvedReplies->count());
@endphp

<section id="comments" class="my-12 pt-8 border-t-2 border-neutral-900 scroll-mt-20">
    <div class="flex items-center justify-between pb-4 mb-6 border-b border-neutral-200">
        <div class="flex items-center gap-3">
            <h2 class="font-headline font-bold text-xl text-neutral-900">
                Diskusi Pembaca
            </h2>
            <span class="text-xs font-bold px-2.5 py-0.5 bg-neutral-900 text-white rounded-full">
                {{ $totalApproved }}
            </span>
        </div>

        <span class="text-xs text-neutral-500 hidden sm:inline">
            Standar Komentar Beradab TopNews
        </span>
    </div>

    {{-- Editorial Moderation Notice --}}
    <div class="p-3.5 bg-neutral-50 border border-neutral-200 rounded-sm text-xs text-neutral-600 mb-6 flex items-start gap-2.5">
        <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="leading-relaxed">
            Semua komentar akan melewati <strong>proses moderasi redaksi</strong> sebelum ditayangkan guna menjaga kualitas diskusi publik yang sehat, bebas ujaran kebencian, dan beretika.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs rounded-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-300 text-rose-800 text-xs rounded-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($article->allow_comments)
        {{-- Submission Form for Authenticated Users --}}
        @auth
            <div class="bg-neutral-50 border border-neutral-200 rounded-sm p-4 sm:p-5 mb-8 shadow-xs">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-full bg-neutral-900 text-white flex items-center justify-center text-xs font-bold">
                        {{ auth()->user()->initials }}
                    </div>
                    <span class="text-xs font-semibold text-neutral-800">
                        Berkomentar sebagai <strong>{{ auth()->user()->name }}</strong>
                    </span>
                </div>

                <form action="{{ route('news.comments.store', $article) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <textarea
                            name="body"
                            rows="3"
                            required
                            minlength="2"
                            maxlength="2000"
                            placeholder="Tulis tanggapan atau analisis berimbang Anda di sini (maks. 2000 karakter)..."
                            class="w-full text-xs sm:text-sm bg-white border border-neutral-300 rounded-sm p-3 focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600"
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[11px] text-neutral-400">
                            Teks murni saja. Hindari tautan promosi atau spam.
                        </span>
                        <button
                            type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-sm transition-colors cursor-pointer shadow-xs"
                        >
                            Kirim Komentar
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Guest Login Prompt --}}
            <div class="bg-neutral-50 border border-neutral-200 rounded-sm p-6 text-center mb-8">
                <h3 class="font-headline font-bold text-sm text-neutral-900 mb-1">
                    Ingin Bergabung dalam Diskusi?
                </h3>
                <p class="text-xs text-neutral-500 mb-4 max-w-md mx-auto">
                    Hanya pembaca terdaftar yang dapat menuliskan komentar dan tanggapan.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-neutral-900 hover:bg-neutral-800 text-white font-semibold text-xs rounded-sm transition-colors">
                        Masuk Akun
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-white border border-neutral-300 hover:border-red-600 hover:text-red-600 text-neutral-800 font-semibold text-xs rounded-sm transition-colors">
                        Daftar Pembaca Baru
                    </a>
                </div>
            </div>
        @endauth
    @else
        <div class="bg-neutral-100 border border-neutral-200 rounded-sm p-4 text-center text-xs text-neutral-500 mb-8">
            Kolom komentar telah ditutup atau dinonaktifkan oleh redaksi untuk artikel ini.
        </div>
    @endif

    {{-- Threaded Comment List --}}
    @if ($comments->count() > 0)
        <div class="space-y-6">
            @foreach ($comments as $comment)
                <div class="border-b border-neutral-200 pb-6 last:border-b-0 space-y-3">
                    {{-- Top Comment Header --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-neutral-200 text-neutral-700 flex items-center justify-center text-xs font-bold">
                                {{ $comment->user?->initials ?? 'U' }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-neutral-900 leading-tight">
                                    {{ $comment->user?->name ?? 'Pembaca' }}
                                </p>
                                <span class="text-[11px] text-neutral-400">
                                    {{ $comment->created_at->locale('id')->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Action for comment owner --}}
                        @auth
                            @if ($comment->user_id === auth()->id())
                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Hapus komentar Anda?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] text-neutral-400 hover:text-red-600 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>

                    {{-- Body --}}
                    <div class="text-xs sm:text-sm text-neutral-800 leading-relaxed pl-10 whitespace-pre-line">
                        {{ $comment->body }}
                    </div>

                    {{-- Reply Trigger & Inline Form --}}
                    @auth
                        @if ($article->allow_comments)
                            <div class="pl-10" x-data="{ openReply: false }">
                                <button
                                    type="button"
                                    @click="openReply = !openReply"
                                    class="text-xs font-semibold text-neutral-500 hover:text-red-600 transition-colors flex items-center gap-1 cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    <span x-text="openReply ? 'Batal Balas' : 'Balas'"></span>
                                </button>

                                <div x-show="openReply" x-cloak class="mt-3 bg-neutral-50 border border-neutral-200 rounded-sm p-3">
                                    <form action="{{ route('news.comments.store', $article) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <textarea
                                            name="body"
                                            rows="2"
                                            required
                                            minlength="2"
                                            maxlength="2000"
                                            placeholder="Tulis balasan untuk {{ $comment->user?->name }}..."
                                            class="w-full text-xs bg-white border border-neutral-300 rounded-sm p-2.5 focus:outline-none focus:border-red-600"
                                        ></textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="openReply = false" class="px-3 py-1 bg-neutral-200 text-neutral-700 text-xs rounded-sm hover:bg-neutral-300">
                                                Batal
                                            </button>
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-sm hover:bg-red-700">
                                                Kirim Balasan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endauth

                    {{-- Approved Replies (Max 1 Level) --}}
                    @if ($comment->approvedReplies->count() > 0)
                        <div class="mt-4 pl-10 space-y-3 border-l-2 border-neutral-200 ml-4">
                            @foreach ($comment->approvedReplies as $reply)
                                <div class="bg-neutral-50 rounded-sm p-3.5 space-y-2 border border-neutral-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-neutral-300 text-neutral-700 flex items-center justify-center text-[10px] font-bold">
                                                {{ $reply->user?->initials ?? 'U' }}
                                            </div>
                                            <span class="text-xs font-bold text-neutral-900">
                                                {{ $reply->user?->name ?? 'Pembaca' }}
                                            </span>
                                            <span class="text-[10px] text-neutral-400">
                                                {{ $reply->created_at->locale('id')->diffForHumans() }}
                                            </span>
                                        </div>

                                        @auth
                                            @if ($reply->user_id === auth()->id())
                                                <form action="{{ route('comments.destroy', $reply) }}" method="POST" onsubmit="return confirm('Hapus balasan Anda?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[10px] text-neutral-400 hover:text-red-600 transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                    <p class="text-xs text-neutral-700 leading-relaxed whitespace-pre-line">
                                        {{ $reply->body }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="p-8 text-center bg-neutral-50 border border-neutral-100 rounded-sm">
            <p class="text-xs text-neutral-500">
                Belum ada komentar yang dipublikasikan pada artikel ini. Jadilah yang pertama memberikan pandangan berbobot Anda.
            </p>
        </div>
    @endif
</section>
