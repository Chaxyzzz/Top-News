@props([
    'author' => null,
])

@if ($author)
    <div class="p-6 bg-[#F9FAFB] rounded-[8px] border border-[#E5E7EB] flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <a href="{{ route('author.show', $author->username ?? 'redaksi') }}" class="shrink-0 group">
            @if ($author->avatar_url)
                <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-subtle group-hover:opacity-90 transition-opacity">
            @else
                <div class="w-16 h-16 rounded-full bg-[#111111] text-white flex items-center justify-center font-bold text-lg border-2 border-white shadow-subtle group-hover:bg-[#E50914] transition-colors">
                    {{ $author->initials ?? 'TN' }}
                </div>
            @endif
        </a>

        <div class="space-y-1.5 flex-1">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#E50914] block">
                        Tentang Penulis
                    </span>
                    <h3 class="font-headline font-bold text-base text-[#111111]">
                        <a href="{{ route('author.show', $author->username ?? 'redaksi') }}" class="hover:text-[#E50914] transition-colors">
                            {{ $author->name }}
                        </a>
                    </h3>
                </div>

                <a href="{{ route('author.show', $author->username ?? 'redaksi') }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#E50914] hover:underline">
                    <span>Lihat Semua Artikel</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <p class="text-xs sm:text-sm text-[#4B5563] leading-relaxed">
                {{ $author->bio ?? 'Jurnalis dan kontributor editorial TopNews yang berfokus pada liputan mendalam, verifikasi fakta independen, dan standar jurnalisme presisi.' }}
            </p>
        </div>
    </div>
@endif
